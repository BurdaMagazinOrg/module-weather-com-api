<?php

/**
 * @file
 * Contains \Drupal\weather_com_api\WundergroundWeatherApiClient.
 */

namespace Drupal\weather_com_api;
use Drupal\Core\Config\Config;
use GuzzleHttp\Client;
use Drupal\weather_com_api\WeatherComApiIntl;




/**
 * Class WundergroundWeatherApiClient.
 *
 * @package Drupal\weather_com_api
 */
class WeatherComApiClient {

  private $api_key;
  private $language;
  private $client;
  private $last_response_data;
  private $output_format;

  /**
   * Constructor.
   */
  public function __construct(Client $client, $config) {
    $this->setApiKey($config->get('api_key'));
    $this->setLanguage($config->get('language'));
    $this->client = $client;
    $this->output_format = "json";
  }

  /**
   * Request data from weather.com api.
   * @param array $options
   *  An array of options which will be used to create the request url.
   * @param string $query_parameters
   *  A string which will be added the the request url.
   * @return response object
   */
  public function requestData($options, $query_parameters) {
    // Build request url using the options.
    $url = '';
    foreach ($options as $option) {
      $url .= '/' . $option;
    }
    if ($options['api_version'] == "v1") {
      $url .= '.' . $this->output_format;
    }

    if (!empty($query_parameters)) {
      $url .= '?' . $query_parameters;
    }
    // Request data from the api.
    $data = $this->client->request('GET', $url);
    $response = json_decode($data->getBody());
    $this->setLastResponseData($response);
    return $response;
  }

  public function getForecast($location, $forecast_configuration) {
    // Check if there is weather already in cache.
    $cached_weather = \Drupal::cache('weather_com_api')->get($location->city);
    if (!empty($cached_weather->data)) {
      return $cached_weather->data;
    }
    $options = [
      'api_version' => 'v1',
      'geocode' => 'geocode',
      'latitude' => $location->coordinates[0],
      'longitude' =>  $location->coordinates[1],
      'data_feature' => 'forecast',
      'day_feature' => $forecast_configuration['day_feature'],
      'days' => $forecast_configuration['days'],
    ];
    $query_parameters = 'language=' . $this->language['language_code'] . '&units=' . $this->language['measure'] . '&apiKey=' . $this->api_key;
    $response = $this->requestData($options, $query_parameters);
    $response->city = $location->city;
    $cache = \Drupal::cache('weather_com_api')->set($location->city, $response, time() + 3600);
    return $response;
  }

  public function getCurrentWeather($location) {
    // Check if there is weather already in cache.
    $cached_weather = \Drupal::cache('weather_com_api')->get('current_' . $location->city);
    if (!empty($cached_weather->data)) {
      return $cached_weather->data;
    }
    $options = [
      'api_version' => 'v1',
      'geocode' => 'geocode',
      'latitude' => $location->coordinates[0],
      'longitude' =>  $location->coordinates[1],
      'data_feature' => 'observations',
      'day_feature' => 'current',
    ];
    $query_parameters = 'language=' . $this->language['language_code'] . '&units=' . $this->language['measure'] . '&apiKey=' . $this->api_key;
    $response = $this->requestData($options, $query_parameters);
    $response->city = $location->city;
    // Cache for 10 minutes.
    $cache = \Drupal::cache('weather_com_api')->set('current_' . $location->city, $response, time() + 600);
    return $response;
  }

  /**
   * Query the location services api and get a city matching geocode coordinates.
   * @param array $coordinates
   * @return string
   */
  public function getCityByCoordinates($coordinates) {
    if (empty($coordinates[0]) || empty($coordinates[1])) {
      return FALSE;
    }
    $options = [
      'api_version' => 'v3',
      'location' => 'location',
      'point' => 'point',
    ];
    $query_parameters = "geocode=" . $coordinates[0] . ',' . $coordinates[1] . '&language=' . $this->language['language_code'] . '&format=json&apiKey=' . $this->api_key;
    $response = $this->requestData($options, $query_parameters);
    return $response->location->city;
  }

  public function getCoordinatesByCity($city) {
    $options = [
      'api_version' => 'v3',
      'location' => 'location',
      'search' => 'search',
    ];
    $query_parameters = "query=" . $city . '&locationType=city&language=' . $this->language['language_code'] . '&format=json&apiKey=' . $this->api_key;
    $response = $this->requestData($options, $query_parameters);
    $coordinates = [$response->location->latitude, $response->location->longitude];
    return $coordinates;
  }

  /**
   * @return mixed
   */
  public function getApiKey() {
    return $this->api_key;
  }

  /**
   * @param mixed $api_key
   */
  public function setApiKey($api_key) {
    $this->api_key = $api_key;
  }

  /**
   * @return mixed
   */
  public function getLanguage() {
    return $this->language;
  }

  /**
   * @param mixed $language
   */
  public function setLanguage($language) {
    $languages = WeatherComApiIntl::getLanguages();
    $this->language = $languages[$language];
  }

  /**
   * @return mixed
   */
  public function getLastResponseData() {
    return $this->last_response_data;
  }

  /**
   * @param mixed $last_response_data
   */
  public function setLastResponseData($last_response_data) {
    $this->last_response_data = $last_response_data;
  }


}
