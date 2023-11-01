<?php

namespace Drupal\re_mgr_visualization\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\re_mgr\Entity\EntityBaseDataTrait;
use Drupal\re_mgr\Entity\EntityInterface;
use Drupal\re_mgr\Service\EntityServiceInterface;
use Drupal\re_mgr_visualization\Service\VisualizationServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides endpoint with ajax visualization steps.
 */
class AjaxVisualizationSteps extends ControllerBase {
  use EntityBaseDataTrait;

  /**
   * The Visualization service.
   */
  protected VisualizationServiceInterface $visualizationService;

  /**
   * The Real Estate Manager Entity service.
   */
  protected EntityServiceInterface $entityService;

  /**
   * Constructs a AjaxVisualizationSteps object.
   */
  public function __construct(
    VisualizationServiceInterface $visualizationService,
    EntityServiceInterface $entity_service
  ) {
    $this->visualizationService = $visualizationService;
    $this->entityService = $entity_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): AjaxVisualizationSteps {
    return new static(
      $container->get('re_mgr_visualization.visualization_service'),
      $container->get('re_mgr.entity_service'),
    );
  }

  /**
   * Ajax function for visualization block activate on next step.
   */
  public function nextStep(
    string $block_id,
    string $current_entity_keyword,
    string $chosen_entity_id,
    string $path_fill,
    string $path_target_opacity,
    string $starting_entity_keyword,
    string $sell_entity_keyword,
    string $webform_id,
    string $main_image_style = NULL
  ): AjaxResponse {

    /* Prepare block data */
    $related_entity_type_id = self::RELATED_ENTITY_MAP['re_mgr_' . $current_entity_keyword];
    $related_entity_keyword = $this->entityService->getEntityKeywordFromString($related_entity_type_id);

    if (empty($related_entity_keyword)) {
      return new AjaxResponse();
    }

    $chosen_entity = $this->entityService->getEntityFromData($related_entity_keyword, $chosen_entity_id);

    if (!$chosen_entity instanceof EntityInterface) {
      return new AjaxResponse();
    }

    $main_image_data = $this->visualizationService->getEntityMainImageData($chosen_entity, $main_image_style);
    $svg_paths_data = $this->visualizationService->getRelatedEntitySvgData($chosen_entity);

    /* Build block */
    $block = $this->visualizationService->buildContent(
      $block_id,
      $related_entity_keyword,
      $chosen_entity_id,
      $main_image_data,
      $svg_paths_data,
      $path_fill,
      $path_target_opacity,
      $starting_entity_keyword,
      $sell_entity_keyword,
      $webform_id
    );

    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#visualization-svg-container--' . $block_id, $block));

    return $response;
  }

  /**
   * Ajax function for visualization block activate on prev step.
   */
  public function prevStep(
    string $block_id,
    string $current_entity_keyword,
    string $current_entity_id,
    string $path_fill,
    string $path_target_opacity,
    string $starting_entity_keyword,
    string $sell_entity_keyword,
    string $webform_id,
    string $main_image_style = NULL
  ): AjaxResponse {

    /* Prepare block data */
    $current_entity = $this->entityService->getEntityFromData($current_entity_keyword, $current_entity_id);

    if (!$current_entity instanceof EntityInterface) {
      return new AjaxResponse();
    }

    /** @var \Drupal\re_mgr\Entity\EntityInterface */
    $parent_entity = $current_entity->getParentEntity();
    $main_image_data = $this->visualizationService->getEntityMainImageData($parent_entity, $main_image_style);
    $svg_paths_data = $this->visualizationService->getRelatedEntitySvgData($parent_entity);

    /* Build block */
    $block = $this->visualizationService->buildContent(
      $block_id,
      $parent_entity->getEntityKeyword(),
      (string) $parent_entity->id(),
      $main_image_data,
      $svg_paths_data,
      $path_fill,
      $path_target_opacity,
      $starting_entity_keyword,
      $sell_entity_keyword,
      $webform_id
    );

    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#visualization-svg-container--' . $block_id, $block));

    return $response;
  }

  /**
   * Ajax function for visualization block activate on navigation.
   */
  public function changeStep(
    string $block_id,
    string $selected_entity_keyword,
    string $selected_entity_id,
    string $path_fill,
    string $path_target_opacity,
    string $starting_entity_keyword,
    string $sell_entity_keyword,
    string $webform_id,
    string $main_image_style = NULL
  ): AjaxResponse {

    /* Prepare block data */
    $selected_entity = $this->entityService->getEntityFromData($selected_entity_keyword, $selected_entity_id);

    if (!$selected_entity instanceof EntityInterface) {
      return new AjaxResponse();
    }

    $main_image_data = $this->visualizationService->getEntityMainImageData($selected_entity, $main_image_style);
    $svg_paths_data = $this->visualizationService->getRelatedEntitySvgData($selected_entity);

    /* Build block */
    $block = $this->visualizationService->buildContent(
      $block_id,
      $selected_entity_keyword,
      $selected_entity_id,
      $main_image_data,
      $svg_paths_data,
      $path_fill,
      $path_target_opacity,
      $starting_entity_keyword,
      $sell_entity_keyword,
      $webform_id
    );

    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#visualization-svg-container--' . $block_id, $block));

    return $response;
  }

}
