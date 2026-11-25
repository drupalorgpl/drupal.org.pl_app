<?php

namespace Drupal\entity_reference_display\Hook;

use Drupal\Component\Utility\Html;
use Drupal\entity_reference_display\Plugin\Field\FieldFormatter\EntityReferenceRevisionsDisplayFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for entity_reference_display.
 */
class EntityReferenceDisplayHooks {

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_field_storage_config_edit_form_alter')]
  public static function formFieldStorageConfigEditFormAlter(&$form, FormStateInterface $form_state) {
    entity_reference_display_disable_cardinality_settings($form['cardinality_container'], $form_state);
  }

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_field_config_edit_form_alter')]
  public static function formFieldConfigEditFormAlter(&$form, FormStateInterface $form_state) {
    $cardinality_container = $form['field_storage']['subform']['cardinality_container'] ?? [];
    entity_reference_display_disable_cardinality_settings($cardinality_container, $form_state);
  }

  /**
   * Implements hook_field_widget_info_alter().
   */
  #[Hook('field_widget_info_alter')]
  public static function fieldWidgetInfoAlter(array &$info) {
    // Allow to use the same widgets as list_string field type.
    entity_reference_display_set_available_plugins($info);
  }

  /**
   * Implements hook_field_formatter_info_alter().
   */
  #[Hook('field_formatter_info_alter')]
  public static function fieldFormatterInfoAlter(array &$info) {
    // Allow to use the same formatters as list_string field type.
    entity_reference_display_set_available_plugins($info);
    // Replace default formatter when revisions are supported.
    if (\Drupal::moduleHandler()->moduleExists('entity_reference_revisions')) {
      $info['entity_reference_display_default']['class'] = EntityReferenceRevisionsDisplayFormatter::class;
    }
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_field')]
  public static function preprocessField(&$variables) {
    // Only for selected display mode formatter.
    if (isset($variables['element']['#formatter']) && $variables['element']['#formatter'] === 'entity_reference_display_default') {
      // Add display mode class for field wrapper.
      if (!empty($variables['element'][0]['#view_mode'])) {
        $class = 'erd-list--' . $variables['element'][0]['#view_mode'];
        $variables['attributes']['class'][] = Html::getClass($class);
      }
    }
  }

}
