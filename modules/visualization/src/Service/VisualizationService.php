<?php

namespace Drupal\re_mgr_visualization\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\re_mgr\Entity\EntityBaseDataTrait;
use Drupal\re_mgr\Entity\EntityInterface;
use Drupal\re_mgr\Entity\Flat\Flat;
use Drupal\re_mgr\Service\EntityServiceInterface;
use Drupal\webform\WebformInterface;

/**
 * Visualization service.
 */
class VisualizationService implements VisualizationServiceInterface {
  use StringTranslationTrait;
  use EntityBaseDataTrait;

  /**
   * The Entity Type Manager service.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Entity Display Repository service.
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * The Config Factory service.
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The Real Estate Manager Entity Service.
   */
  protected EntityServiceInterface $entityService;

  /**
   * Constructs a VisualizationService object.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    EntityDisplayRepositoryInterface $entity_display_repository,
    ConfigFactoryInterface $config_factory,
    EntityServiceInterface $entity_service
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityDisplayRepository = $entity_display_repository;
    $this->configFactory = $config_factory;
    $this->entityService = $entity_service;
  }

  /**
   * {@inheritdoc}
   */
  public function getStartingEntity(array $configuration): ?EntityInterface {
    if ($configuration['start_from_building'] === 0) {
      $entity_keyword = 'estate';
      $entity_id = $configuration['starting_estate'];
    }
    else {
      $entity_keyword = 'building';
      $entity_id = $configuration['starting_building'];
    }

    return $this->entityService->getEntityFromData($entity_keyword, $entity_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityMainImageData(EntityInterface $entity, string $main_image_style = NULL): array {
    /** @var \Drupal\media\MediaInterface|null */
    $media_image = $entity->get('main_image')->entity;

    if (is_null($media_image)) {
      return [];
    }

    $field_image = $media_image->get('field_re_mgr_image');
    /** @var array */
    $image_properties = $field_image->getValue();

    /* Apply optional main image style */
    if ($main_image_style) {
      /** @var \Drupal\image\ImageStyleInterface|null */
      $image_style = $this->entityTypeManager->getStorage('image_style')->load($main_image_style);

      if (!empty($image_style)) {
        /** @var \Drupal\file\Entity\File */
        $file = $this->entityTypeManager->getStorage('file')->load($image_properties[0]['target_id']);
        /** @var string */
        $image_uri = $file->getFileUri();
        $styled_image_url = $image_style->buildUrl($image_uri);
        $image_style->createDerivative($image_uri, $styled_image_url);
        $main_image_url = $styled_image_url;
      }
      else {
        /** @var \Drupal\file\Entity\File */
        $file = $field_image->entity;
        $main_image_url = $file->createFileUrl();
      }
    }
    else {
      /** @var \Drupal\file\Entity\File */
      $file = $field_image->entity;
      $main_image_url = $file->createFileUrl();
    }

    return [
      'url' => $main_image_url,
      'width' => $image_properties[0]['width'],
      'height' => $image_properties[0]['height'],
      'style' => $main_image_style,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getRelatedEntitySvgData(EntityInterface $entity): array {
    if ($entity instanceof Flat) {
      return [];
    }

    $paths = [];
    $related_entities = $this->entityService->getRelatedEntities($entity);

    if (!empty($related_entities)) {
      /** @var \Drupal\re_mgr\Entity\EntityInterface $entity */
      foreach ($related_entities as $entity) {
        $paths[$entity->id()] = $entity->get('coordinates')->value;
      }
    }

    return $paths;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetOpacity(int $target_opacity): string {
    $filtered_target_opacity = '0.3';

    switch ($target_opacity) {
      case 0:
        $filtered_target_opacity = '0';
        break;

      case 100:
        $filtered_target_opacity = '1';
        break;

      default:
        $filtered_target_opacity = '0.' . $target_opacity;
    }

    return $filtered_target_opacity;
  }

  /**
   * {@inheritdoc}
   */
  public function getLegend(array $related_entities): array {
    $legend = [];

    if (!empty($related_entities)) {
      $legend = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['legend'],
        ],
        'statuses' => [],
      ];

      foreach ($related_entities as $entity) {
        $entity_status = $entity->status->value;

        if (empty($entity->status->value)) {
          $entity_status = '1';
        }

        /** @var string */
        $status_name = self::ENTITIES_STATUSES[$entity_status];

        // @codingStandardsIgnoreStart
        $legend['statuses']['status_' . $entity_status] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => [
            'class' => [strtolower(self::ENTITIES_STATUSES[$entity_status])],
          ],
          '#value' => $this->t($status_name),
        ];
        // @codingStandardsIgnoreEnd
      }
    }

    return $legend;
  }

  /**
   * {@inheritdoc}
   */
  public function convertToSvgPathsWithStatus(array $svg_paths_data, string $related_entity_type): array {
    $svg_paths_data_with_status = [];

    foreach ($svg_paths_data as $entity_id => $path) {
      /** @var \Drupal\re_mgr\Entity\EntityInterface|null */
      $entity = $this->entityTypeManager->getStorage($related_entity_type)->load($entity_id);
      $entity_status = $entity?->get('status')->value ?: '1';
      $svg_paths_data_with_status[$entity_id]['coordinates'] = $path;
      $svg_paths_data_with_status[$entity_id]['status'] = strtolower(self::ENTITIES_STATUSES[$entity_status]);
      $svg_paths_data_with_status[$entity_id]['fill'] = self::STATUSES_COLOR[$entity_status];
    }

    return $svg_paths_data_with_status;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelatedEntitiesTooltipData(EntityInterface $entity, string $related_entity_type): array {
    $related_entities = $this->entityService->getRelatedEntities($entity);
    $view_builder = $this->entityTypeManager->getViewBuilder($related_entity_type);
    $tooltip_data = [];

    foreach ($related_entities as $entity_id => $entity) {
      $entity_status = $entity->status->value ?? '1';
      $entity_view_modes = $this->entityDisplayRepository->getViewModeOptionsByBundle($related_entity_type, $entity->type->target_id);

      if ($entity_status !== '3' && array_key_exists('tooltip', $entity_view_modes)) {
        $entity_view = $view_builder->view($entity, 'tooltip');
        $tooltip_data[$entity_id] = $entity_view;
      }
    }

    return $tooltip_data;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityDescription(EntityInterface $entity): array {
    $entity_type_id = $entity->getEntityTypeId();
    $entity_view_modes = $this->entityDisplayRepository->getViewModeOptionsByBundle($entity_type_id, $entity->get('type')->target_id);
    $entity_description = [];

    if (array_key_exists('description', $entity_view_modes)) {
      $view_builder = $this->entityTypeManager->getViewBuilder($entity_type_id);
      $entity_description = $view_builder->view($entity, 'description');
    }

    return $entity_description;
  }

  /**
   * {@inheritdoc}
   */
  public function getWebformView(string $webform_id): array {
    $view_builder = $this->entityTypeManager->getViewBuilder('webform');
    $webform = $this->entityTypeManager->getStorage('webform')->load($webform_id);
    $webform_view = $webform instanceof WebformInterface ? $view_builder->view($webform) : [];
    return $webform_view;
  }

  /**
   * {@inheritdoc}
   */
  public function getNavigationOptions(EntityInterface $entity, string $starting_entity_keyword, string $sell_entity_keyword): array {
    $navigation_options = [
      'buildings' => [],
      'default_building' => NULL,
      'floors' => [],
      'default_floor' => NULL,
      'flats' => [],
      'default_flat' => NULL,
    ];
    $is_create_buildings_options = $starting_entity_keyword === 'estate' ? TRUE : FALSE;
    $entity_type_id = $entity->getEntityType()->id();

    /* Estate stage */
    if ($entity_type_id === 're_mgr_estate' && $is_create_buildings_options) {

      /* Set building options */
      $building_storage = $this->entityTypeManager->getStorage('re_mgr_building');
      $related_buildings_query = $building_storage
        ->getQuery()
        ->accessCheck(TRUE)
        ->condition('estate_id', $entity->id());

      if ($sell_entity_keyword === 'building') {
        $related_buildings_query->condition('status', 3, 'NOT IN');
      }

      $related_buildings_ids = $related_buildings_query->execute();
      $related_buildings = $building_storage->loadMultiple($related_buildings_ids);

      $navigation_options['buildings']['null'] = '-';
      $navigation_options['floors']['null'] = '-';
      $navigation_options['flats']['null'] = '-';

      foreach ($related_buildings as $id => $building) {
        $navigation_options['buildings'][$id] = $building->label();
      }
    }

    /* Building stage */
    if ($entity_type_id === 're_mgr_building') {

      if ($is_create_buildings_options) {
        /* Set default building option */
        $navigation_options['default_building'] = $entity->id();

        /* Set building options */
        $building_storage = $this->entityTypeManager->getStorage('re_mgr_building');
        $sibling_buildings_query = $building_storage
          ->getQuery()
          ->accessCheck(TRUE)
          ->condition('estate_id', $entity->get('estate_id')->target_id);

        if ($sell_entity_keyword === 'building') {
          $sibling_buildings_query->condition('status', 3, 'NOT IN');
        }

        $sibling_buildings_ids = $sibling_buildings_query->execute();
        $sibling_buildings = $building_storage->loadMultiple($sibling_buildings_ids);

        foreach ($sibling_buildings as $id => $building) {
          $navigation_options['buildings'][$id] = $building->label();
        }
      }

      /* Set floor options */
      $navigation_options['floors']['null'] = '-';
      $building_storage = $this->entityTypeManager->getStorage('re_mgr_floor');
      $related_floors_ids = $building_storage
        ->getQuery()
        ->accessCheck(TRUE)
        ->condition('building_id', $entity->id())
        ->execute();
      $related_floors = $building_storage->loadMultiple($related_floors_ids);

      foreach ($related_floors as $id => $floor) {
        $navigation_options['floors'][$id] = $floor->label();
      }

      /* Set flat options */
      $navigation_options['flats']['null'] = '-';
    }

    /* Floor stage */
    if ($entity_type_id === 're_mgr_floor') {

      if ($is_create_buildings_options) {
        /* Set default building option */
        $navigation_options['default_building'] = $entity->get('building_id')->target_id;
        /* @phpstan-ignore-next-line */
        $parent_estate_id = $entity->building_id->entity->estate_id->target_id;

        /* Set building options */
        $building_storage = $this->entityTypeManager->getStorage('re_mgr_building');
        $parent_buildings_query = $building_storage
          ->getQuery()
          ->accessCheck(TRUE)
          ->condition('estate_id', $parent_estate_id);

        if ($sell_entity_keyword === 'building') {
          $parent_buildings_query->condition('status', 3, 'NOT IN');
        }

        $parent_buildings_ids = $parent_buildings_query->execute();
        $parent_buildings = $building_storage->loadMultiple($parent_buildings_ids);

        foreach ($parent_buildings as $id => $building) {
          $navigation_options['buildings'][$id] = $building->label();
        }
      }

      /* Set default floor option */
      $navigation_options['default_floor'] = $entity->id();

      /* Set floor options */
      $floor_storage = $this->entityTypeManager->getStorage('re_mgr_floor');
      $sibling_floors_ids = $floor_storage
        ->getQuery()
        ->accessCheck(TRUE)
        ->condition('building_id', $entity->get('building_id')->target_id)
        ->execute();
      $sibling_floors = $floor_storage->loadMultiple($sibling_floors_ids);

      foreach ($sibling_floors as $id => $floor) {
        $navigation_options['floors'][$id] = $floor->label();
      }

      /* Set flat options */
      $navigation_options['flats']['null'] = '-';
      $flat_storage = $this->entityTypeManager->getStorage('re_mgr_flat');
      $related_flats_ids = $flat_storage
        ->getQuery()
        ->accessCheck(TRUE)
        ->condition('floor_id', $entity->id())
        ->condition('status', 3, 'NOT IN')
        ->execute();
      $related_flats = $flat_storage->loadMultiple($related_flats_ids);

      foreach ($related_flats as $id => $flat) {
        $navigation_options['flats'][$id] = $flat->label();
      }
    }

    /* Flat stage */
    if ($entity_type_id === 're_mgr_flat') {

      if ($is_create_buildings_options) {
        /* Set default building option */
        /* @phpstan-ignore-next-line */
        $navigation_options['default_building'] = $entity->floor_id->entity->building_id->target_id;
        /* @phpstan-ignore-next-line */
        $parent_estate_id = $entity->floor_id->entity->building_id->entity->estate_id->target_id;

        /* Set building options */
        $building_storage = $this->entityTypeManager->getStorage('re_mgr_building');
        $parent_buildings_ids = $building_storage
          ->getQuery()
          ->accessCheck(TRUE)
          ->condition('estate_id', $parent_estate_id)
          ->execute();
        $parent_buildings = $building_storage->loadMultiple($parent_buildings_ids);

        foreach ($parent_buildings as $id => $building) {
          $navigation_options['buildings'][$id] = $building->label();
        }
      }

      /* Set default floor option */
      $navigation_options['default_floor'] = $entity->get('floor_id')->target_id;

      /* Set floor options */
      $floor_storage = $this->entityTypeManager->getStorage('re_mgr_floor');

      /* @phpstan-ignore-next-line */
      $building_id_condition = $entity->floor_id->entity->building_id->target_id;
      $parent_floors_ids = $floor_storage
        ->getQuery()
        ->accessCheck(TRUE)
        ->condition('building_id', $building_id_condition)
        ->execute();
      $parent_floors = $floor_storage->loadMultiple($parent_floors_ids);

      foreach ($parent_floors as $id => $floor) {
        $navigation_options['floors'][$id] = $floor->label();
      }

      /* Set default flat option */
      $navigation_options['default_flat'] = $entity->id();

      /* Set flat options */
      $flat_storage = $this->entityTypeManager->getStorage('re_mgr_flat');
      $sibling_flats_ids = $flat_storage
        ->getQuery()
        ->accessCheck(TRUE)
        ->condition('floor_id', $entity->get('floor_id')->target_id)
        ->condition('status', 3, 'NOT IN')
        ->execute();
      $sibling_flats = $flat_storage->loadMultiple($sibling_flats_ids);

      foreach ($sibling_flats as $id => $flat) {
        $navigation_options['flats'][$id] = $flat->label();
      }
    }

    return $navigation_options;
  }

  /**
   * {@inheritdoc}
   */
  public function getNavigation(EntityInterface $entity, string $starting_entity_keyword, string $sell_entity_keyword): array {
    $navigation_options = $this->getNavigationOptions($entity, $starting_entity_keyword, $sell_entity_keyword);
    $selects_count = 0;
    $navigation = [
      '#type' => 'container',
      'navigation' => [],
    ];

    if ($starting_entity_keyword === 'estate') {
      $navigation['navigation']['select_building'] = [
        '#type' => 'select',
        '#title' => $this->t('Building'),
        '#options' => $navigation_options['buildings'],
        '#value' => $navigation_options['default_building'],
        '#attributes' => ['class' => ['building']],
      ];
      $selects_count++;
    }

    $navigation['navigation']['select_floor'] = [
      '#type' => 'select',
      '#title' => $this->t('Floor'),
      '#options' => $navigation_options['floors'],
      '#value' => $navigation_options['default_floor'],
      '#attributes' => ['class' => ['floor']],
    ];
    $selects_count++;

    if ($sell_entity_keyword === 'flat') {
      $navigation['navigation']['select_flat'] = [
        '#type' => 'select',
        '#title' => $this->t('Flat', [], ['context' => "Real estate"]),
        '#options' => $navigation_options['flats'],
        '#value' => $navigation_options['default_flat'],
        '#attributes' => ['class' => ['flat']],
      ];
      $selects_count++;
    }

    switch ($selects_count) {
      case 2:
        $navigation['#attributes']['class'] = [
          'navigation-container',
          'two-selects',
        ];
        break;

      case 3:
        $navigation['#attributes']['class'] = [
          'navigation-container',
          'three-selects',
        ];
        break;

      default:
        $navigation['#attributes']['class'] = [
          'navigation-container',
          'one-select',
        ];
    }

    return $navigation;
  }

  /**
   * {@inheritdoc}
   */
  public function getBuildingDescriptionButton(EntityInterface $entity): array {
    if (
      $entity->getEntityKeyword() === 'estate' ||
      $entity->getEntityKeyword() === 'flat'
    ) {
      return [];
    }

    if ($entity->getEntityKeyword() === 'floor') {
      $entity = $entity->getParentEntity();
    }

    $description_btn = [
      '#type' => 'link',
      '#title' => $this->t('Description'),
      '#url' => Url::fromRoute('entity.re_mgr_building.canonical_description', ['re_mgr_building' => $entity?->id()]),
      '#attributes' => [
        'class' => [
          'description-btn',
          're-mgr-visualization-btn',
          'use-ajax',
        ],
        'data-dialog-options' => '{"height":"80%","width":"90%","max-height":"650","max-width":"750"}',
        'data-dialog-type' => 'modal',
      ],
    ];

    return $description_btn;
  }

  /**
   * {@inheritdoc}
   */
  public function getGuide(string $sell_entity_keyword, string $entity_keyword): array {
    if (
      $entity_keyword === 'flat' ||
      $sell_entity_keyword === 'building' && $entity_keyword === 'floor'
    ) {
      return [];
    }

    $related_entity_type_id = self::RELATED_ENTITY_MAP['re_mgr_' . $entity_keyword];
    $related_entity_keyword = $this->entityService->getEntityKeywordFromString($related_entity_type_id);

    return [
      '#type' => 'html_tag',
      '#attributes' => [
        'class' => ['description'],
      ],
      '#tag' => 'span',
      '#value' => $this->t('Select @entity by hovering over it with the cursor', ['@entity' => $related_entity_keyword]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getBackButton(): array {
    return [
      '#type' => 'html_tag',
      '#attributes' => [
        'class' => ['back-btn', 're-mgr-visualization-btn'],
      ],
      '#tag' => 'span',
      '#value' => $this->t('Back'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getAskForOfferButton(string $webform_id): array {
    return [
      '#type' => 'link',
      '#title' => $this->t('Ask for offer'),
      '#url' => Url::fromRoute('entity.webform.canonical', ['webform' => $webform_id]),
      '#attributes' => [
        'class' => [
          'ask-for-offer-btn',
          're-mgr-visualization-btn',
          'use-ajax',
        ],
        'data-dialog-options' => '{"height":"80%","width":"90%","max-height":"650","max-width":"750"}',
        'data-dialog-type' => 'modal',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildContent(
    string $block_id,
    string $entity_keyword,
    string $entity_id,
    array $main_image_data,
    array $svg_paths_data,
    string $path_fill,
    string $path_target_opacity,
    string $starting_entity_keyword,
    string $sell_entity_keyword,
    string $webform_id
  ): array {

    /* Prepare current entity */
    $entity = $this->entityService->getEntityFromData($entity_keyword, $entity_id);

    /* Clear svg paths if building is for sell */
    if ($entity_keyword === 'floor' && $sell_entity_keyword === 'building') {
      $svg_paths_data = [];
    }

    /* Prepare navigation */
    $navigation = [];
    $is_use_navigation = $this->configFactory->get('block.block.' . $block_id)->get('settings.visualization.settings.use_navigation');

    if ($is_use_navigation && !empty($entity)) {
      $navigation = $this->getNavigation($entity, $starting_entity_keyword, $sell_entity_keyword);
    }

    /* Prepare building description button */
    $description_btn = [];

    if (
      $sell_entity_keyword === 'building' && $entity_keyword === 'building' ||
      $sell_entity_keyword === 'building' && $entity_keyword === 'floor'
    ) {
      $description_btn = !empty($entity) ? $this->getBuildingDescriptionButton($entity) : [];
    }

    /* Prepare guide */
    $guide = [];

    if ($entity_keyword !== 'flat') {
      $guide = $this->getGuide($sell_entity_keyword, $entity_keyword);
    }

    /* Prepare back button */
    $back_btn = [];

    if ($entity_keyword !== $starting_entity_keyword) {
      $back_btn = $this->getBackButton();
    }

    /* Prepare ask for offer button */
    $ask_for_offer_btn = [];

    if ($sell_entity_keyword === 'building' && ($entity_keyword === 'building' || $entity_keyword === 'floor')) {
      $ask_for_offer_btn = $this->getAskForOfferButton($webform_id);
    }

    /* Prepare tooltip data */
    $legend = [];
    $tooltip_data = [];

    if (
      ($entity_keyword === 'floor' && $sell_entity_keyword === 'flat') ||
      ($entity_keyword === 'estate' && $sell_entity_keyword === 'building')
    ) {
      $related_entity_type = $entity_keyword === 'floor' ? 're_mgr_flat' : 're_mgr_building';
      $entity = $this->entityService->getEntityFromData($entity_keyword, $entity_id);

      if (!empty($entity)) {
        $related_entities = $this->entityService->getRelatedEntities($entity);
        $legend = $this->getLegend($related_entities);
        $svg_paths_data = $this->convertToSvgPathsWithStatus($svg_paths_data, $related_entity_type);
        $tooltip_data = $this->getRelatedEntitiesTooltipData($entity, $related_entity_type);
      }
    }

    $entity_description = [];
    $webform = [];

    /* Prepare flat view */
    if ($entity_keyword === 'flat') {
      $flat_entity = $this->entityService->getEntityFromData($entity_keyword, $entity_id);
      $entity_description = !empty($flat_entity) ? $this->getEntityDescription($flat_entity) : [];
      $webform = $this->getWebformView($webform_id);
    }

    /* Prepare block content */
    $block = [
      '#theme' => 'visualization_presentation',
      '#attached' => [
        'library' => ['re_mgr_visualization/global'],
      ],
      '#block_id' => $block_id,
      '#navigation' => $navigation,
      '#entity_description' => $entity_description,
      '#guide' => $guide,
      '#image' => [
        'width' => $main_image_data['width'],
        'height' => $main_image_data['height'],
        'url' => $main_image_data['url'],
        'entity_keyword' => $entity_keyword,
        'entity_id' => $entity_id,
        'path_fill' => $path_fill,
        'path_target_opacity' => $path_target_opacity,
        'starting_entity_keyword' => $starting_entity_keyword,
        'sell_entity_keyword' => $sell_entity_keyword,
        'webform_id' => $webform_id,
        'style' => $main_image_data['style'],
      ],
      '#paths' => $svg_paths_data,
      '#description_btn' => $description_btn,
      '#back_btn' => $back_btn,
      '#ask_for_offer_btn' => $ask_for_offer_btn,
      '#legend' => $legend,
      '#tooltip_data' => $tooltip_data,
      '#webform' => $webform,
      '#front_url' => Url::fromRoute('<front>'),
    ];

    return $block;
  }

}
