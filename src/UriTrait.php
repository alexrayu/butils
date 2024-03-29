<?php

namespace Drupal\butils;

use Drupal\node\Entity\Node;
use Drupal\Core\Url;

/**
 * Trait Uri.
 *
 * Uri related utils.
 */
trait UriTrait {

  /**
   * Get string from URL respecting language aliases.
   *
   * @param string $uri
   *   Drupal uri.
   * @param array $options
   *   Url options.
   */
  public function uriToString($uri, array $options = []) {
    $uri_parts = parse_url($uri);
    $string = '';
    if (empty($uri) || $uri_parts == FALSE) {
      return '';
    }
    if (!empty($uri_parts['scheme'])) {
      if ($uri_parts['scheme'] === 'internal') {
        return str_replace('internal:', '', $uri);
      }
      elseif ($uri_parts['scheme'] === 'entity') {
        $parts = explode('/', $uri_parts['path']);
        if ($parts[0] === 'node' && !empty($parts[1])) {
          $node = Node::load($parts[1]);
          if (!empty($node)) {
            $string = $node->toUrl()->setAbsolute()->toString();
          }
        }
      }
      elseif ($uri_parts['scheme'] === 'internal') {
        $string = $uri_parts['path'];
      }
      else {
        $string = $uri;
      }
    }
    if (empty($string)) {
      $string = Url::fromUri($uri, $options)->toString();
    }

    return $string;
  }

}
