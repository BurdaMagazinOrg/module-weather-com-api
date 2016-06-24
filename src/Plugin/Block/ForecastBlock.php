<?php

/**
 * @file
 * Contains \Drupal\weather_com_api\Plugin\Block\ForecastBlock.
 */

namespace Drupal\weather_com_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'ForecastBlock' block.
 *
 * @Block(
 *  id = "forecast_block",
 *  admin_label = @Translation("Forecast block"),
 * )
 */
class ForecastBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['location'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Default location'),
      '#description' => $this->t(''),
      '#default_value' => isset($this->configuration['location']) ? $this->configuration['location'] : '',
      '#maxlength' => 64,
      '#size' => 64,
      '#autocomplete_route_name' => 'weather_com_api.city.autocomplete',
    );
    $form['data_feature'] = array(
      '#type' => 'select',
      '#title' => $this->t('Data Feature'),
      '#description' => $this->t('Choose the type of data you want to display'),
      '#options' => ['forecast' => 'forecast', 'observations' => 'observations'],
      '#default_value' => isset($this->configuration['data_feature']) ? $this->configuration['data_feature'] : '',
    );
    $form['forecast']['day_feature'] = array(
      '#type' => 'select',
      '#title' => $this->t('Day Feature'),
      '#options' => ['intraday' => 'intraday'],
      '#default_value' => isset($this->configuration['forecast']['day_feature']) ? $this->configuration['forecast']['day_feature'] : '',
      '#states' => array(
        'visible' => array(
          ':input[name="settings[data_feature]"]' => array('value' => 'forecast'),
        ),
      ),
    );
    $form['forecast']['days'] = array(
      '#type' => 'select',
      '#title' => $this->t('Day Feature'),
      '#options' => ['3day' => '3 days', '5day' => '5 days', '7day' => '7 days', '10day' => '10 Days'],
      '#default_value' => isset($this->configuration['forecast']['days']) ? $this->configuration['forecast']['days'] : '',
      '#states' => array(
        'visible' => array(
          ':input[name="settings[data_feature]"]' => array('value' => 'forecast'),
        ),
      ),
    );
    $form['observations']['observation_feature'] = array(
      '#type' => 'select',
      '#title' => $this->t('Observation feature'),
      '#options' => ['current' => 'current',],
      '#default_value' => isset($this->configuration['observations']['observation_feature']) ? $this->configuration['observations']['observation_feature'] : '',
      '#states' => array(
        'visible' => array(
          ':input[name="settings[data_feature]"]' => array('value' => 'observations'),
        ),
      ),
    );
    $form['current'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Include current weather'),
      '#default_value' => isset($this->configuration['current']) ? $this->configuration['current'] : '',
      '#states' => array(
        'invisible' => array(
          ':input[name="settings[data_feature]"]' => array('value' => 'observations'),
        ),
      ),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['location'] = str_replace('"', '', $form_state->getValue('location'));
    $this->configuration['data_feature'] = $form_state->getValue('data_feature');
    $this->configuration['day_feature'] = $form_state->getValue('day_feature');
    $this->configuration['forecast'] = $form_state->getValue('forecast');
    $this->configuration['observations'] = $form_state->getValue('observations');
    $this->configuration['current'] = $form_state->getValue('current');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['form'] = \Drupal::formBuilder()->getForm('Drupal\weather_com_api\Form\WeatherComApiWidgetForm', $this->configuration);

    $build['forecast_block_location'] = [
      '#theme' => 'weather_com_api_forecast_block',
    ];
    $build['forecast_block_location']['#attached']['library'][] = 'weather_com_api/weather-widget';
   return $build;
  }
  
  
}
