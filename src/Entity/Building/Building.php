<?php

namespace Drupal\re_mgr\Entity\Building;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\re_mgr\Entity\EntityBase;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Building entity.
 *
 * @ContentEntityType(
 *   id = "re_mgr_building",
 *   label = @Translation("Building"),
 *   label_collection = @Translation("Buildings"),
 *   label_singular = @Translation("building"),
 *   label_plural = @Translation("buildings"),
 *   label_count = @PluralTranslation(
 *     singular = "@count building",
 *     plural = "@count buildings",
 *   ),
 *   bundle_label = @Translation("Building type"),
 *   base_table = "re_mgr_building",
 *   data_table = "re_mgr_building_field",
 *   revision_table = "re_mgr_building_revision",
 *   revision_data_table = "re_mgr_building_field_revision",
 *   show_revision_ui = TRUE,
 *   admin_permission = "administer building entity",
 *   permission_granularity = "bundle",
 *   fieldable = TRUE,
 *   field_ui_base_route = "entity.re_mgr_building_type.edit_form",
 *   translatable = TRUE,
 *   bundle_entity_type = "re_mgr_building_type",
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\re_mgr\Entity\EntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\re_mgr\Entity\EntityAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\re_mgr\Form\Entity\EntityForm",
 *       "delete" = "Drupal\re_mgr\Form\Entity\EntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\re_mgr\Form\Entity\EntityDeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\entity\Routing\AdminHtmlRouteProvider",
 *       "revisions" = "Drupal\entity\Routing\RevisionRouteProvider",
 *     },
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "owner" = "uid",
 *     "uuid" = "uuid",
 *     "label" = "name",
 *     "langcode" = "langcode",
 *     "revision" = "vid",
 *     "published" = "published",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   },
 *   links = {
 *     "add-page" = "/admin/re-mgr/content/building/add",
 *     "add-form" = "/admin/re-mgr/content/building/add/{re_mgr_building_type}",
 *     "edit-form" = "/admin/re-mgr/content/building/{re_mgr_building}/edit",
 *     "delete-form" = "/admin/re-mgr/content/building/{re_mgr_building}/delete",
 *     "delete-multiple-form" = "/admin/re-mgr/content/buildings/delete",
 *     "collection" = "/admin/re-mgr/content/buildings",
 *     "canonical" = "/building/{re_mgr_building}",
 *     "version-history" = "/admin/re-mgr/content/building/{re_mgr_building}/revisions",
 *     "revision" = "/admin/re-mgr/content/building/{re_mgr_building}/revision/{re_mgr_building_revision}/view",
 *     "revision-revert-form" = "/admin/re-mgr/content/building/{re_mgr_building}/revision/{re_mgr_building_revision}/revert",
 *     "revision-delete-form" = "/admin/re-mgr/content/building/{re_mgr_building}/revision/{re_mgr_building_revision}/delete",
 *   },
 * )
 */
class Building extends EntityBase {
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    /** @var array $fields */
    $fields['uid']
      ->setLabel(t('Author'))
      ->setDescription(t('The Name of the associated user.'))
      ->setSetting('handler', 'default')
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'region' => 'hidden',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time when the building was created.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time when the building was last edited.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['path'] = BaseFieldDefinition::create('path')
      ->setLabel(t('URL alias'))
      ->setDisplayOptions('form', [
        'type' => 'path',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setComputed(TRUE);

    $fields['published']
      ->setLabel(t('Published'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Building entity.'))
      ->setSettings([
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['estate_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Estate'))
      ->setDescription(t('The name of the associated estate.'))
      ->setSetting('target_type', 're_mgr_estate')
      ->setSetting('handler', 'default')
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'region' => 'hidden',
      ])
      ->setDisplayOptions('form', [
        'type' => 're_mgr_entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('list_integer')
      ->setSettings([
        'allowed_values' => [
          1 => t('Available'),
          2 => t('Reserved'),
          3 => t('Sold'),
        ],
      ])
      ->setDefaultValue(1)
      ->setLabel(t('Status'))
      ->setDescription(t('Status of the building.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
