<?php

namespace Drupal\butils;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\butils\Language\ButilsLanguageNegotiator;

/**
 * Class BUtils.
 *
 * Back end Utils.
 */
class BUtils {

  use ArrayTrait;
  use DatetimeTrait;
  use EntityTrait;
  use FieldTrait;
  use FileTrait;
  use HtmlTrait;
  use StringTrait;
  use TaxonomyTrait;
  use XmlTrait;
  use ParagraphsTrait;
  use StateTrait;
  use DomDocumentTrait;
  use LanguageTrait;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Drupal\Core\Entity\EntityDisplayRepository definition.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepository
   */
  protected $entityDisplayRepository;

  /**
   * State service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * Language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * BUtils language negotiator.
   *
   * @var \Drupal\butils\Language\ButilsLanguageNegotiator
   */
  protected $butilsLanguageNegotiator;

  /**
   * Constructs a new BUtils object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   Entity field manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepository $entity_display_repository
   *   Entity display repository.
   * @param \Drupal\Core\State\StateInterface $state
   *   State manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Renderer.
   * @param \Drupal\Core\Database\Driver\mysql\Connection $database
   *   Database connection.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   Language manager.
   * @param \Drupal\butils\Language\ButilsLanguageNegotiator $butils_language_negotiator
   *   BUtils language negotiator.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    EntityDisplayRepository $entity_display_repository,
    StateInterface $state,
    RendererInterface $renderer,
    Connection $database,
    LanguageManagerInterface $language_manager,
    ButilsLanguageNegotiator $butils_language_negotiator) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityDisplayRepository = $entity_display_repository;
    $this->state = $state;
    $this->renderer = $renderer;
    $this->database = $database;
    $this->languageManager = $language_manager;
    $this->butilsLanguageNegotiator = $butils_language_negotiator;
  }

}
