<?php

namespace Drupal\butils;

use Drupal\Core\Mail\MailFormatHelper;

/**
 * Trait Html.
 *
 * Html related utils.
 */
trait HtmlTrait {

  /**
   * Clean up the html string.
   *
   * Removes the multiple spaces, newlines, comments, and invalid characters.
   *
   * @param string $html
   *   Source html.
   *
   * @return string
   *   Cleaned-up html.
   */
  public function cleanHtml($html) {
    $html = $this->cleanString($html);
    $html = preg_replace('/<!--.*?-->/', '', $html);
    $html = preg_replace('/<!--(.|\s)*?-->/', '', $html);
    $html = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $html);
    $html = preg_replace("{(<br[\\s]*(>|\/>)\s*){2,}}i", "<br /><br />", $html);
    $html = preg_replace("{(<br[\\s]*(>|\/>)\s*)}i", "<br />", $html);
    $html = preg_replace('/[[:blank:]]+/', ' ', $html);
    $html = trim($html);

    return $html;
  }

  /**
   * A wrapper to convert html to text.
   *
   * @param string $html
   *   Html.
   *
   * @return string
   *   Plain text.
   */
  public function htmlToText($html) {
    return MailFormatHelper::htmlToText($html);
  }

  /**
   * {@inheritdoc}
   *
   * A wrapper around the TruncateHtml class.
   */
  public function trucateHtml($html, $limit, $ellipsis = '...', $type = 'chars') {
    $truncater = new TruncateHTML();
    if ($type == 'words') {
      return $truncater->truncateWords($html, $limit, $ellipsis);
    }

    return $truncater->truncateChars($html, $limit, $ellipsis);
  }

}
