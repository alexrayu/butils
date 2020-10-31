<?php

namespace Drupal\butils;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Database\Driver\mysql\Connection;

/**
 * Class BUtils.
 *
 * Back end Utils.
 */
class BUtils {
  use ArrayTrait;
  use DatetimeTrait;
  use DomDocumentTrait;
  use EntityTrait;
  use FieldTrait;
  use FileTrait;
  use HtmlTrait;
  use ImageStyleTrait;
  use MediaTrait;
  use ParagraphsTrait;
  use SqlQueryTrait;
  use StateTrait;
  use StringTrait;
  use TaxonomyTrait;
  use UriTrait;
  use UserTrait;
  use XmlTrait;

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
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    EntityDisplayRepository $entity_display_repository,
    StateInterface $state,
    RendererInterface $renderer,
    Connection $database) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityDisplayRepository = $entity_display_repository;
    $this->state = $state;
    $this->renderer = $renderer;
    $this->database = $database;
  }

}
