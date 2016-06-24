<?php
/**
 * @file
 *  Ajax render callback for weather widget.
 */

namespace Drupal\weather_com_api\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\weather_com_api\WeatherComApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\weather_com_api\Controller\ReplaceWidgetController;

class ReplaceWeatherWidget  implements CommandInterface {

  private $user_location;
  private $user_location_coordinates;
  private $weather_options;
  public function __construct($weather_options, $user_location, $user_location_coordinates) {
    $this->user_location = $user_location;
    $this->user_location_coordinates = $user_location_coordinates;
    $this->weather_options = $weather_options;
  }
  
  public function render() {
    $client = new Client(['base_uri' => WEATHER_API_URL]);
    $config = \Drupal::config('weather_com_api.settings');
    $weather_client = new WeatherComApiClient($client, $config);
    $location = new \stdClass();
    $location->city = $this->user_location;
    $location->coordinates = $this->user_location_coordinates;
    $weather = new \stdClass();
    if ($this->weather_options['current'])  {
      $weather->current = $weather_client->getCurrentWeather($location);
      $weather->city = $weather->current->city;
    }
    if ($this->weather_options['data_feature'] == 'forecast') {
      $weather->forecast = $weather_client->getForecast($location, $this->weather_options['forecast']);
      $weather->city = $weather->forecast->city;
    }
    if (empty($weather->current) && $this->weather_options['data_feature'] == "observations" #
      && $this->weather_options['observations']['observation_feature'] == "current") {
      $weather->current = $weather_client->getCurrentWeather($location);
      $weather->city = $weather->current->city;
    }

    $weather_ajax_controller = new ReplaceWidgetController();
    $weather_html = $weather_ajax_controller->replaceWidget($weather);
    $response_data = array(
      'command' => 'replaceWeatherWidget',
      'selector' => '.weather-details',
      'data' => $weather_html,
      'location' => $location,
    );
    return $response_data;
  }

}