<?php

namespace Drupal\re_mgr_visualization\Plugin\RealestateManagerPresentation;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation\PresentationBase;
use Drupal\re_mgr_visualization\Service\VisualizationServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the visualization presentation on images.
 *
 * @RealestateManagerPresentation(
 *   id = "visualization",
 *   label = @Translation("Visualization"),
 * )
 */
class VisualizationPresentation extends PresentationBase implements ContainerFactoryPluginInterface {

  /**
   * The Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Visualization service.
   *
   * @var \Drupal\re_mgr_visualization\Service\VisualizationServiceInterface
   */
  protected VisualizationServiceInterface $visualizationService;

  /**
   * Constructs a new VisualizationPresentation.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity Type Manager service.
   * @param \Drupal\re_mgr_visualization\Service\VisualizationServiceInterface $visualization_service
   *   The Visualization service.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    array $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    VisualizationServiceInterface $visualization_service,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->visualizationService = $visualization_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): VisualizationPresentation {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('re_mgr_visualization.visualization_service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);
    $default_estate = NULL;
    $default_building = NULL;
    $default_webform = NULL;
    $default_image_style = NULL;

    if (isset($this->configuration['starting_estate']) && !empty($this->configuration['starting_estate'])) {
      $default_estate = $this->entityTypeManager->getStorage('re_mgr_estate')->load($this->configuration['starting_estate']);
    }
    if (isset($this->configuration['start_from_building']) && !empty($this->configuration['start_from_building'])) {
      $default_building = $this->entityTypeManager->getStorage('re_mgr_building')->load($this->configuration['starting_building']);
    }
    if (isset($this->configuration['webform']) && !empty($this->configuration['webform'])) {
      $default_webform = $this->entityTypeManager->getStorage('webform')->load($this->configuration['webform']);
    }
    if (isset($this->configuration['main_image_style']) && !empty($this->configuration['main_image_style'])) {
      $default_image_style = $this->entityTypeManager->getStorage('image_style')->load($this->configuration['main_image_style']);
    }

    $form['#attached']['library'][] = 're_mgr_visualization/visualization_config_form';
    $form['start_from_building'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Start display from building'),
      '#default_value' => $this->configuration['start_from_building'] ?? 0,
      '#attributes' => ['id' => 'edit-settings-visualization-settings-content-start-from-building'],
    ];
    $form['starting_estate'] = [
      '#type' => 're_mgr_entity_autocomplete',
      '#target_type' => 're_mgr_estate',
      '#selection_handler' => 'default',
      '#default_value' => $default_estate,
      '#title' => $this->t('Select starting estate'),
      '#description' => $this->t('Choose starting display estate.'),
      '#states' => [
        'visible' => [
          ':input[id="edit-settings-visualization-settings-content-start-from-building"]' => ['checked' => FALSE],
        ],
      ],
    ];
    $form['starting_building'] = [
      '#type' => 're_mgr_entity_autocomplete',
      '#target_type' => 're_mgr_building',
      '#selection_handler' => 'default',
      '#default_value' => $default_building,
      '#title' => $this->t('Select starting building'),
      '#description' => $this->t('Choose starting display building.'),
      '#states' => [
        'visible' => [
          ':input[id="edit-settings-visualization-settings-content-start-from-building"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['sell_buildings'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Sell buildings'),
      '#default_value' => $this->configuration['sell_buildings'] ?? 0,
      '#description' => $this->t('If disable, flats are sold.'),
    ];
    $form['use_navigation'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use navigation'),
      '#default_value' => $this->configuration['use_navigation'] ?? 1,
      '#description' => $this->t('If checked, navigation will be use.'),
    ];
    $form['webform'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'webform',
      '#selection_handler' => 'default',
      '#default_value' => $default_webform,
      '#title' => $this->t('Select webform'),
      '#description' => $this->t('Choose webform to get leads.'),
      '#required' => TRUE,
    ];
    $form['main_image_style'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'image_style',
      '#selection_handler' => 'default',
      '#default_value' => $default_image_style,
      '#title' => $this->t('Select main images style'),
      '#description' => $this->t('Optional, choose style to apply on main images.'),
    ];
    $form['fill'] = [
      '#type' => 'textfield',
      '#size' => 10,
      '#maxlength' => 6,
      '#pattern' => '^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
      '#default_value' => $this->configuration['fill'] ?? NULL,
      '#title' => $this->t('Enter svg paths fill color'),
      '#description' => $this->t('Color in HEX format without "#" prefix. Default is #FBD71A.'),
    ];
    $form['target_opacity'] = [
      '#type' => 'number',
      '#size' => 10,
      '#maxlength' => 3,
      '#min' => 0,
      '#max' => 100,
      '#default_value' => $this->configuration['target_opacity'] ?? NULL,
      '#title' => $this->t('Enter svg paths target opacity'),
      '#description' => $this->t('Numbers from 0 to 100, to set svg path target opacity while hovering on it. Example 50 = 0.5, default is 30.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state): void {
    /** @var array */
    $values = $form_state->getValues();

    /* Empty estate entity error */
    if ($values['start_from_building'] === 0 && empty($values['starting_estate'])) {
      $form_state->setErrorByName('starting_estate', $this->t('This field is required.'));
    }

    /* Empty building entity error */
    if ($values['start_from_building'] === 1 && empty($values['starting_building'])) {
      $form_state->setErrorByName('starting_building', $this->t('This field is required.'));
    }

    /* Fill color validation */
    if (!empty($values['fill']) && !preg_match('/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $values['fill'])) {
      $form_state->setErrorByName('fill', $this->t('Field must contain hex format color without "#" prefix.'));
    }

    /* Target opacity validation */
    if (!empty($values['target_opacity']) && !preg_match('/^[0-9]$|^[1-9][0-9]$|^(100)$/', $values['target_opacity'])) {
      $form_state->setErrorByName('target_opacity', $this->t('Field must contain number from 0 to 100.'));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration = $form_state->getValues();
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel(): TranslatableMarkup {
    return $this->t('Visualization');
  }

  /**
   * {@inheritdoc}
   */
  public function getContent(array $configuration, ?string $block_id): array {
    if (empty($configuration['settings']) || !$configuration['switch'] || $block_id === NULL) {
      return [];
    }

    $configuration = $configuration['settings'];

    /* Prepare block data */
    $starting_entity = $this->visualizationService->getStartingEntity($configuration);

    if (empty($starting_entity)) {
      return [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $this->t('Starting entity is missing, you need to rebuild this block with new data.'),
      ];
    }

    $main_image_data = $this->visualizationService->getEntityMainImageData($starting_entity, $configuration['main_image_style']);

    if (empty($main_image_data)) {
      return [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $this->t('Media is missing, you need to rebuild this block with new data.'),
      ];
    }

    $svg_paths_data = $this->visualizationService->getRelatedEntitySvgData($starting_entity);
    $path_fill = $configuration['fill'] ?: 'FBD71A';
    $path_target_opacity = !empty($configuration['target_opacity']) ? $this->visualizationService->getTargetOpacity($configuration['target_opacity']) : '0.3';

    /* Build block */
    $block = $this->visualizationService->buildContent(
      $block_id,
      $starting_entity->getEntityKeyword(),
      (string) $starting_entity->id(),
      $main_image_data,
      $svg_paths_data,
      $path_fill,
      $path_target_opacity,
      $starting_entity->getEntityKeyword(),
      $configuration['sell_buildings'] ? "building" : "flat",
      $configuration['webform'],
    );

    $block['#cache'] = [
      'tags' => [
        're_mgr_building_list',
        're_mgr_floor_list',
      ],
    ];

    return $block;
  }

}
