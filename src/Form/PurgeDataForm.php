<?php

namespace Drupal\re_mgr\Form;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\re_mgr\Entity\EntityBaseDataTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form used to delete all module data.
 */
class PurgeDataForm extends ConfirmFormBase {
  use EntityBaseDataTrait;

  /**
   * The Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Date Formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * The Module Handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * Constructs a new PurgeDataForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity Type Manager service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The Date Formatter service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The Module Handler service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    DateFormatterInterface $date_formatter,
    ModuleHandlerInterface $module_handler,
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->dateFormatter = $date_formatter;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): PurgeDataForm {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('date.formatter'),
      $container->get('module_handler'),
      $container->get('re_mgr.entity_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 're_mgr_data_purge';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t("Are you sure you want purge all module's data?");
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t("Purge all entities and related media in preparation for uninstalling the <i>Real Estate Manager</i> module. <strong>Remember! This action cannot be undone.</strong>");
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('re_mgr.admin');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    foreach (self::ENTITY_LIST as $type) {
      /* Delete entities of given type */
      $entity_storage = $this->entityTypeManager->getStorage($type);
      $entities = $entity_storage->loadMultiple();
      $entity_storage->delete($entities);

      /* Delete media of given type */
      if ($this->moduleHandler->moduleExists('media')) {
        $media_storage = $this->entityTypeManager->getStorage('media');
        $result = $media_storage
          ->getQuery()
          ->accessCheck(TRUE)
          ->condition('bundle', $type, '=')
          ->execute();
        $entity_all_media = $media_storage->loadMultiple($result);
        $media_storage->delete($entity_all_media);
      }
    }

    $this->messenger()->addMessage($this->t("All module's data has been purged. You're now free to uninstall it."));
    $form_state->setRedirect('re_mgr.admin');
  }

}
