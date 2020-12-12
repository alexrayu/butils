<?php

namespace Drupal\butils;

/**
 * SQL Query trait.
 *
 * Field related utils.
 */
trait SqlQueryTrait {

  /**
   * Converts SQL Query to an SQL compatible stirng with parameters.
   *
   * @param object $query
   *   SQL Query.
   *
   * @return string
   *   SQL query string.
   */
  public function sqlQueryToString($query) {
    $query_string = $query->__toString();
    if (method_exists($query, 'getArguments')) {
      $args = $query->getArguments();
      foreach ($args as $placeholder => $arg) {
        $query_string = str_replace($placeholder, '"' . $arg . '"', $query_string);
      }
      $query_string = str_replace(['{', '}'], '', $query_string);
    }

    return $query_string;
  }

}
