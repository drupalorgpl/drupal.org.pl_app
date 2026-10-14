<?php

namespace Drupal\we_megamenu\Hook;

use Drupal\we_megamenu\WeMegaMenuBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\system\Entity\Menu;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for we_megamenu.
 */
class WeMegamenuHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme($existing, $type, $theme, $path) {
    $items['we_megamenu_backend'] = [
      'variables' => [
        'menu_name' => NULL,
        'content' => NULL,
        'section' => 'admin',
        'blocks' => NULL,
        'block_theme' => NULL,
        'data_config' => NULL,
        'trail' => NULL,
      ],
    ];
    $items['we_megamenu_frontend'] = [
      'variables' => [
        'menu_name' => NULL,
        'content' => NULL,
        'section' => '',
        'blocks' => NULL,
        'block_theme' => NULL,
        'data_config' => NULL,
        'trail' => NULL,
      ],
    ];
    $items['we_megamenu_ul'] = [
      'variables' => [
        'menu_name' => NULL,
        'content' => NULL,
        'section' => 'frontend',
        'items' => NULL,
        'subtree' => NULL,
        'data_config' => NULL,
        'block_theme' => NULL,
        'trail' => NULL,
      ],
    ];
    $items['we_megamenu_li'] = [
      'variables' => [
        'menu_name' => NULL,
        'content' => NULL,
        'section' => 'frontend',
        'title' => NULL,
        'subtree' => NULL,
        'items' => NULL,
        'item' => NULL,
        'data_config' => NULL,
        'block_theme' => NULL,
        'trail' => NULL,
      ],
    ];
    $items['we_megamenu_submenu'] = [
      'variables' => [
        'menu_name' => NULL,
        'content' => NULL,
        'section' => 'frontend',
        'title' => NULL,
        'subtree' => NULL,
        'items' => NULL,
        'data_config' => NULL,
        'item_config' => NULL,
        'block_theme' => NULL,
        'trail' => NULL,
      ],
    ];
    $items['we_megamenu_block'] = [
      'variables' => [
        'menu_name' => NULL,
        'content' => NULL,
        'section' => 'frontend',
        'title' => NULL,
        'subtree' => NULL,
        'items' => NULL,
        'data_config' => NULL,
        'item_config' => NULL,
        'block_content' => NULL,
        'block_theme' => NULL,
        'trail' => NULL,
      ],
    ];
    $items['we_megamenu_row'] = [
      'variables' => [
        'menu_name' => NULL,
        'content' => NULL,
        'section' => 'frontend',
        'title' => NULL,
        'subtree' => NULL,
        'items' => NULL,
        'data_config' => NULL,
        'item_config' => NULL,
        'block_theme' => NULL,
        'trail' => NULL,
      ],
    ];
    $items['we_megamenu_col'] = [
      'variables' => [
        'menu_name' => NULL,
        'content' => NULL,
        'section' => 'frontend',
        'title' => NULL,
        'subtree' => NULL,
        'items' => NULL,
        'data_config' => NULL,
        'item_config' => NULL,
        'block_theme' => NULL,
        'trail' => NULL,
      ],
    ];
    $items['we_megamenu_subul'] = [
      'variables' => [
        'menu_name' => NULL,
        'content' => NULL,
        'section' => 'frontend',
        'items' => NULL,
        'subtree' => NULL,
        'data_config' => NULL,
        'item_config' => NULL,
        'block_theme' => NULL,
        'trail' => NULL,
      ],
    ];
    return $items;
  }

  /**
   * Implements hook_block_view_BASE_BLOCK_ID_alter().
   *
   * Config Contextual link of Mega Menu blocks.
   */
  #[Hook('block_view_we_megamenu_block_alter')]
  public function blockViewWeMegamenuBlockAlter(array &$build, BlockPluginInterface $block) {
    $menus = Menu::loadMultiple();
    $menu_name = $block->getDerivativeId();
    if (isset($menus[$menu_name])) {
      $build['#contextual_links']['menu'] = [
        'route_parameters' => [
          'menu' => $menu_name,
        ],
      ];
    }
    $build['#contextual_links']['we_megamenu_block']['route_parameters'] = [
      'menu_name' => $build['#derivative_plugin_id'],
    ];
  }

  /**
   * Implements hook_entity_insert().
   *
   * Reset menu to default status when origin menu changed.
   */
  #[Hook('entity_insert')]
  public function entityInsert(EntityInterface $entity) {
    if (method_exists($entity, 'getTypedData') && method_exists($entity, 'getEntityTypeId')) {
      $data = $entity->getTypedData();
      $data = $data->toArray();
      $entity_type = $entity->getEntityTypeId();
      switch ($entity_type) {
        case 'menu':
          break;

        case 'menu_link_content':
          if (isset($data['parent'][0]['value'])) {
            $mid_parent = $data['parent'][0]['value'];
            $mid_parent = str_replace('menu_link_content:', '', $mid_parent);
            $menu_name = $data['menu_name'][0]['value'];
            $config = \Drupal::config('system.theme');
            $theme_name = $config->get('default');
            // Load main menu and insert to megamenu.
            $menu_item_obj = new \stdClass();
            $menu_item_obj->rows_content = [];
            $menu_item_obj->submenu_config = new \stdClass();
            $menu_item_obj->submenu_config->width = '';
            $menu_item_obj->submenu_config->height = '';
            $menu_item_obj->submenu_config->type = '';
            $menu_item_obj->item_config = new \stdClass();
            $menu_item_obj->item_config->level = 0;
            $menu_item_obj->item_config->type = '';
            $menu_item_obj->item_config->id = $entity->get('uuid')->getString();
            $menu_item_obj->item_config->submenu = '';
            $menu_item_obj->item_config->hide_sub_when_collapse = '';
            $menu_item_obj->item_config->group = '';
            $menu_item_obj->item_config->class = '';
            $menu_item_obj->item_config->{'data-icon'} = '';
            $menu_item_obj->item_config->{'data-caption'} = '';
            $menu_item_obj->item_config->{'data-alignsub'} = '';
            $menu_item_obj->item_config->{'data-target'} = '_self';
            // Mega menu config.
            $menu_config = WeMegaMenuBuilder::loadConfig($menu_name, $theme_name);
            foreach ($menu_config->menu_config as $key_menu => $menu) {
              $mid_parent = $mid_parent == 'standard.front_page' ? base_path() : $mid_parent;
              if ($key_menu == $mid_parent) {
                $rows_content = $menu->rows_content;
                $tmp_col_content = new \stdClass();
                $tmp_col_content->mlid = $entity->get('uuid')->getString();
                $tmp_col_content->type = 'we-mega-menu-li';
                $tmp_col_content->item_config = new \stdClass();
                $tmp_col_cfg = new \stdClass();
                $tmp_col_cfg->hidewhencollapse = '';
                $tmp_col_cfg->type = 'we-mega-menu-col';
                $tmp_col_cfg->width = 12;
                $tmp_col_cfg->block = '';
                $tmp_col_cfg->class = '';
                $tmp_col_cfg->block_title = 0;
                $child_item = [
                  'col_content' => $tmp_col_content,
                  'col_cfg' => $tmp_col_cfg,
                ];
                $menu_position = WeMegaMenuBuilder::menuItemInsert($key_menu, $menu_config, $menu, $child_item);
              }
            }
            $menu_config->menu_config->{$entity->get('uuid')->getString()} = $menu_item_obj;
            $data_cfg = json_encode($menu_config);
            WeMegaMenuBuilder::saveConfig($menu_name, $theme_name, $data_cfg);
          }
          break;
      }
    }
  }

  /**
   * Implements hook_entity_delete().
   */
  #[Hook('entity_delete')]
  public function entityDelete(EntityInterface $entity) {
    if (method_exists($entity, 'getTypedData') && method_exists($entity, 'getEntityTypeId')) {
      $data = $entity->getTypedData();
      $data = $data->toArray();
      $entity_type = $entity->getEntityTypeId();
      switch ($entity_type) {
        case 'menu':
          break;

        case 'menu_link_content':
          $menu_name = $data['menu_name'][0]['value'];
          $config = \Drupal::config('system.theme');
          $theme_name = $config->get('default');
          $menu_uuid = $entity->get('uuid')->getString();
          $menu_uuid = $menu_uuid == 'standard.front_page' ? base_path() : $menu_uuid;
          $menu_config = WeMegaMenuBuilder::loadConfig($menu_name, $theme_name);
          WeMegaMenuBuilder::menuItemDelete($menu_config, $menu_uuid);
          WeMegaMenuBuilder::saveConfig($menu_name, $theme_name, json_encode($menu_config));
          break;
      }
    }
  }

  /**
   * Implements hook_entity_presave().
   *
   * Reset menu to default status when origin menu changed.
   */
  #[Hook('entity_presave')]
  public function entityPresave(EntityInterface $entity) {
    if (method_exists($entity, 'getTypedData') && method_exists($entity, 'getEntityTypeId')) {
      $data = $entity->getTypedData();
      $data = $data->toArray();
      $entity_type = $entity->getEntityTypeId();
      switch ($entity_type) {
        case 'menu':
          $menu_name = $data['id'];
          $config = \Drupal::config('system.theme');
          $theme_name = $config->get('default');
          $tmp_col_content = new \stdClass();
          $tmp_col_content->mlid = '';
          $tmp_col_content->type = 'we-mega-menu-li';
          $tmp_col_content->item_config = new \stdClass();
          $tmp_col_cfg = new \stdClass();
          $tmp_col_cfg->hidewhencollapse = '';
          $tmp_col_cfg->type = 'we-mega-menu-col';
          $tmp_col_cfg->width = 12;
          $tmp_col_cfg->block = '';
          $tmp_col_cfg->class = '';
          $tmp_col_cfg->block_title = 0;
          $child_item = [
            'col_content' => $tmp_col_content,
            'col_cfg' => $tmp_col_cfg,
          ];
          $menu_config = WeMegaMenuBuilder::loadConfig($menu_name, $theme_name);
          if (!empty($menu_config)) {
            $menu_config->menu_update_flag = 1;
            WeMegaMenuBuilder::saveConfig($menu_name, $theme_name, json_encode($menu_config));
          }
          break;

        case 'menu_link_content':
          break;
      }
    }
  }

}
