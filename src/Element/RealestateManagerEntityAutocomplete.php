<?php

namespace Drupal\re_mgr\Element;

use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an Real Estate Manager entity autocomplete form element.
 *
 * Field extend basic entity_autocomplete form element
 * by restrict to not final floor in floor reference and
 * rebuild entity preview by adding extra reference info
 * if they are any.
 *
 * @FormElement("re_mgr_entity_autocomplete")
 */
class RealestateManagerEntityAutocomplete extends EntityAutocomplete {

  /**
   * Change route serving autocomplete.
   *
   * @param array $element
   *   The form element to process. Properties used:
   *   - #target_type: The ID of the target entity type.
   *   - #selection_handler: The plugin ID of the entity reference selection
   *     handler.
   *   - #selection_settings: An array of settings that will be passed to the
   *     selection handler.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The form element.
   */
  public static function processEntityAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    $element = parent::processEntityAutocomplete($element, $form_state, $complete_form);
    $element['#autocomplete_route_name'] = 're_mgr.entity_autocomplete';

    return $element;
  }

}
