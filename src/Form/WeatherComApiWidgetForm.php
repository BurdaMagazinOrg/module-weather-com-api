<?php

/**
 * @file
 * Contains \Drupal\weather_com_api\Form\WundergroundWeatherApiSettingsForm.
 */

namespace Drupal\weather_com_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\weather_com_api\Ajax\ReplaceWeatherWidget;

/**
 * Class WundergroundWeatherApiSettingsForm.
 *
 * @package Drupal\weather_com_api\Form
 */
class WeatherComApiWidgetForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'weather_com_widget_city_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['city'] = array(
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#size' => 15,
      '#default_value' => '',
      '#autocomplete_route_name' => 'weather_com_api.city.autocomplete',
      '#id' => 'weather-widget-city'
    );
    $form['coordinates'] = array(
      '#type' => 'textfield',
      '#id' => 'weather-widget-coordinates',
    );
    $form['submit'] = [
      '#type' => 'image_button',
      '#src' => drupal_get_path('module', 'weather_com_api') .'/images/lense.svg',
      '#value' => t('Submit'),
      '#id' => 'weather-widget-submit',
      '#ajax' => array(
        'callback' => 'Drupal\weather_com_api\FormElement\WeatherComApiWidgetSubmit::sendAjax',
        'progress' => array(
          'type' => 'throbber',
          'message' => NULL,
        ),
      ),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
