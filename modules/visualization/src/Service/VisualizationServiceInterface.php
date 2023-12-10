<?php

namespace Drupal\re_mgr_visualization\Service;

use Drupal\re_mgr\Entity\EntityInterface;

/**
 * Defines the interface for visualization service.
 */
interface VisualizationServiceInterface {

  /**
   * Get starting entity.
   *
   * @param array $configuration
   *   The configuration.
   *
   * @return \Drupal\re_mgr\Entity\EntityInterface|null
   *   The entity.
   */
  public function getStartingEntity(array $configuration): ?EntityInterface;

  /**
   * Get entity main image data.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface $entity
   *   The entity.
   * @param string|null $main_image_style
   *   Main image style.
   *
   * @return array
   *   Main image data.
   */
  public function getEntityMainImageData(EntityInterface $entity, string $main_image_style = NULL): array;

  /**
   * Get related entity svg data.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return array
   *   Related entity svg data.
   */
  public function getRelatedEntitySvgData(EntityInterface $entity): array;

  /**
   * Get filter target opacity.
   *
   * @param int $target_opacity
   *   Target opacity.
   *
   * @return string
   *   Converted target opacity.
   */
  public function getTargetOpacity(int $target_opacity): string;

  /**
   * Returns Entity legend.
   *
   * @param array $related_entities
   *   The related entities.
   *
   * @return array
   *   The legend.
   */
  public function getLegend(array $related_entities): array;

  /**
   * Convert normal svg path data by adding flat status.
   *
   * @param array $svg_paths_data
   *   Svg paths data.
   * @param string $related_entity_type
   *   The related entity type.
   *
   * @return array
   *   Converted paths statuses.
   */
  public function convertToSvgPathsWithStatus(array $svg_paths_data, string $related_entity_type): array;

  /**
   * Get related entities tooltip data.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface $entity
   *   The entity.
   * @param string $related_entity_type
   *   The related entity type.
   *
   * @return array
   *   Related entities tooltip data.
   */
  public function getRelatedEntitiesTooltipData(EntityInterface $entity, string $related_entity_type): array;

  /**
   * Get Entity description.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return array
   *   The entity description.
   */
  public function getEntityDescription(EntityInterface $entity): array;

  /**
   * Get block webform.
   *
   * @param string $webform_id
   *   The webform id.
   *
   * @return array
   *   The webform view.
   */
  public function getWebformView(string $webform_id): array;

  /**
   * Get navigation options.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface $entity
   *   The entity.
   * @param string $starting_entity_keyword
   *   The entity keyword.
   * @param string $sell_entity_keyword
   *   Sell entity keyword.
   *
   * @return array
   *   The navigation options.
   */
  public function getNavigationOptions(EntityInterface $entity, string $starting_entity_keyword, string $sell_entity_keyword): array;

  /**
   * Get navigation block as render array.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface $entity
   *   The entity.
   * @param string $starting_entity_keyword
   *   The entity keyword.
   * @param string $sell_entity_keyword
   *   Sell entity keyword.
   *
   * @return array
   *   The navigation.
   */
  public function getNavigation(EntityInterface $entity, string $starting_entity_keyword, string $sell_entity_keyword): array;

  /**
   * Get building description button.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return array
   *   Building description button.
   */
  public function getBuildingDescriptionButton(EntityInterface $entity): array;

  /**
   * Get guide.
   *
   * @param string $sell_entity_keyword
   *   Sell entity keyword.
   * @param string $entity_keyword
   *   The entity keyword.
   *
   * @return array
   *   The guide.
   */
  public function getGuide(string $sell_entity_keyword, string $entity_keyword): array;

  /**
   * Get back button.
   *
   * @return array
   *   Back button.
   */
  public function getBackButton(): array;

  /**
   * Get ask for offer button.
   *
   * @param string $webform_id
   *   The webform id.
   *
   * @return array
   *   Ask for offer button.
   */
  public function getAskForOfferButton(string $webform_id): array;

  /**
   * Build block content.
   *
   * @param string $block_id
   *   The block id.
   * @param string $entity_keyword
   *   The entity keyword.
   * @param string $entity_id
   *   The entity id.
   * @param array $main_image_data
   *   Main image data.
   * @param array $svg_paths_data
   *   Svg paths data.
   * @param string $path_fill
   *   Svg path fill color.
   * @param string $path_target_opacity
   *   Path target opacity.
   * @param string $starting_entity_keyword
   *   The starting entity keyword.
   * @param string $sell_entity_keyword
   *   Sell entity keyword.
   * @param string $webform_id
   *   Webform id.
   *
   * @return array
   *   Content render array.
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
  ): array;

}
