<?php

/**
 * @file
 * Contains \Drupal\weather_com_api\Controller\ReplaceWidgetController.
 */

namespace Drupal\weather_com_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\weather_com_api\Ajax\ReplaceWeatherWidget;
/**
 * Class ReplaceWidgetController.
 *
 * @package Drupal\weather_com_api\Controller
 */
class ReplaceWidgetController extends ControllerBase {
  /**
   * Replacewidget.
   *
   * @return string
   *   Return Hello string.
   */
  public function replaceWidget($weather) {
    $build['forecast_block_location'] = [
      '#theme' => 'weather_com_api_forecast_block',
      '#weather' => $weather,
    ];
    return render($build);
  }
}
