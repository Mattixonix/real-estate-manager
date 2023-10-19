<?php

namespace Drupal\re_mgr\Entity\Estate;

use Drupal\re_mgr\Entity\EntityTypeBase;

/**
 * Defines the Estate type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "re_mgr_estate_type",
 *   label = @Translation("Estate type"),
 *   label_collection = @Translation("Estate types"),
 *   label_singular = @Translation("estate type"),
 *   label_plural = @Translation("estate types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count estate type",
 *     plural = "@count estate types",
 *   ),
 *   bundle_of = "re_mgr_estate",
 *   config_prefix = "re_mgr_estate",
 *   admin_permission = "administer estate entity",
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
 *     "add-form" = "/admin/re-mgr/config/entities/estate/type/add",
 *     "edit-form" = "/admin/re-mgr/config/entities/estate/type/{re_mgr_estate_type}/edit",
 *     "delete-form" = "/admin/re-mgr/config/entities/estate/type/{re_mgr_estate_type}/delete",
 *     "collection" = "/admin/re-mgr/config/entities/estate/types"
 *   },
 * )
 */
class EstateType extends EntityTypeBase {}
