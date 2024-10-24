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

  /**
   * Gets the json metadata from an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to get the metadata for.
   * @param string $field_name
   *   The name of the metadata field.
   *
   * @return array
   *   The metadata.
   */
  public function getEntityMeta($entity, $field_name = 'field_json_metadata'): array {
    if (!$entity->hasField($field_name)
      || $entity->get($field_name)->isEmpty()
    ) {
      return [];
    }
    $meta = $entity->get($field_name)->value;

    return json_decode($meta, TRUE);
  }

  /**
   * Applies the json metadata to an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to apply the metadata to.
   * @param array $meta
   *   The metadata to apply.
   * @param string $field_name
   *   The name of the field.
   * @param bool $save
   *   Whether to save the node.
   *
   * @return bool
   *   Whether the metadata was applied.
   */
  public function setEntityMeta($entity, array $meta, $field_name = 'field_json_metadata', $save = TRUE): bool {
    if (!$entity->hasField($field_name)) {
      return FALSE;
    }
    $entity->set($field_name, json_encode($meta));
    if ($save) {
      $entity->changed->preserve = TRUE;
      $entity->save();
    }

    return TRUE;
  }

  /**
   * Applies the json metadata item by key/value to an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to apply the metadata to.
   * @param string $key
   *   The metadata key to apply.
   * @param mixed $value
   *   The value to apply.
   * @param string $field_name
   *   The name of the field.
   * @param bool $save
   *   Whether to save the node.
   *
   * @return bool
   *   Whether the metadata was applied.
   */
  public function setEntityMetaItem($entity, $key, $value, $field_name = 'field_json_metadata', $save = TRUE): bool {
    $meta = $this->getEntityMeta($entity, $field_name);
    $meta[$key] = $value;
    return $this->setEntityMeta($entity, $meta, $field_name, $save);
  }

  /**
   * Gets the json metadata by key from an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to get the metadata for.
   * @param string $key
   *   The key for the metadata item.
   * @param string $field_name
   *   The name of the metadata field.
   *
   * @return mixed
   *   The metadata value if any.
   */
  public function getEntityMetaItem($entity, $key, $field_name = 'field_json_metadata') {
    if (!$entity->hasField($field_name)
      || $entity->get($field_name)->isEmpty()
    ) {
      return NULL;
    }
    $meta = $entity->get($field_name)->value;
    $decoded = json_decode($meta, TRUE);

    return $decoded[$key] ?? NULL;
  }

}
