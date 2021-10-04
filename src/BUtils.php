<?php

namespace Drupal\butils;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;

/**
 * Class BUtils.
 *
 * Back end Utils.
 */
class BUtils {
  use ArrayTrait;
  use CsvTrait;
  use CurrentTrait;
  use DatetimeTrait;
  use DebugTrait;
  use DomDocumentTrait;
  use EntityTrait;
  use FieldTrait;
  use FileTrait;
  use HtmlTrait;
  use ImageStyleTrait;
  use JsonApiTrait;
  use MediaTrait;
  use MenuTrait;
  use ParagraphsTrait;
  use RedirectsTrait;
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
   * FileSystem service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * Module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Menu link tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuTree;

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
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   FileSystem service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   Language manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   Route matcher.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Request stack.
   * @param \Drupal\Core\Session\AccountProxyInterface $account_proxy
   *   Current account.
   * @param \Drupal\Core\Path\PathMatcherInterface $path_matcher
   *   Path matcher.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler.
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_link_tree
   *   Menu link treee service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    EntityDisplayRepository $entity_display_repository,
    StateInterface $state,
    RendererInterface $renderer,
    Connection $database,
    FileSystemInterface $file_system,
    LanguageManagerInterface $language_manager,
    RouteMatchInterface $route_match,
    RequestStack $request_stack,
    AccountProxyInterface $account_proxy,
    PathMatcherInterface $path_matcher,
    ModuleHandlerInterface $module_handler,
    MenuLinkTreeInterface $menu_link_tree) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityDisplayRepository = $entity_display_repository;
    $this->state = $state;
    $this->renderer = $renderer;
    $this->database = $database;
    $this->fileSystem = $file_system;
    $this->languageManager = $language_manager;
    $this->routeMatch = $route_match;
    $this->requestStack = $request_stack;
    $this->currentUser = $account_proxy;
    $this->pathMatcher = $path_matcher;
    $this->moduleHandler = $module_handler;
    $this->menuTree = $menu_link_tree;
  }

}
