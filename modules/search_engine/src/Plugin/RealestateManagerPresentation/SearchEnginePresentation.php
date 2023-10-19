<?php

namespace Drupal\re_mgr_search_engine\Plugin\RealestateManagerPresentation;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation\PresentationBase;

/**
 * Provides the search engine for Real estate manager data.
 *
 * @RealestateManagerPresentation(
 *   id = "search_engine",
 *   label = @Translation("Search engine"),
 * )
 */
class SearchEnginePresentation extends PresentationBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['description'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $this->t('Currently there are no plugin settings here.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel(): TranslatableMarkup {
    return $this->t('Search Engine');
  }

  /**
   * {@inheritdoc}
   */
  public function getContent(array $configuration, ?string $block_id): array {
    return [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => $this->t('Work on this search engine is ongoing.'),
    ];
  }

}
