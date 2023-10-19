<?php

namespace Drupal\re_mgr\Entity;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface as CoreEntityInterface;
use Drupal\Core\Entity\EntityListBuilder as CoreEntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\re_mgr\Service\EntityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for Real estate manager entities.
 */
class EntityListBuilder extends CoreEntityListBuilder {

  /**
   * The date formatter service.
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * The language manager service.
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * The Real estate manager Entity Service.
   */
  protected EntityServiceInterface $entityService;

  /**
   * Constructs a new EntityListBuilder object.
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    EntityStorageInterface $storage,
    DateFormatterInterface $date_formatter,
    LanguageManagerInterface $language_manager,
    EntityServiceInterface $entity_service
  ) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
    $this->languageManager = $language_manager;
    $this->entityService = $entity_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
      $container->get('language_manager'),
      $container->get('re_mgr.entity_service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['name'] = $this->t('Name');

    if (
      $this->entityTypeId === 're_mgr_flat' ||
      $this->entityTypeId === 're_mgr_building'
    ) {
      $header['status'] = $this->t('Status');
    }

    if ($this->entityTypeId !== 're_mgr_estate') {
      $entity_map = [
        're_mgr_flat' => $this->t('Floor'),
        're_mgr_floor' => $this->t('Building'),
        're_mgr_building' => $this->t('Estate'),
      ];
      $header['related'] = $entity_map[$this->entityTypeId];
    }

    $header['type'] = $this->t('Type');
    $header['author'] = $this->t('Author');
    $header['published'] = $this->t('Publication');
    $header['changed'] = $this->t('Updated');

    if ($this->languageManager->isMultilingual()) {
      $header['language_name'] = $this->t('Language');
    }

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(CoreEntityInterface $entity): array {
    /** @var \Drupal\re_mgr\Entity\EntityInterface $entity */
    $row['name'] = $entity->toLink();

    if (
      $this->entityTypeId === 're_mgr_flat' ||
      $this->entityTypeId === 're_mgr_building'
    ) {
      $status_definition = $entity->getFieldDefinition('status');
      /** @var array */
      $status_values = $status_definition ? $status_definition->getSetting('allowed_values') : NULL;
      $row['status'] = !empty($entity->status->value) ? $status_values[$entity->status->value] : NULL;
    }

    if ($this->entityTypeId !== 're_mgr_estate') {
      $row['related'] = $entity->getParentEntity() instanceof EntityInterface ? $entity->getParentEntity()->toLink() : NULL;
    }

    $row['type'] = $entity->bundle();
    $row['author']['data'] = [
      '#theme' => 'username',
      '#account' => $entity->getOwner(),
    ];
    $row['published'] = $entity->isPublished() ? $this->t('published') : $this->t('not published');
    $row['changed'] = $this->dateFormatter->format($entity->getChangedTime(), 'short');

    if ($this->languageManager->isMultilingual()) {
      $row['language_name'] = $this->languageManager->getLanguageName($entity->language()->getId());
    }

    return $row + parent::buildRow($entity);
  }

}
