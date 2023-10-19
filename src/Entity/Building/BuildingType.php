<?php

namespace Drupal\re_mgr\Entity\Building;

use Drupal\re_mgr\Entity\EntityTypeBase;

/**
 * Defines the Building type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "re_mgr_building_type",
 *   label = @Translation("Building type"),
 *   label_collection = @Translation("Building types"),
 *   label_singular = @Translation("building type"),
 *   label_plural = @Translation("building types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count building type",
 *     plural = "@count building types",
 *   ),
 *   bundle_of = "re_mgr_building",
 *   config_prefix = "re_mgr_building",
 *   admin_permission = "administer building entity",
 *   handlers = {
 *     "list_builder" = "Drupal\re_mgr\Entity\EntityTypeListBuilder",
 *     "form" = {
 *       "default" = "Drupal\re_mgr\Form\Entity\EntityTypeForm",
 *       "delete" = "Drupal\re_mgr\Form\Entity\EntityTypeDeleteForm",
 *     },
 *     "local_task_provider" = {
 *       "default" = "Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "description" = "description"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "new_revision",
 *   },
 *   links = {
 *     "add-form" = "/admin/re-mgr/config/entities/building/type/add",
 *     "edit-form" = "/admin/re-mgr/config/entities/building/type/{re_mgr_building_type}/edit",
 *     "delete-form" = "/admin/re-mgr/config/entities/building/type/{re_mgr_building_type}/delete",
 *     "collection" = "/admin/re-mgr/config/entities/building/types"
 *   },
 * )
 */
class BuildingType extends EntityTypeBase {}
