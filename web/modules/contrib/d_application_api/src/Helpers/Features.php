<?php

namespace Drupal\d_application_api\Helpers;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\features\FeaturesManagerInterface;

class Features {

  /**
   * Drupal logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * Features manager.
   *
   * @var \Drupal\features\FeaturesManagerInterface
   */
  private $manager;

  /**
   * Features constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   * @param \Drupal\features\FeaturesManagerInterface $manager
   */
  public function __construct(LoggerChannelFactoryInterface $logger_factory, FeaturesManagerInterface $manager) {
    $this->logger = $logger_factory->get('d_application_api');
    $this->manager = $manager;
  }

  /**
   * Copy from file features.drush.inc.
   *
   * @param array $args
   */
  public function import($args) {

    // Determine if revert should be forced.
    $force = TRUE;
    // Determine if -y was supplied. If so, we can filter out needless output
    // from this command.
    $skip_confirmation = TRUE;

    // Parse list of arguments.
    $modules = [];
    foreach ($args as $arg) {
      $arg = explode(':', $arg);
      $module = array_shift($arg);
      $component = array_shift($arg);

      if (isset($module)) {
        if (empty($component)) {
          // If we received just a feature name, this means that we need all of
          // its components.
          $modules[$module] = TRUE;
        }
        elseif ($modules[$module] !== TRUE) {
          if (!isset($modules[$module])) {
            $modules[$module] = [];
          }
          $modules[$module][] = $component;
        }
      }
    }

    // Process modules.
    foreach ($modules as $module => $components_needed) {

      $dt_args['@module'] = $module;
      /** @var \Drupal\features\Package $feature */
      $feature = $this->manager->loadPackage($module, TRUE);
      if (empty($feature)) {
        $this->logger->error('No such feature is available: @module', $dt_args);
        return;
      }

      if ($feature->getStatus() != FeaturesManagerInterface::STATUS_INSTALLED) {
        $this->logger->error('No such feature is installed: @module', $dt_args);
        return;
      }

      // Forcefully revert all components of a feature.
      if ($force) {
        $components = $feature->getConfigOrig();
      }
      // Only revert components that are detected to be Overridden.
      else {
        $components = $this->manager->detectOverrides($feature);
        $missing = $this->manager->reorderMissing($this->manager->detectMissing($feature));
        // Be sure to import missing components first.
        $components = array_merge($missing, $components);
      }

      if (!empty($components_needed) && is_array($components_needed)) {
        $components = array_intersect($components, $components_needed);
      }

      if (empty($components)) {
        $this->logger->notice('Current state already matches active config, aborting.');
      }
      else {
        // Determine which config the user wants to import/revert.
        $config_to_create = [];
        foreach ($components as $component) {
          $dt_args['@component'] = $component;
          if ($skip_confirmation) {
            $config_to_create[$component] = '';
          }
        }

        // Perform the import/revert.
        $config_imported = $this->manager->createConfiguration($config_to_create);

        // List the results.
        foreach ($components as $component) {
          $dt_args['@component'] = $component;
          if (isset($config_imported['new'][$component])) {
            $this->logger->notice('Imported @module : @component.', $dt_args);
          }
          elseif (isset($config_imported['updated'][$component])) {
            $this->logger->notice('Reverted @module : @component.', $dt_args);
          }
          elseif (!isset($config_to_create[$component])) {
            $this->logger->notice('Skipping @module : @component.', $dt_args);
          }
          else {
            $this->logger->notice('Error importing @module : @component.', $dt_args);
          }
        }
      }
    }
  }
}
