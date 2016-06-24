<?php

/**
 * @file
 * Contains \Drupal\weather_com_api\Form\WundergroundWeatherApiSettingsForm.
 */

namespace Drupal\weather_com_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\weather_com_api\WeatherComApiIntl;
use Drupal\Core\Locale\CountryManager;

/**
 * Class WundergroundWeatherApiSettingsForm.
 *
 * @package Drupal\weather_com_api\Form
 */
class WeatherComApiSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'weather_api_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('weather_com_api.settings');
    // Get all languages available in the drupal installation.
    $languages = WeatherComApiIntl::getLanguages();
    // Build the options for selecting a language.
    $available_languages = [];
    foreach ($languages as $langcode => $language) {
      $available_languages[$langcode] = $language['description'];
    }
    $wunderground_autocomplete_countries = CountryManager::getStandardList();
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#description' => $this->t('Enter your weather.com API key'),
      '#maxlength' => 255,
      '#size' => 64,
      '#default_value' => !empty($config->get('api_key')) ? $config->get('api_key') : '',
      '#required' => TRUE,
    ];
    $form['language'] = [
      '#type' => 'select',
      '#title' => t('Language'),
      '#options' => $available_languages,
      '#default_value' => !empty($config->get('language')) ? $config->get('language') : '',
      '#required' => TRUE,
    ];
    $form['autocomplete_countries'] = [
      '#type' => 'select',
      '#title' => t('Autocomplete Countries'),
      '#default_value' => !empty($config->get('autocomplete_countries')) ? explode(',', $config->get('autocomplete_countries')) : $wunderground_autocomplete_countries,
      '#options' => $wunderground_autocomplete_countries,
      '#multiple' => TRUE,
      '#size' => min(12, count($wunderground_autocomplete_countries)),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('weather_com_api.settings');
    $config->set('api_key', $form_state->getValue('api_key'));
    $config->set('language', $form_state->getValue('language'));
    $config->set('autocomplete_countries', implode(',', $form_state->getValue('autocomplete_countries')));
    $config->save();
  }

}
