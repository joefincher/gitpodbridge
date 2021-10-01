<?php

namespace Drupal\language_suggestion\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\file\Entity\File;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class LanguageSuggestionController extends ControllerBase {

  protected $config_factory;

  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config_factory = $config_factory->get('language_suggestion.settings');
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Get HTTP parameter value.
   */
  public function getHTTPHeaderValue(Request $request) {
    $langcode = '';
    $data = [];
    $parameter = $this->config_factory->get('http_header_parameter');
    if ($this->config_factory->get('language_detection') == 'http_header') {
      $langcode = $request->headers->get($parameter);
    }
    $data['#cache'] = [
      'max-age' => 600,
      'tags' => ['language_suggestion_http_header'],
      'contexts' => [
        'url.query_args',
        'ip'
      ],
    ];
    $response = new CacheableJsonResponse($langcode);
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray($data));
    return $response;
  }

  /**
   * Get GEOIP country code from database.
   */
  public function getCountryCode(Request $request) {
    $langcode = '';
    $data = '';
    $language_detection = $this->config_factory->get('language_detection');
    if ($language_detection == 'geoip_db') {
      $db_file_id = $this->config_factory->get('geoip_db_file');
      if ($file = File::load($db_file_id)) {
        try {
          $reader = new Reader($file->getFileUri());
          $record = $reader->country($request->getClientIp());
          $data = !empty($record->country->isoCode) ? $record->country->isoCode : '';
        }
        catch (AddressNotFoundException $e) {

        }
      }
    }
    else if ($language_detection == '_server') {
      if ($_server_param = $this->config_factory->get('_server_parameter')) {
        header('cache-control: no-store', false);
        header('Vary: x-geo-country-code', false);  
        $data = !empty($_SERVER[$_server_param]) ? $_SERVER[$_server_param] : '';
      }
    }
    if ($this->config_factory->get('debug')) {
      \Drupal::logger('language_suggestion')->notice('Detected country:' . $data);
    }
    return new JsonResponse([ 'country' => $data]);
  }

}
