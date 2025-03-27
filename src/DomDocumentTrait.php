<?php

namespace Drupal\butils;

use Masterminds\HTML5;

/**
 * Trait DomDocument.
 *
 * Taxonomy related utils.
 */
trait DomDocumentTrait {

  /**
   * Gets the dom-element HTML without the parent tag.
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
   * Deleted all matching elements.
   *
   * @param \DOMDocument|string $dom
   *   DomDocument object or html string.
   * @param string $selector
   *   jQuery selector (simple syntax).
   *
   * @return string
   *   The cleaned up html.
   */
  public function domDelAll($dom, string $selector) {
    if (empty($dom)) {
      return NULL;
    }
    if (is_string($dom)) {
      $dom = $this->loadHtml($dom);
    }
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
    $values = $elements ? iterator_to_array($elements) : [];
    foreach ($values as $value) {
      $value->parentNode->removeChild($value);
    }

    $html = $this->domGetBodyHtml($dom);
    return $this->cleanHtml($html);
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
    $content = '';
    $body = $dom->getElementsByTagName('body')->item(0);
    if ($body) {
      foreach ($body->childNodes as $node) {
        $content .= $dom->saveHTML($node);
      }
    }
    else {
      foreach ($dom->childNodes as $node) {
        if ($node->nodeName !== 'html' && $node->nodeName !== '#document') {
          $content .= $dom->saveHTML($node);
        }
        elseif ($node->nodeName === 'html') {
          foreach ($node->childNodes as $child) {
            $content .= $dom->saveHTML($child);
          }
        }
      }
    }

    return $content;
  }

  /**
   * Finds the first matching element.
   *
   * @param \DOMDocument|string $dom
   *   DomDocument object or html string.
   * @param string $selector
   *   jQuery selector (simple syntax).
   * @param bool $inner
   *   Whether to get only the inner html of the element.
   *
   * @return string|null
   *   The first matched snippet.
   */
  public function domFind($dom, string $selector, $inner = FALSE) {
    if (empty($dom)) {
      return NULL;
    }
    if (is_string($dom)) {
      $dom = $this->loadHtml($dom);
    }
    $snippets = $this->domFindAll($dom, $selector, $inner);
    return $snippets[0] ?? NULL;
  }

  /**
   * Finds the element's attributes.
   *
   * Attributes of the first encountered tag will be returned.
   *
   * @param \DOMDocument|string $dom
   *   DomDocument object or html string.
   *
   * @return array
   *   The attributes of an element.
   */
  public function domGetAttributes($dom) {
    if (empty($dom)) {
      return [];
    }
    if (is_string($dom)) {
      $dom = $this->loadHtml($dom);
    }
    $attributes = [];
    $first_element = NULL;
    foreach ($dom->documentElement->childNodes as $node) {
      if ($node instanceof \DOMElement) {
        $first_element = $node;
        break;
      }
    }
    if ($first_element && $first_element->hasAttributes()) {
      foreach ($first_element->attributes as $attr) {
        $attributes[$attr->name] = $attr->value;
      }
    }
    return $attributes;
  }

  /**
   * Finds all matching elements.
   *
   * @param \DOMDocument|string $dom
   *   DomDocument object or html string.
   * @param string $selector
   *   jQuery selector (simple syntax).
   * @param bool $inner
   *   Whether to get only the inner html of the element.
   *
   * @return array
   *   The matched snippets.
   */
  public function domFindAll($dom, string $selector, $inner = FALSE) {
    if (empty($dom)) {
      return [];
    }
    if (is_string($dom)) {
      $dom = $this->loadHtml($dom);
    }
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
      if ($inner) {
        $innerHtml = '';
        foreach ($value->childNodes as $child) {
          $innerHtml .= $dom->saveHTML($child);
        }
        $snippets[] = $innerHtml;
      }
      else {
        $snippets[] = $dom->saveHTML($value);
      }

    }

    return $snippets;
  }

  /**
   * Loads html into DOM without adding a body tag.
   *
   * @param string $html
   *   HTML to load.
   *
   * @return \DOMDocument
   *   Loaded dom.
   */
  public function loadHtml($html) {
    $html5 = new HTML5(['disable_html_ns' => TRUE, 'encoding' => 'UTF-8']);
    return $html5->loadHTML($html);
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
    $tag = preg_match('/^[a-zA-Z0-9\-\_]+/', $selector, $matches) ? $matches[0] : '*';
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
