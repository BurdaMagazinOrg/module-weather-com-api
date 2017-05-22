<?php
/**
 * @file
 *  Ajax submit callback for weather widget form.
 */

namespace Drupal\weather_com_api\FormElement;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Render\Element\Ajax;
use Drupal\weather_com_api\Ajax\ReplaceWeatherWidget;
use Drupal\weather_com_api\WundergroundAutoCompleteDatabase;
use Drupal\weather_com_api\WundergroundAutocompleteAPi;
use Drupal\weather_com_api\WidgetHandler;
use Drupal\weather_com_api\Controller\ReplaceWidgetController;
use GuzzleHttp\Client;
use Drupal\weather_com_api\WeatherComApiClient;

class WeatherComApiWidgetSubmit {

  /**
   * Handle form input and replace the weather widget html with weather.
   *
   * @param $form
   * @param $form_state
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public static function sendAjax($form, &$form_state) {
    $widget_handler = new WidgetHandler();
    // Gather information from the form, the block configuration and cookie.
    $location_information = $widget_handler->gatherInformation($form, $form_state);
    // Decide which location information to use.
    $decide_location = $widget_handler->decideLocation($location_information);

    // Decide location might be an array containing coordinates already from
    // Weather.com when querying the city based on user coordinates.
    if (is_array($decide_location)) {
      $widget_location = $decide_location['city'];
      $coordinates = $decide_location['coordinates'];
    }
    else {
      $widget_location = $decide_location;
      $autocomplete_database = new WundergroundAutoCompleteDatabase(\Drupal::database());
      $city_result = $autocomplete_database->getCityResult($widget_location);
      if (is_array($city_result)) {
        $city_object = array_pop($city_result);
        if (!empty($city_object->latitude)) {
          $coordinates = [$city_object->latitude, $city_object->longitude];
        }
      }
    }
    // If coordinates are empty, a city might not have been queried in the
    // Autocomplete Database. We can query it here.
    if (empty($coordinates)) {
      $client = new Client(['base_uri' => WUNDERGROUND_AUTOCOMPLETE_API_URL]);
      $autocomplete_client = new WundergroundAutocompleteAPi($client);
      $autocomplete_client->requestData($decide_location,  ['c' => 'DE']);
      // Lookup coordinates by city name.
      if (empty($city_result)) {
        $autocomplete_database = new WundergroundAutoCompleteDatabase(\Drupal::database());
        $city_result = $autocomplete_database->getCityResult($widget_location);
        if (is_array($city_result)) {
          $city_object = array_pop($city_result);
          if (!empty($city_object->latitude)) {
            $coordinates = [$city_object->latitude, $city_object->longitude];
          }
        }
      }
    }
    // Build the Ajax Response.
    $response = new AjaxResponse();
    setcookie('weather_location', $widget_location, strtotime("+1 days"), '/');
    $response->addCommand(new ReplaceWeatherWidget($form_state->getBuildInfo()['args'][0], $widget_location, $coordinates));
    return $response;
  }
}