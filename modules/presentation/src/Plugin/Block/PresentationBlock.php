<?php

namespace Drupal\re_mgr_presentation\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\re_mgr_presentation\Manager\PresentationManager;
use Drupal\re_mgr_presentation\Service\PresentationBlockBuilderServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Real Estate Manager Presentation block.
 *
 * @Block(
 *   id = "re_mgr_presentation",
 *   admin_label = @Translation("Real Estate Manager Presentation"),
 *   category = @Translation("Real Estate")
 * )
 */
class PresentationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Presentation Plugin Manager service.
   */
  protected PresentationManager $presentationPluginManager;

  /**
   * The Presntation Block Builder service.
   */
  protected PresentationBlockBuilderServiceInterface $presentationBlockBuilderService;

  /**
   * Contains plugin objects.
   */
  protected array $plugins = [];

  /**
   * Contains plugin labels.
   */
  protected array $pluginLabels = [];

  /**
   * Contains plugin content.
   */
  protected array $pluginContent = [];

  /**
   * Constructs a new PresentationBlock.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    array $plugin_definition,
    PresentationManager $presentation_plugin_manager,
    PresentationBlockBuilderServiceInterface $presentation_block_builder_service
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->presentationPluginManager = $presentation_plugin_manager;
    $this->presentationBlockBuilderService = $presentation_block_builder_service;
    $block_id = NULL;
    if (!empty($this->configuration)) {
      $block_id = $this->configuration['block_id'] ?? NULL;
    }
    $this->init($block_id);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): PresentationBlock {
    return new static(
    $configuration,
    $plugin_id,
    $plugin_definition,
    $container->get('plugin.manager.re_mgr_presentation'),
    $container->get('re_mgr_presentation.block_builder_service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['#tree'] = TRUE;
    $form['#attached']['library'][] = 're_mgr_presentation/presentation_config_form';
    $form['plugins_settings_title'] = [
      '#markup' => '<hr><h3>' . $this->t('Select plugins to display') . '</h3>',
    ];
    $form['plugins_settings_description'] = [
      '#markup' => '<p>' . $this->t('At least one plugin must be enabled.') . '</p>',
    ];

    if (empty($this->plugins)) {
      $form['plugins_settings_description'] = [
        '#markup' => '<p>' . $this->t('There are no plugins available, please install additional modules to place the block.') . '</p>',
      ];
    }

    /** @var \Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation\PresentationInterface $plugin */
    foreach ($this->plugins as $plugin_id => $plugin) {
      $open_settings = FALSE;
      $html_plugin_id = str_replace('_', '-', $plugin_id);

      if (isset($this->configuration[$plugin_id])) {
        $open_settings = $this->configuration[$plugin_id]['switch'] ? TRUE : FALSE;
      }

      $form[$plugin_id] = [
        'switch' => [
          '#type' => 'checkbox',
          '#title' => $plugin->getLabel(),
          '#default_value' => $this->configuration[$plugin_id]['switch'] ?? 0,
          '#attributes' => [
            'id' => 'edit-settings-' . $html_plugin_id . '-switch',
            'class' => ['plugin-settings-switch'],
            'data-plugin-id' => $plugin_id,
          ],
        ],
        'settings' => [
          '#type' => 'details',
          '#title' => $plugin->getLabel() . ' - ' . $this->t('settings'),
          '#open' => $open_settings,
          '#attributes' => [
            'id' => $plugin_id . '-plugin-settings',
            'class' => ['plugin-settings-wrapper'],
            'plugin-id' => $plugin_id,
          ],
          '#states' => [
            'visible' => [
              ':input[id="edit-settings-' . $html_plugin_id . '-switch"]' => ['checked' => TRUE],
            ],
          ],
          'content' => [],
        ],
      ];

      if (isset($this->configuration[$plugin_id])) {
        $plugin->setConfiguration($this->configuration[$plugin_id]['settings']);
      }
      $subform_state = SubformState::createForSubform($form[$plugin_id]['settings']['content'], $form, $form_state);
      $form[$plugin_id]['settings']['content'] = $plugin->buildConfigurationForm($form[$plugin_id]['settings']['content'], $subform_state);
    }

    if (count($this->plugins) > 1) {
      /* Plugins order section */
      $form['plugins_order_title'] = [
        '#markup' => '<hr><h3>' . $this->t('Set plugins order') . '</h3>',
      ];
      $group_class = 'presentation-plugins';
      $form['plugins_order_table'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Plugin'),
          $this->t('Weight'),
        ],
        '#empty' => $this->t('No plugins.'),
        '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => $group_class,
        ],
        ],
      ];

      $plugins_order_i = -10;
      /** @var \Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation\PresentationInterface $plugin */
      foreach ($this->plugins as $plugin_id => $plugin) {
        $plugin_weight = 0;

        if (
        isset($this->configuration['plugins_order']) &&
        !empty($this->configuration['plugins_order']) &&
        isset($this->configuration['plugins_order'][$plugin_id]) &&
        !empty($this->configuration['plugins_order'][$plugin_id])
        ) {
          $plugin_weight = $this->configuration['plugins_order'][$plugin_id]['weight'];
        }

        $plugin_weight = !empty($plugin_weight) ? $plugin_weight : $plugins_order_i;

        $form['plugins_order_table'][$plugin_id]['#attributes']['class'][] = 'draggable';
        $form['plugins_order_table'][$plugin_id]['plugin']['#plain_text'] = $plugin->getLabel();
        $form['plugins_order_table'][$plugin_id]['#weight'] = $plugin_weight;
        $form['plugins_order_table'][$plugin_id]['weight'] = [
          '#type' => 'weight',
          '#title' => $this->t('Weight for @title', ['@title' => $plugin->getLabel()]),
          '#title_display' => 'invisible',
          '#default_value' => $plugin_weight,
          '#attributes' => ['class' => [$group_class]],
        ];

        $plugins_order_i++;
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state): void {
    /* Clear disabled plugins errors & validated enabled plugins */
    $form_errors = $form_state->getErrors();
    $form_state->clearErrors();
    $any_plugin_enabled = FALSE;

    /** @var \Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation\PresentationInterface $plugin */
    foreach ($this->plugins as $plugin_id => $plugin) {
      if ($form_state->getValue([$plugin_id, 'switch'])) {

        /* Must remove 'settings' element in $form array to proper use
        setErrorByName() in plugin validation form. */
        $element = $form[$plugin_id]['settings']['content'];
        array_shift($element['#array_parents']);

        $plugin->validateConfigurationForm(
          $form[$plugin_id]['settings']['content'],
          SubformState::createForSubform($element, $form, $form_state)
        );
        $any_plugin_enabled = TRUE;
      }
      else {
        foreach ($form_errors as $key => $error) {
          if (str_starts_with($key, 'settings][' . $plugin_id)) {
            unset($form_errors[$key]);
          }
        }
      }
    }

    foreach ($form_errors as $name => $error_message) {
      $field_name = substr($name, 10);
      $form_state->setErrorByName($field_name, $error_message);
    }

    if (!$any_plugin_enabled) {
      $form_state->setError($form['plugins_settings_description'], $this->t('At least one plugin must be enabled.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['block_id'] = $form_state->getBuildInfo()['callback_object']->getEntity()->id();

    /** @var \Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation\PresentationInterface $plugin */
    foreach ($this->plugins as $plugin_id => $plugin) {

      /* Save plugins to display */
      $this->configuration[$plugin_id]['switch'] = $form_state->getValue([
        $plugin_id,
        'switch',
      ]);

      /* Must remove 'settings' element in $form array to proper use getValues()
      in plugin submit form. */
      $element = $form['settings'][$plugin_id]['settings']['content'];
      array_shift($element['#parents']);

      /* Save plugins settings */
      $plugin->submitConfigurationForm(
        $form['settings'][$plugin_id]['settings']['content'],
        SubformState::createForSubform($element, $form, $form_state)
      );
      $this->configuration[$plugin_id]['settings'] = $plugin->getConfiguration();
    }

    /* Set plugins order. */
    $plugins_order_settings = $form_state->getValue('plugins_order_table');
    $this->configuration['plugins_order'] = $plugins_order_settings;
  }

  /**
   * Builds the presentation block.
   */
  public function build(): array {
    $tabs = $this->presentationBlockBuilderService->getBlockTabs($this->pluginLabels, $this->configuration);
    $content = $this->presentationBlockBuilderService->getBlockContent($this->pluginContent, $this->configuration);

    return [
      '#theme' => 'presentation_block',
      '#attached' => ['library' => ['re_mgr_presentation/presentation']],
      '#tabs' => $tabs,
      '#content' => $content,
      '#block_id' => $this->configuration['block_id'],
    ];
  }

  /**
   * Prepare class properties.
   */
  protected function init(?string $block_id): void {
    /** @var array[] */
    $plugin_definitions = $this->presentationPluginManager->getDefinitions();
    $plugins = [];

    foreach ($plugin_definitions as $key => $definition) {
      /** @var \Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation\PresentationInterface */
      $presentation_plugin = $this->presentationPluginManager->createInstance($key);
      $plugin_configuration = $this->configuration[$key] ?? [];

      $plugins[$key] = $presentation_plugin;
      $this->plugins[$key] = $presentation_plugin;
      $this->pluginLabels[$key] = $presentation_plugin->getLabel();
      $this->pluginContent[$key] = [
        'content' => $presentation_plugin->getContent($plugin_configuration ?? [], $block_id),
      ];
    }

    /* Set plugins in order */
    if (isset($this->configuration['plugins_order']) && !empty($this->configuration['plugins_order'])) {
      $sorted_plugins = $this->presentationBlockBuilderService->getSortedPlugins(
        $this->configuration['plugins_order'],
        $this->plugins,
        $this->pluginLabels,
        $this->pluginContent
      );

      $this->plugins = $sorted_plugins['plugins'];
      $this->pluginLabels = $sorted_plugins['plugin_labels'];
      $this->pluginContent = $sorted_plugins['plugin_content'];
    }
  }

}
