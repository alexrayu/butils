<?php

namespace Drupal\butils;

/**
 * Trait DomDocument.
 *
 * DomDocument related utils.
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

}
