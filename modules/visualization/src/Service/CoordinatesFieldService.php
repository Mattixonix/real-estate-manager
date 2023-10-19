<?php

namespace Drupal\re_mgr_visualization\Service;

use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\re_mgr\Entity\EntityBaseDataTrait;

/**
 * Coordinates field service.
 */
class CoordinatesFieldService implements CoordinatesFieldServiceInterface {
  use StringTranslationTrait;
  use EntityBaseDataTrait;

  /**
   * The Entity Type Manager service.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Entity Type Bundle Info service.
   */
  protected EntityTypeBundleInfoInterface $entityTypeBundleInfo;

  /**
   * The Display Repository service.
   */
  protected EntityDisplayRepositoryInterface $displayRepository;

  /**
   * List of coordinated entities.
   */
  protected array $coordinatedEntities;

  /**
   * Constructs a CoordinatesFieldService object.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    EntityDisplayRepositoryInterface $display_repository,
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->displayRepository = $display_repository;
    $coordinated_entities = self::ENTITY_LIST;
    array_shift($coordinated_entities);
    $this->coordinatedEntities = $coordinated_entities;
  }

  /**
   * {@inheritdoc}
   */
  public function createCoordinatesField(): void {
    $field_name = 'coordinates';

    foreach ($this->coordinatedEntities as $entity_type_id) {
      if (empty(FieldStorageConfig::loadByName($entity_type_id, $field_name))) {
        $field_storage = FieldStorageConfig::create([
          'field_name'             => $field_name,
          'langcode'               => 'en',
          'entity_type'            => $entity_type_id,
          'type'                   => 'string_long',
          'module'                 => 'text',
          'locked'                 => TRUE,
          'cardinality'            => 1,
          'translatable'           => TRUE,
          'persist_with_no_fields' => FALSE,
          'custom_storage'         => FALSE,
        ]);
        $field_storage->save();
      }

      $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_type_id);

      foreach ($bundles as $bundle_id => $bundle) {
        $field = FieldConfig::loadByName($entity_type_id, $bundle_id, $field_name);

        if (empty($field)) {
          FieldConfig::create([
            'field_name'   => $field_name,
            'entity_type'  => $entity_type_id,
            'bundle'       => $bundle_id,
            'label'        => $this->t('Coordinates'),
            'translatable' => TRUE,
          ])->save();

          /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface */
          $entity_bundle_display = $this->entityTypeManager
            ->getStorage('entity_form_display')
            ->load($entity_type_id . '.' . $bundle_id . '.default');

          if ($entity_bundle_display instanceof EntityFormDisplayInterface) {
            $entity_bundle_display
              ->setComponent($field_name, [
                'type' => 'string_textarea',
                'weight' => 87,
              ])
              ->save();
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteCoordinatesField(): void {
    $field_name = 'coordinates';

    foreach ($this->coordinatedEntities as $entity_type_id) {
      if (!empty($field_storage = FieldStorageConfig::loadByName($entity_type_id, $field_name))) {
        $field_storage->delete();
      }
    }
  }

}
