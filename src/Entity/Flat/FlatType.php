<?php

namespace Drupal\re_mgr\Entity\Flat;

use Drupal\re_mgr\Entity\EntityTypeBase;

/**
 * Defines the Flat type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "re_mgr_flat_type",
 *   label = @Translation("Flat type"),
 *   label_collection = @Translation("Flat types"),
 *   label_singular = @Translation("flat type"),
 *   label_plural = @Translation("flat types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count flat type",
 *     plural = "@count flat types",
 *   ),
 *   bundle_of = "re_mgr_flat",
 *   config_prefix = "re_mgr_flat",
 *   admin_permission = "administer flat entity",
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
 *     "add-form" = "/admin/re-mgr/config/entities/flat/type/add",
 *     "edit-form" = "/admin/re-mgr/config/entities/flat/type/{re_mgr_flat_type}/edit",
 *     "delete-form" = "/admin/re-mgr/config/entities/flat/type/{re_mgr_flat_type}/delete",
 *     "collection" = "/admin/re-mgr/config/entities/flat/types"
 *   },
 * )
 */
class FlatType extends EntityTypeBase {}
