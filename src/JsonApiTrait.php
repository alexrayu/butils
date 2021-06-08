<?php

namespace Drupal\butils;

/**
 * Trait JsonApiTrait.
 *
 * Basic Support for JsonApi Quering.
 */
trait JsonApiTrait {

  /**
   * Request data from jsonapi.
   *
   * @param string $url
   *   Jsonapi URL.
   * @param array $params
   *   Request parameters.
   * @param int $timeout
   *   Request timeout.
   *
   * @return array
   *   Response jsonapi data.
   */
  public function jsonApiQuery($url, array $params = [], $timeout = 5) {
    $query_items = [];
    foreach ($params as $name => $param) {
      $query_items[] = "filter[$name]=$param";
    }
    if (!empty($query_items)) {
      if (strpos($url, '?') === FALSE) {
        $url .= '?';
      }
      else {
        $url .= '&';
      }
      $url .= implode('&', $query_items);
    }
    $res = $this->httpGet($url, $timeout);
    if ($res['code'] == 200) {
      $data = @json_decode($res['text'], TRUE);
      if (!empty($data['data'])) {
        return $data['data'];
      }
    }

    return [];
  }

  /**
   * Get the http url.
   *
   * @param string $url
   *   HTTP(s) url.
   * @param int $timeout
   *   Await timeout.
   *
   * @return array
   *   Request result.
   */
  public function httpGet($url, $timeout = 5) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $res = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
      'code' => $http_code ?: '408',
      'text' => $res,
    ];
  }

}
