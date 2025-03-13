<?php

namespace Drupal\butils;

/**
 * Trait DomDocument.
 *
 * Taxonomy related utils.
 */
trait DomDocumentTrait {

  /**
   * Gets the domelement HTML without the parent tag.
   *
   * @param \DOMNode $n
   *   DOMElement.
   *
   * @return string|string[]|null
   *   Inner HTML of the Dom XML node.
   */
  public function domNodeInnerHtml(\DOMNode $n) {
    $doc = new \DOMDocument();
    $doc->appendChild($doc->importNode($n, TRUE));
    $html = trim($doc->saveHTML());
    $node_name = $n->nodeName;
    if (!empty($node_name) && $node_name != '#text') {
      $res = preg_replace('#^<' . $node_name . '[^>]*>|</' . $node_name . '>$#', '', $html);
    }
    else {
      $res = $html;
    }

    return $res;
  }

  /**
   * Deletes content by class form DOM.
   *
   * @param \DOMDocument $dom
   *   Dom object.
   * @param string $class
   *   Class string.
   */
  public function domDelByClass(\DOMDocument $dom, $class) {
    $xpath = new \DOMXPath($dom);
    foreach ($xpath->query('//*[contains(attribute::class, "' . $class . '")]') as $e) {
      $e->parentNode->removeChild($e);
    }
  }

  /**
   * Deletes content by id form DOM.
   *
   * @param \DOMDocument $dom
   *   Dom object.
   * @param string $id
   *   Id string.
   */
  public function domDelById(\DOMDocument $dom, $id) {
    $xpath = new \DOMXPath($dom);
    foreach ($xpath->query('//*[contains(attribute::id, "' . $id . '")]') as $e) {
      $e->parentNode->removeChild($e);
    }
  }

  /**
   * Gets the dom body and turns it into html.
   *
   * @param \DOMDocument $dom
   *   Document.
   *
   * @return string
   *   Html output.
   */
  public function domGetBodyHtml(\DOMDocument $dom) {
    $output = '';
    $body = $dom->getElementsByTagName('body')->item(0);
    foreach ($body->childNodes as $childNode) {
      $output .= $dom->saveHTML($childNode);
    }

    return $output;
  }

  /**
   * Finds the first matching element.
   *
   * @param \DOMDocument $dom
   *   DomDocument object.
   * @param string $selector
   *   jQuery selector (simple syntax).
   *
   * @return string|null
   *   The first matched snippet.
   */
  public function domFind($dom, string $selector) {
    $snippets = $this->domFindAll($dom, $selector);
    return $snippets[0] ?? NULL;
  }

  /**
   * Finds all matching elements.
   *
   * @param \DOMDocument $dom
   *   DomDocument object.
   * @param string $selector
   *   jQuery selector (simple syntax).
   *
   * @return array
   *   The matched snippets.
   */
  public function domFindAll($dom, string $selector) {
    [$tag, $attrs] = $this->parseQuerySelector($selector);
    $xpath = new \DOMXPath($dom);
    $query = "//{$tag}";
    if (!empty($attrs)) {
      $conditions = [];
      foreach ($attrs as $key => $values) {
        $values = (array) $values;
        $conditions = array_map(fn ($value) => "contains(concat(' ', normalize-space(@" . $key . "), ' '), ' $value ')", $values);
      }
      $query = '//' . $tag . '[' . implode(' and ', $conditions) . ']';
    }
    $elements = $xpath->query($query);
    $snippets = [];
    $values = $elements ? iterator_to_array($elements) : [];
    foreach ($values as $value) {
      $snippets[] = $dom->saveHTML($value);
    }

    return $snippets;
  }

  /**
   * Parses css syntax selectors into array of tag and attributes.
   *
   * @param string $selector
   *   Selector string.
   *
   * @return array
   *   Array of tag and attributes.
   */
  public function parseQuerySelector(string $selector) {
    $tag = preg_match('/^[a-zA-Z0-9]+/', $selector, $matches) ? $matches[0] : '*';
    $attributes = [];

    // Match class selectors.
    if (preg_match_all('/\.([a-zA-Z0-9_-]+)/', $selector, $classMatches)) {
      $attributes['class'] = $classMatches[1];
    }

    // Match ID selectors.
    if (preg_match('/#([a-zA-Z0-9_-]+)/', $selector, $idMatch)) {
      $attributes['id'] = $idMatch[1];
    }

    // Match attribute selectors ([key=value])
    if (preg_match_all('/\[([a-zA-Z0-9_-]+)(?:=["\']?([^"\']*)["\']?)?\]/', $selector, $attrMatches, PREG_SET_ORDER)) {
      foreach ($attrMatches as $attr) {
        $key = $attr[1];
        $value = $attr[2] ?? '';
        $attributes[$key] = $value;
      }
    }

    return [$tag, $attributes];
  }

}
