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
  public function sendAjax($form, &$form_state) {
    $widget_handler = new WidgetHandler();
    // Gather information from the form, the block configuration and cookie.
    $location_information = $widget_handler->gatherInformation($form, $form_state);
    // Decide which location information to use.
    $decide_location = $widget_handler->decideLocation($location_information);

    // Decide location might be an array containing coordinates already from
    // Weather.com when querying the city based on user coordinates.
    if (is_array($decide_location)) {
      $coordinates = $decide_location['coordinates'];
      $widget_location = $decide_location['city'];
    }
    else {
      $autocomplete_database = new WundergroundAutoCompleteDatabase(\Drupal::database());
      $city_object = array_pop($autocomplete_database->getCityResult($decide_location));
      if (!empty($city_object->latitude)) {
        $coordinates = array($city_object->latitude, $city_object->longitude);
      }
      $widget_location = $decide_location;
    }
    // If coordinates are empty, a city might not have been queried in the
    // Autocomplete Database. We can query it here.
    if (empty($coordinates)) {
      $client = new Client(['base_uri' => WUNDERGROUND_AUTOCOMPLETE_API_URL]);
      $autocomplete_client = new WundergroundAutocompleteAPi($client);
      $autocomplete_client->requestData($decide_location,  ['c' => 'DE']);
      // Lookup coordinates by city name.
      $autocomplete_database = new WundergroundAutoCompleteDatabase(\Drupal::database());
      $city_object = array_pop($autocomplete_database->getCityResult($decide_location));
      if (!empty($city_object->latitude)) {
        $coordinates = array($city_object->latitude, $city_object->longitude);
      }
    }
    // Build the Ajax Response.
    $response = new AjaxResponse();
    setcookie('weather_location', $decide_location, strtotime("+1 days"), '/');
    $response->addCommand(new ReplaceWeatherWidget($form_state->getBuildInfo()['args'][0], $widget_location, $coordinates));
    return $response;
  }
}