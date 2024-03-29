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
   * @param bool $ellipsis
   *   Whether to use ellipsis.
   * @param bool $count_html
   *   Whether html tags are counted.
   *
   * @return string
   *   Truncated html.
   */
  public function truncateHtml($html,
    $type = 'chars',
    $limit = 254,
    $ellipsis = TRUE,
    $count_html = TRUE) {
    if (empty($html)) {
      return '';
    }
    if ($count_html) {
      return FieldPluginBase::trimText([
        'max_length' => $limit,
        'word_boundary' => TRUE,
        'ellipsis' => $ellipsis,
        'html' => TRUE,
      ], $html);
    }
    else {
      if (!empty($ellipsis)) {
        $ellipsis = '…';
      }
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
   * Strip the listed tags from the html sltring.
   *
   * @param string $html
   *   Imput html string.
   * @param array|string $tags
   *   Tag or tags. Ex.: 'a, p, div' or ['a', 'p', 'div'].
   *
   * @return string
   *   Processed string.
   */
  public function stripTags($html, $tags) {
    if (empty($html)) {
      return NULL;
    }
    if (empty($tags)) {
      return $html;
    }
    if (is_string($tags)) {
      $tags = explode(',', $tags);
    }
    $tags = (array) $tags;
    foreach ($tags as $tag) {
      $html = preg_replace('/<' . $tag . '(\s+>|\s+.*>|>)/', '', $html);
      $html = str_replace('</' . $tag . '>', '', $html);
    }
    $html = $this->cleanHtml($html);

    return $html;
  }

  /**
   * Replace a tag in the html sltring.
   *
   * All the tag attributes will be lost.
   *
   * @param string $html
   *   Imput html string.
   * @param string $tag
   *   Tag to be replaced. Ex.: 'a'.
   * @param string $replacement_tag
   *   Tag to serve as a replacement. Ex.: 'p'.
   *
   * @return string
   *   Processed string.
   */
  public function replaceTag($html, $tag, $replacement_tag) {
    if (empty($html)) {
      return NULL;
    }
    if (empty($tag) || empty($replacement_tag)) {
      return $html;
    }
    $html = preg_replace('/<' . $tag . '(\s+>|\s+.*>|>)/', "<$replacement_tag>", $html);
    $html = str_replace('</' . $tag . '>', "</$replacement_tag>", $html);

    return $html;
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
    return $this->stripTags($html, 'a');
  }

  /**
   * Counts words in HTML.
   *
   * @param string $html
   *   HTML to count words in. Can be a plain text string.
   *
   * @return int
   *   Number of words.
   */
  public function countWords($html) {
    $html = strip_tags($html);
    $html = strtolower($this->cleanHtml($html));
    return str_word_count($html);
  }
  
  /**
   * Strip the empty listed tags from the html sltring.
   *
   * @param string $html
   *   Imput html string.
   * @param array|string $tags
   *   Tag or tags. Ex.: 'a, p, div' or ['a', 'p', 'div'].
   *
   * @return string
   *   Processed string.
   */
  public function stripEmptyTags($html, $tags) {
    if (empty($html)) {
      return NULL;
    }
    if (empty($tags)) {
      return $html;
    }
    if (is_string($tags)) {
      $tags = explode(',', $tags);
    }
    $tags = (array) $tags;
    $rounds = 0;
    do {
      $total = 0;
      foreach ($tags as $tag) {
        $tag = trim($tag);
        $html = preg_replace('/<' . $tag . '(\w*)(?:\s+\w+="[^"]+(?:"\$[^"]+"[^"]+)?")*>(\s|&nbsp;|\n|\r|\0|<\/\s?br\s?\/>)*<\/' . $tag . '>/', '', $html, -1, $cases);
        $total += $cases;
      }
      $rounds++;
    } while ($total > 0);

    return $html;
  }

}
