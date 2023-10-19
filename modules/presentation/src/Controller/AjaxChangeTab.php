<?php

namespace Drupal\re_mgr_presentation\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\re_mgr_presentation\Manager\PresentationManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides endpoint with ajax presentation change tab.
 */
class AjaxChangeTab extends ControllerBase {

  /**
   * The Module Extension List service.
   */
  protected PresentationManager $presentationPluginManager;

  /**
   * Constructs a AjaxChangeTab object.
   */
  public function __construct(
    PresentationManager $presentation_plugin_manager,
    ConfigFactoryInterface $config_factory
  ) {
    $this->presentationPluginManager = $presentation_plugin_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): AjaxChangeTab {
    return new static(
      $container->get('plugin.manager.re_mgr_presentation'),
      $container->get('config.factory'),
    );
  }

  /**
   * Ajax change tab function.
   */
  public function changeTab(string $plugin_id, string $block_id): AjaxResponse {
    /** @var array */
    $block_configuration = $this->configFactory->get('block.block.' . $block_id)->get();
    $plugin_configuration = $block_configuration['settings'][$plugin_id];
    /** @var \Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation\PresentationInterface */
    $plugin = $this->presentationPluginManager->createInstance($plugin_id);
    $content = $plugin->getContent($plugin_configuration, $block_id);
    $re_mgr_presentation_content = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 're-mgr-presentation-content--' . $block_id,
      ],
      'content' => $content,
    ];

    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#re-mgr-presentation-content--' . $block_id, $re_mgr_presentation_content));

    return $response;
  }

}
