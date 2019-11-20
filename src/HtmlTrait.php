<?php

namespace Drupal\butils;

use Drupal\Core\Mail\MailFormatHelper;
use Drupal\views\Plugin\views\field\FieldPluginBase;

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

    // Replace spaces before closing bracket.
    $regex = '/<p[^>].*\s>/U';
    preg_match_all($regex, $html, $matches);
    $matches[0] = $matches[0] ?? [];
    foreach ($matches[0] as $match) {
      $html = str_replace(' >', '>', $html);
    }

    // Other replacements.
    $html = str_replace('< ', '&lt; ', $html);
    $html = preg_replace('/<!--.*?-->/', '', $html);
    $html = preg_replace('/<!--(.|\s)*?-->/', '', $html);
    $html = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $html);
    $html = preg_replace("{(<br[\\s]*(>|\/>)\s*){2,}}i", "<br /><br />", $html);
    $html = preg_replace("{(<br[\\s]*(>|\/>)\s*)}i", "<br />", $html);
    $html = preg_replace('/[[:blank:]]+/', ' ', $html);
    $html = str_replace(' < ', ' &lt; ', $html);
    $html = str_replace(' > ', ' &gt; ', $html);
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
   * Truncate HTML.
   *
   * @param string $html
   *   HTML to truncate.
   * @param string $type
   *   Whether the $limit variable will be words or characters.
   * @param int $limit
   *   Truncation limit.
   * @param string $ellipsis
   *   Truncation ellipsis.
   * @param bool $count_html
   *   Whether html tags are counted.
   *
   * @return string
   *   Truncated html.
   */
  public function truncateHtml($html,
    $type = 'chars',
    $limit = 300,
    $ellipsis = '…',
    $count_html = FALSE) {
    if ($count_html) {
      return FieldPluginBase::trimText([
        'max_length' => $limit,
        'word_boundry' => TRUE,
        'ellipsis' => $ellipsis,
        'html' => TRUE,
      ], $html);
    }
    else {
      $truncate = new TruncateHTML();
      if ($type == 'words') {
        return $truncate->truncateWords($html, $limit, $ellipsis);
      }
      else {
        return $truncate->truncateChars($html, $limit, $ellipsis);
      }
    }
  }

  /**
   * Strip links and replace them with their text.
   *
   * @param string $html
   *   Text to process.
   *
   * @return string
   *   String with stripped links.
   */
  public function stripLinks($html) {
    return preg_replace('/<a.*?>(.*)<\/a>/i', '$1', $html);
  }

}
