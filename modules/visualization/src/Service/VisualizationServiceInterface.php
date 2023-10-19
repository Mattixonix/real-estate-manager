<?php

namespace Drupal\re_mgr_visualization\Service;

use Drupal\re_mgr\Entity\EntityInterface;

/**
 * Defines the interface for visualization service.
 */
interface VisualizationServiceInterface {

  /**
   * Get starting entity.
   */
  public function getStartingEntity(array $configuration): ?EntityInterface;

  /**
   * Get entity main image data.
   */
  public function getEntityMainImageData(EntityInterface $entity, string $main_image_style = NULL): array;

  /**
   * Get related entity svg data.
   */
  public function getRelatedEntitySvgData(EntityInterface $entity): array;

  /**
   * Get filter target opacity.
   */
  public function getTargetOpacity(int $target_opacity): string;

  /**
   * Returns Entity legend.
   */
  public function getLegend(array $related_entities): array;

  /**
   * Convert normal svg path data by adding flat status.
   */
  public function convertToSvgPathsWithStatus(array $svg_paths_data, string $related_entity_type): array;

  /**
   * Get related entities tooltip data.
   */
  public function getRelatedEntitiesTooltipData(EntityInterface $re_mgr_entity, string $related_entity_type): array;

  /**
   * Get Entity description.
   */
  public function getEntityDescription(EntityInterface $re_mgr_entity): array;

  /**
   * Get block webform.
   */
  public function getWebformView(string $webform_id): array;

  /**
   * Get navigation options.
   */
  public function getNavigationOptions(EntityInterface $re_mgr_entity, string $starting_entity_name, string $sell_entity_name): array;

  /**
   * Get navigation block as render array.
   */
  public function getNavigation(EntityInterface $entity, string $starting_entity_keyword, string $sell_entity_keyword): array;

  /**
   * Get building description button.
   */
  public function getBuildingDescriptionButton(EntityInterface $entity): array;

  /**
   * Get guide.
   */
  public function getGuide(string $sell_entity_keyword, string $entity_keyword): array;

  /**
   * Get back button.
   */
  public function getBackButton(): array;

  /**
   * Get ask for offer button.
   */
  public function getAskForOfferButton(string $webform_id): array;

  /**
   * Build block content.
   */
  public function buildContent(
    string $block_id,
    string $entity_name,
    string $entity_id,
    array $main_image_data,
    array $svg_paths_data,
    string $fill,
    string $target_opacity,
    string $starting_entity_type,
    string $sell_building,
    string $webform_id
  ): array;

}
