<?php

namespace Drupal\re_mgr\Controller;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Entity\EntityAutocompleteMatcherInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\Core\Site\Settings;
use Drupal\re_mgr\Entity\EntityInterface;
use Drupal\system\Controller\EntityAutocompleteController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Defines a route controller for entity autocomplete form elements.
 */
class RealestateManagerEntityAutocompleteController extends EntityAutocompleteController {

  /**
   * The Entity Repository revision.
   */
  protected EntityRepositoryInterface $entityRepository;

  /**
   * Constructs an RealestateManagerEntityAutocompleteController object.
   */
  public function __construct(
    EntityAutocompleteMatcherInterface $matcher,
    KeyValueStoreInterface $key_value,
    EntityTypeManagerInterface $entity_type_manager,
    EntityRepositoryInterface $entity_repository
  ) {
    parent::__construct($matcher, $key_value);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): RealestateManagerEntityAutocompleteController {
    return new static(
      $container->get('entity.autocomplete_matcher'),
      $container->get('keyvalue')->get('entity_autocomplete'),
      $container->get('entity_type.manager'),
      $container->get('entity.repository')
    );
  }

  /**
   * Rebuild autocomplete method to change match function.
   */
  public function handleAutocomplete(Request $request, $target_type, $selection_handler, $selection_settings_key) {
    $matches = [];

    if ($request->query->get('q') !== '') {
      $input = $request->query->get('q');
      /** @var string $input */
      $tag_list = Tags::explode($input);
      $typed_string = !empty($tag_list) ? mb_strtolower(array_pop($tag_list)) : '';
      $selection_settings = $this->keyValue->get($selection_settings_key, FALSE);

      if ($selection_settings !== FALSE) {
        $selection_settings_hash = Crypt::hmacBase64(serialize($selection_settings) . $target_type . $selection_handler, Settings::getHashSalt());
        if (!hash_equals($selection_settings_hash, $selection_settings_key)) {
          throw new AccessDeniedHttpException('Invalid selection settings value$value.');
        }
      }
      else {
        throw new AccessDeniedHttpException();
      }

      /** @var array $selection_settings */
      $matches = $this->getMatches($target_type, $selection_settings, $typed_string);
    }

    return new JsonResponse($matches);
  }

  /**
   * Gets matched labels based on a given search string.
   */
  public function getMatches(string $target_type, array $selection_settings, string $string = ''): array {
    $matches = [];

    if ($string !== '') {
      $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
      $match_limit = isset($selection_settings['match_limit']) ? (int) $selection_settings['match_limit'] : 10;
      $entity_labels = $this->getReferenceableEntities($string, $match_operator, $match_limit, $target_type);

      foreach ($entity_labels as $values) {
        foreach ($values as $entity_id => $label) {
          $value = "$label ($entity_id)";
          /** @var string */
          $value = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($value)))));
          $value = Tags::encode($value);
          $matches[] = ['value' => $value, 'label' => $label];
        }
      }
    }

    return $matches;
  }

  /**
   * Builds an EntityQuery to get referenceable entities with limitations.
   */
  public function getReferenceableEntities(string $match = NULL, string $match_operator = 'CONTAINS', int $limit = 0, string $target_type = ''): array {
    /** @var \Drupal\Core\Entity\EntityTypeInterface */
    $entity_type = $this->entityTypeManager->getDefinition($target_type);
    $query = $this->entityTypeManager
      ->getStorage($target_type)
      ->getQuery()
      ->accessCheck(TRUE);

    if (isset($match) && $label_key = $entity_type->getKey('label')) {
      $query->condition($label_key, $match, $match_operator);
    }

    if ($limit > 0) {
      $query->range(0, $limit);
    }

    if ($target_type === 're_mgr_floor') {
      $query->condition('is_final', 0, '=');
    }

    $query->addTag($target_type . '_access');
    $query->addMetaData('entity_reference_selection_handler', $this);
    $results = $query->execute();

    if (empty($results)) {
      return [];
    }

    $options = [];
    /** @var array $results */
    $entities = $this->entityTypeManager->getStorage($target_type)->loadMultiple($results);

    foreach ($entities as $entity_id => $entity) {
      /** @var \Drupal\re_mgr\Entity\EntityInterface $entity */
      $bundle = $entity->bundle();
      $entity_preview = $this->t('Name@suffix', ['@suffix' => ':'], ['context' => 'autocomplete']) . ' ' . Html::escape($this->entityRepository->getTranslationFromContext($entity)->label() ?? '');

      /* Add related to information */
      if (in_array($target_type, ['re_mgr_floor', 're_mgr_building'])) {
        $field_name = 'building_id';

        if ($target_type === 're_mgr_building') {
          $field_name = 'estate_id';
        }

        $related_entity = $entity->get($field_name)->entity;

        if ($related_entity instanceof EntityInterface) {
          $related_entity_label = $related_entity->label();
          $entity_preview .= ' | ' . $this->t('Related to@suffix', ['@suffix' => ':']) . ' ' . $related_entity_label;
        }
      }

      $options[$bundle][$entity_id] = $entity_preview;
    }

    return $options;
  }

}
