<?php

namespace Drupal\butils;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\State\StateInterface;

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
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    EntityDisplayRepository $entity_display_repository,
    StateInterface $state) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityDisplayRepository = $entity_display_repository;
    $this->state = $state;
  }

}
