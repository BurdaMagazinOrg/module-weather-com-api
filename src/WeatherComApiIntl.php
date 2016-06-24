<?php

namespace Drupal\weather_com_api;


/**
 * Provide information about available languages and measure units.
 *
 * @package Drupal\weather_com_api
 */
class WeatherComApiIntl {
  /**
   * Constructor.
   */
  public function __construct() {

  }

  public static function getLanguages() {

    return [
      'de-DE' => [
        'description' => 'German / Germany',
        'short' => 'de',
        'measure' => 'm',
        'language_code' => 'de-DE',
      ],
      'en-GB' => [
        'description' => 'English / Great Britain',
        'short' => 'en',
        'measure' => 'm',
        'language_code' => 'en-GB',
      ],
      'en-US' => [
        'description' => 'English / United States',
        'short' => 'en',
        'measure' => 'e',
        'language_code' => 'en-US',
      ],
    ];
  }

}
