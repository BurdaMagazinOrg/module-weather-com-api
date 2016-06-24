<?php

/**
 * @file
 * Contains \Drupal\weather_com_api\WundergroundAutoCompleteDatabase.
 */

namespace Drupal\weather_com_api;

use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WundergroundAutoCompleteDatabase.
 *
 * @package Drupal\weather_com_api
 */
class WundergroundAutoCompleteDatabase {

  protected $database;
  /**
   * Constructor.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Save Autocomplete Result Objects to the database.
   * @param array $results
   *  An results set of autocomplete request.
   */
  public function saveAutocompleteResults($results) {
    foreach ($results as $result) {
      $city = $result->name;
      // Check if city is already in database.
      $db = $this->database;
      $query_result = $db->select('weather_api_cities', 'cities')
        ->fields('cities', array('cid', 'city'))
        ->condition('cities.city', $city, '=')
        ->execute();
      $data = $query_result->fetchAll();
      $city_name = substr($result->name, 0, strpos($result->name, ','));
      if (empty($data)) {
        $db->insert('weather_api_cities', array())
          ->fields(array('city', 'l', 'latitude', 'longitude'), array($city_name, $result->l, $result->lat, $result->lon))
          ->execute();
      }
    }
  }

  /**
   * Try to get a already saved result from database.
   * @param $city
   */
  public function getCityResult($city) {
    $db = $this->database;
    $query_result = $db->select('weather_api_cities', 'cities')
      ->fields('cities', array('cid', 'city', 'l', 'latitude', 'longitude'))
      ->condition('cities.city', $city, '=')
      ->execute();
    $data = $query_result->fetchAll();
    if (empty($data)) {
      return FALSE;
    }
    return $data;
  }
}
