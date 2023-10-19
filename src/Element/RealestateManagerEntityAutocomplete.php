<?php

namespace Drupal\re_mgr\Element;

use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an Real estate manager entity autocomplete form element.
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
   */
  public static function processEntityAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $element = parent::processEntityAutocomplete($element, $form_state, $complete_form);
    $element['#autocomplete_route_name'] = 're_mgr.entity_autocomplete';

    return $element;
  }

}
