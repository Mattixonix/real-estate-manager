<?php

namespace Drupal\re_mgr\Entity\Floor;

use Drupal\re_mgr\Entity\EntityTypeBase;

/**
 * Defines the Floor type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "re_mgr_floor_type",
 *   label = @Translation("Floor type"),
 *   label_collection = @Translation("Floor types"),
 *   label_singular = @Translation("floor type"),
 *   label_plural = @Translation("floor types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count floor type",
 *     plural = "@count floor types",
 *   ),
 *   bundle_of = "re_mgr_floor",
 *   config_prefix = "re_mgr_floor",
 *   admin_permission = "administer floor entity",
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
 *     "add-form" = "/admin/re-mgr/config/entities/floor/type/add",
 *     "edit-form" = "/admin/re-mgr/config/entities/floor/type/{re_mgr_floor_type}/edit",
 *     "delete-form" = "/admin/re-mgr/config/entities/floor/type/{re_mgr_floor_type}/delete",
 *     "collection" = "/admin/re-mgr/config/entities/floor/types"
 *   },
 * )
 */
class FloorType extends EntityTypeBase {}
