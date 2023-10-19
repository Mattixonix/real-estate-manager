<?php

namespace Drupal\re_mgr\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin of the 're_mgr_entity_reference_autocomplete' widget.
 *
 * @FieldWidget(
 *   id = "re_mgr_entity_reference_autocomplete",
 *   label = @Translation("Real estate manager autocomplete"),
 *   description = @Translation("An autocomplete text field for Real estate manager module entities."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class RealestateManagerEntityReferenceAutocompleteWidget extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['target_id']['#type'] = 're_mgr_entity_autocomplete';
    return $element;
  }

}
