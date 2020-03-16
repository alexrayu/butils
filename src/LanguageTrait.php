<?php

namespace Drupal\butils;

/**
 * Trait LanguageTrait.
 *
 * Language related utils.
 */
trait LanguageTrait {

  /**
   * Set the current language by langcode. Reset if empty.
   *
   * @param string $langcode
   *   Langcode.
   */
  public function setLangcode($langcode) {
    $butilsLanguageNegotiator = $this->butilsLanguageNegotiator;
    $this->languageManager->setNegotiator($butilsLanguageNegotiator);
    $this->languageManager->reset();
    $this->languageManager->getNegotiator()->setLanguageCode($langcode);
  }

  /**
   * Get the current langcode.
   *
   * @return string
   *   Current langcode id.
   */
  public function getLangcode() {
    return $this->languageManager->getCurrentLanguage()->getId();
  }

}
