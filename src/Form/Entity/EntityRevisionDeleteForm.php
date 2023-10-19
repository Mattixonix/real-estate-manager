<?php

namespace Drupal\re_mgr\Form\Entity;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\re_mgr\Entity\EntityInterface;
use Drupal\re_mgr\Service\EntityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Real estate manager entities revision.
 */
class EntityRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Entity Type Manager service.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Date Formatter service.
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * The Entity service.
   */
  protected EntityServiceInterface $entityService;

  /**
   * The entity revision.
   */
  protected EntityInterface $revision;

  /**
   * Constructs a new EntityRevisionDeleteForm.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    DateFormatterInterface $date_formatter,
    EntityServiceInterface $entity_service
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->dateFormatter = $date_formatter;
    $this->entityService = $entity_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): EntityRevisionDeleteForm {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('date.formatter'),
      $container->get('re_mgr.entity_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 're_mgr_' . $this->entityService->getCurrentEntityKeyword() . '_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.re_mgr_' . $this->entityService->getCurrentEntityKeyword() . '.version_history', [
      're_mgr_' . $this->entityService->getCurrentEntityKeyword() => $this->revision->id(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(
    array $form,
    FormStateInterface $form_state,
    EntityInterface $re_mgr_estate_revision = NULL,
    EntityInterface $re_mgr_building_revision = NULL,
    EntityInterface $re_mgr_floor_revision = NULL,
    EntityInterface $re_mgr_flat_revision = NULL
  ) {
    $revisions = [
      $re_mgr_estate_revision,
      $re_mgr_building_revision,
      $re_mgr_floor_revision,
      $re_mgr_flat_revision,
    ];

    foreach ($revisions as $revision) {
      if ($revision) {
        $this->revision = $revision;
        break;
      }
    }

    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    if ($this->revision->getRevisionId()) {
      $vid = (int) $this->revision->getRevisionId();
    }
    else {
      throw new \exception('The revision id can\'t be null.');
    }

    /** @var \Drupal\Core\Entity\RevisionableStorageInterface */
    $revision_storage = $this->entityTypeManager->getStorage('re_mgr_' . $this->entityService->getCurrentEntityKeyword());
    $revision_storage->deleteRevision($vid);

    $this->messenger()
      ->addStatus($this->t('Revision from %revision-date %title has been deleted.', [
        '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
        '%title' => $this->revision->label(),
      ]));
    $form_state->setRedirect(
      'entity.re_mgr_' . $this->entityService->getCurrentEntityKeyword() . '.version_history',
      ['re_mgr_' . $this->entityService->getCurrentEntityKeyword() => $this->revision->id()]
    );
  }

}
