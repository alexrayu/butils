<?php

namespace Drupal\butils\Language;

use Drupal\language\LanguageNegotiator;

/**
 * Class responsible for performing language negotiation.
 */
class ButilsLanguageNegotiator extends LanguageNegotiator {

  /**
   * Language code.
   *
   * @var string
   */
  protected $languageCode = NULL;

  /**
   * {@inheritdoc}
   */
  public function initializeType($type) {
    $language = NULL;
    $method_id = static::METHOD_ID;
    $availableLanguages = $this->languageManager->getLanguages();
    if ($this->languageCode && isset($availableLanguages[$this->languageCode])) {
      $language = $availableLanguages[$this->languageCode];
    }
    else {
      $language = $this->languageManager->getDefaultLanguage();
    }

    return [$method_id => $language];
  }

  /**
   * Set the language.
   *
   * @param string $langcode
   *   Language langcode.
   */
  public function setLanguageCode($langcode) {
    $this->languageCode = $langcode;
  }

}
