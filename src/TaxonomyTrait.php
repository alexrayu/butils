<?php

namespace Drupal\butils;

use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Trait Taxonomy.
 *
 * Taxonomy related utils.
 */
trait TaxonomyTrait {

  /**
   * Get term ids, create if missing.
   *
   * @param array $data
   *   Term names.
   * @param string $vid
   *   Vocabulary id.
   * @param string $langcode
   *   Language code.
   *
   * @return array
   *   Term ids.
   */
  public function toTerms(array $data, $vid, $langcode = 'zxx') {
    $tids = [];
    foreach ($data as $name) {
      $tids[] = $this->toTerm($name, $vid, $langcode);
    }

    return $tids;
  }

  /**
   * Get term id, create if missing.
   *
   * @param string $name
   *   Term name.
   * @param string $vid
   *   Vocabulary id.
   * @param string $langcode
   *   Language code.
   *
   * @return int
   *   Term id.
   */
  public function toTerm($name, $vid, $langcode = 'zxx') {
    if (empty($name)) {
      return FALSE;
    }

    // Force zxx if vocabulary is not translatable.
    $vocab_langcode = Vocabulary::load($vid)->language()->getId();
    if ($vocab_langcode === 'zxx') {
      $langcode = 'zxx';
    }

    // Get existing.
    $name = trim(strip_tags($name));
    $res = $this->entityTypeManager->getStorage('taxonomy_term')->getQuery()
      ->condition('vid', $vid)
      ->condition('name', $name)
      ->condition('langcode', $langcode)
      ->execute();
    if (!empty($res)) {
      $tid = reset($res);
    }

    // Create new.
    else {
      $term = Term::create([
        'vid' => $vid,
        'name' => $name,
        'langcode' => $langcode,
      ]);
      $term->save();
      $tid = $term->id();
    }

    return $tid;
  }

  /**
   * Convert array of strings to terms hierarchy.
   *
   * @param array $parts
   *   Strings.
   * @param string $vid
   *   Vocabulary id.
   * @param string $langcode
   *   Vertical language code.
   *
   * @return int
   *   Final child term id.
   */
  public function toHierarchicalTerms(array $parts, $vid, $langcode = 'zxx') {

    // Force zxx if vocabulary is not translatable.
    $vocab_langcode = Vocabulary::load($vid)->language()->getId();
    if ($vocab_langcode === 'zxx') {
      $langcode = 'zxx';
    }

    // If final child exists, return it.
    $word = [end($parts)];
    if (!empty($word) && !empty($vid) && !empty($langcode)) {
      $res = $this->entityTypeManager->getStorage('taxonomy_term')->getQuery()
        ->condition('vid', $vid)
        ->condition('name', $word)
        ->condition('langcode', $langcode)
        ->execute();
      if (!empty($res)) {
        return reset($res);
      }
    }

    // Child does not exist, check and create hierarchy.
    $tids = [];
    foreach ($parts as $part) {
      if (!empty($part)) {
        $res = $this->toTerm($part, $vid, $langcode);
        $tids[] = $res;
      }
    }
    $terms = Term::loadMultiple($tids);
    $terms = array_reverse($terms);
    foreach ($terms as $i => $term) {
      if (!empty($terms[$i + 1])) {
        $parent = $terms[$i + 1];
        $term->set('parent', [
          'target_id' => $parent->id(),
        ])->save();
      }
    }
    if (!empty($terms[0])) {
      return $terms[0]->id();
    }

    return 0;
  }

  /**
   * Get all terms in a vocabulary by key => name.
   *
   * @param string $vocabulary
   *   Vocabulary id.
   * @param bool $show_hierarchy
   *   Shows hierarchy for selects, like:
   *     term_a
   *       - term_b.
   *
   * @return array
   *   Result.
   */
  public function getTermsList($vocabulary, $show_hierarchy = FALSE) {
    $termStorage = $this->entityTypeManager->getStorage('taxonomy_term');
    $tree = $termStorage->loadTree($vocabulary);
    $items = [];
    foreach ($tree as $term) {
      $name = $term->name;
      if ($term->depth && $show_hierarchy) {
        $name = str_repeat('-', $term->depth) . ' ' . $name;
      }
      $items[$term->tid] = $name;
    }

    return $items;
  }
  
  /**
   * Get children of a taxonomy term.
   *
   * @param string|int $tid
   *   Term is.
   * @param int $depth
   *   Deph of tree. 0 - whole depth.
   * @param bool $load_entities
   *   Whether to also load the terms entities.
   *
   * @return array|null
   *   Operation result.
   */
  public function getTermChildren($tid, $depth = 0, $load_entities = FALSE) {
    $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($tid);
    $vid = $term->getVocabularyId();
    return $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vid, $tid, $depth, $load_entities);
  }

}
