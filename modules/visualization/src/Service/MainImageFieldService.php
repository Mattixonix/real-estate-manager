<?php

namespace Drupal\re_mgr_visualization\Service;

use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\re_mgr\Entity\EntityBaseDataTrait;

/**
 * Main image field service.
 */
class MainImageFieldService implements MainImageFieldServiceInterface {
  use StringTranslationTrait;
  use EntityBaseDataTrait;

  /**
   * The Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Entity Type Bundle Info service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected EntityTypeBundleInfoInterface $entityTypeBundleInfo;

  /**
   * The Display Repository service.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected EntityDisplayRepositoryInterface $displayRepository;

  /**
   * List of coordinated entities.
   *
   * @var array
   */
  protected array $entityList;

  /**
   * Constructs a MainImageFieldService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity Type Manager service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The Entity Type Bundle Info service.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository
   *   The Display Repository service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    EntityDisplayRepositoryInterface $display_repository,
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->displayRepository = $display_repository;
    $this->entityList = self::ENTITY_LIST;
  }

  /**
   * {@inheritdoc}
   */
  public function createMainImageField(): void {
    $field_name = 'main_image';

    foreach ($this->entityList as $entity_type_id) {
      if (empty(FieldStorageConfig::loadByName($entity_type_id, $field_name))) {
        $field_storage = FieldStorageConfig::create([
          'field_name'             => $field_name,
          'langcode'               => 'en',
          'entity_type'            => $entity_type_id,
          'type'                   => 'entity_reference',
          'locked'                 => TRUE,
          'required'               => TRUE,
          'cardinality'            => 1,
          'persist_with_no_fields' => FALSE,
          'custom_storage'         => FALSE,
          'settings'               => [
            'target_type' => 'media',
          ],
        ]);
        $field_storage->save();
      }

      $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_type_id);
      foreach ($bundles as $bundle_id => $bundle) {
        $field = FieldConfig::loadByName($entity_type_id, $bundle_id, $field_name);
        if (empty($field)) {
          FieldConfig::create([
            'field_name'   => $field_name,
            'description'  => $this->t('Main entity image used in visualization plugin.'),
            'entity_type'  => $entity_type_id,
            'bundle'       => $bundle_id,
            'label'        => $this->t('Main image'),
            'required' => TRUE,
            'settings'     => [
              'handler' => 'default',
              'handler_settings' => [
                'target_bundles' => [$entity_type_id],
              ],
            ],
          ])->save();

          /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface */
          $entity_bundle_form_display = $this->entityTypeManager
            ->getStorage('entity_form_display')
            ->load($entity_type_id . '.' . $bundle_id . '.default');
          if ($entity_bundle_form_display instanceof EntityFormDisplayInterface) {
            $entity_bundle_form_display
              ->setComponent($field_name, [
                'type' => 'entity_reference_autocomplete',
                'weight' => 86,
              ])
              ->save();
          }

          /** @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface */
          $entity_bundle_view_display = $this->entityTypeManager
            ->getStorage('entity_view_display')
            ->load($entity_type_id . '.' . $bundle_id . '.default');
          if ($entity_bundle_view_display instanceof EntityViewDisplayInterface) {
            $entity_bundle_view_display
              ->setComponent($field_name, [
                'label' => 'hidden',
                'type' => 'entity_reference_entity_view',
                'weight' => 101,
                'region' => 'content',
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
  public function deleteMainImageField(): void {
    $field_name = 'main_image';
    foreach ($this->entityList as $entity_type_id) {
      if (!empty($field_storage = FieldStorageConfig::loadByName($entity_type_id, $field_name))) {
        $field_storage->delete();
      }
    }
  }

}
