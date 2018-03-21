<?php

/**
 * @file
 * Contains \Drupal\weather_com_api\WidgetHandler.
 */

namespace Drupal\weather_com_api;

use Drupal\weather_com_api\WeatherComApiClient;
use GuzzleHttp\Client;

/**
 * Class WidgetHandler.
 *
 * This class takes care of handling user input from the weather widget.
 *
 * @package Drupal\weather_com_api
 */
class WidgetHandler {
  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * Get all information from the widget form, block configuration and user.
   *
   * @param $form
   * @param $form_state
   * @return array
   *
   */
  public function gatherInformation($form, &$form_state) {
    $form_values = $form_state->getValues();
    $block_configuration = $block_configuration = $form_state->getBuildInfo()['args'][0];;
    // Check for a stored city.
    $cookie_location = !empty($_COOKIE['weather_location']) ? $_COOKIE['weather_location'] : $block_configuration['location'];
    $form_city = $form_values['city'];
    $coordinates_text = explode(':', $form_values['coordinates']);

    $location_information = [
      'block_location' => $block_configuration['location'],
      'cookie_location' => $cookie_location,
      'form_location' => $form_city,
      'form_coordinates' => $coordinates_text,
    ];
    return $location_information;
  }

  /**
   * Based on the widget information, figure out which city to use for the
   * weather lookup.
   * @param $location_information
   * @return bool|string
   */
  public function decideLocation($location_information) {
    // If the form is empty, we either only have the coordinates from the
    // geolookup or already have a cookie if the user has visited the site prior.
    // If the coordinates are empty as well, the geolookup takes more time.
    // If the coordinates are "DEFAULT", we use the default location of the block.

    // If form city name is empty, the user usually loads the weather widget for
    // the first time.
    if (empty($location_information['form_location'])) {
      // We might know the users location already provided by the browser, but
      // we do not know for sure.
      if (empty($location_information['form_coordinates'][0])) {
        // If we either do not know the city or the coordinates, we check the
        // users cookie for a location. If this fails, we fallback to the
        // block default.
        if (empty($location_information['cookie_location'])) {
          return $location_information['block_location'];
        }
        // Cookie location is known, use this city.
        else {
          return $location_information['cookie_location'];
        }
      }
      // Coordinates are present, city is not.
      else {
        // If a city is stored in the cookie, use this city.
        if (!empty($location_information['cookie_location'])) {
          return $location_information['cookie_location'];
        }
        // Either City in Form or cookie are present, lookup the city from
        // the user coordinates.
        else {
          $client = new Client(['base_uri' => WEATHER_API_URL]);
          $config = \Drupal::config('weather_com_api.settings');
          $weather_client = new WeatherComApiClient($client, $config);
          $city_name = $weather_client->getCityByCoordinates($location_information['form_coordinates']);
          return ['city' => $city_name, 'coordinates' => $location_information['form_coordinates']];
        }
      }
    }
    // The city is known from the form.
    else {
      return $location_information['form_location'];
    }
  }
}
