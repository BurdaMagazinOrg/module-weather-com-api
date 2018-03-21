<?php

/**
 * @file
 * Contains \Drupal\weather_com_api\WundergroundAutocompleteAPi.
 */

namespace Drupal\weather_com_api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class WundergroundAutocompleteAPi.
 *
 * @package Drupal\weather_com_api
 */
class WundergroundAutocompleteAPi {
  private $client;
  private $wundergrounddb;
  /**
   * Constructor.
   */
  public function __construct(Client $client) {
    $this->client = $client;
    $this->wundergrounddb = new WundergroundAutoCompleteDatabase(\Drupal::database());
  }

  /**
   * Request data from the wunderground autocomplete api.
   *
   * For a complete overview of options:
   * @see https://www.wunderground.com/weather/api/d/docs?d=autocomplete-api
   * @param $search
   *  the search string
   * @param $options
   *  options to pass as additional query parameters
   * @return JsonResponse
   */
  public function requestData ($search, $options) {
    $search_query = 'aq?query=' . $search;
    if (!empty($options)) {
      foreach ($options as $parameter_key => $parameter_value) {
        $search_query .= '&' . $parameter_key . '=' . $parameter_value;
      }
    }

    $results = [];

    try {
      $response = $this->client->request('GET', $search_query);
      $data = json_decode($response->getBody());
      // Extract key and value from the returned array.
      if (!empty($data->RESULTS)) {
        foreach ($data->RESULTS as $result) {
          // Remove country names when returning the results.
          // @ToDO may figure out a better way to do this.
          $city_name = substr($result->name, 0, strpos($result->name, ','));
          if (!empty($city_name)) {
            $results[] = ['value' => $city_name];
          }
        }
        $this->wundergrounddb->saveAutocompleteResults($data->RESULTS);
      }
    }
    catch (GuzzleException $exception) {
      \Drupal::logger('weather_com_api')->error('Failed to receive data from Weather.com API at class WundergroundAutocompleteAPi. Exception was: ' . $exception->getMessage());
    }

    return new JsonResponse($results);
  }

}
