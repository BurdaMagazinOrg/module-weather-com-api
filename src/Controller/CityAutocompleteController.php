<?php
/**
 * @file
 *  Ajax Autocomplete Callback for weatherwidget city textfield.
 */

namespace Drupal\weather_com_api\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\weather_com_api\WundergroundAutocompleteAPi;

class CityAutocompleteController {
  /**
   * Autocomplete for searching locations.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request made from the autocomplete widget.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A json respoonse to use in the autocomplete widget.
   */
  public function autocomplete(Request $request) {
    $client = new Client(['base_uri' => WUNDERGROUND_AUTOCOMPLETE_API_URL]);
    $autocomplete_client = new WundergroundAutocompleteAPi($client);
    $config = \Drupal::config('weather_com_api.settings');
    $countries = $config->get('autocomplete_countries');
    $countries = !empty($countries) ? explode(',', $countries) : 'DE';
    $text = $request->query->get('q');
    $query_text = $this->toASCII($text);
    $cities = [];
    foreach ($countries as $country_code) {
      $temp_response =  $autocomplete_client->requestData($query_text, ['c' => $country_code]);
      $response_content = json_decode($temp_response->getContent(), TRUE);
      $cities = array_merge($cities, $response_content);
    }
    $response_data = new JsonResponse();
    $response_data->setContent(json_encode($cities));
    return $response_data;
  }

  private function toASCII($str) {
        return strtr($str, array('ä' => 'ae', 'ü' => 'ue', 'ö' => 'oe', 'ß' => 'ss'));
    }
}