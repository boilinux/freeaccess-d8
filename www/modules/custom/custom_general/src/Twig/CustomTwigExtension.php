<?php

namespace Drupal\custom_general\Twig;

use Drupal\node\Entity\Node;

class CustomTwigExtension extends \Twig_Extension {
  /**
   * @return array
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('custom_general_parse_photo', [$this, 'custom_general_parse_photo']),
      new \Twig_SimpleFunction('custom_general_render_attribute', [$this, 'custom_general_render_attribute']),
      new \Twig_SimpleFunction('custom_general_render_gallery_tags', [$this, 'custom_general_render_gallery_tags']),
      new \Twig_SimpleFunction('custom_general_activity_message', [$this, 'custom_general_activity_message']),
      new \Twig_SimpleFunction('custom_general_logo_path', [$this, 'custom_general_logo_path']),
      new \Twig_SimpleFunction('custom_general_print_username', [$this, 'custom_general_print_username']),
      new \Twig_SimpleFunction('custom_general_get_current_uid', [$this, 'custom_general_get_current_uid']),
      new \Twig_SimpleFunction('custom_general_render_menu', [$this, 'custom_general_render_menu']),
    ];
  }

  public function getName() {
    return 'custom_general.twig_extension';
  }

  public function custom_general_parse_photo($photo) {
    return str_replace('.jpg', '', render($photo));
  }

  public function custom_general_render_attribute($field) {
    $explode = explode(':', $field['content']['#cache']['tags'][0]);

    $alt = \Drupal::database()->query("SELECT field_gallery_alt FROM node__field_gallery WHERE field_gallery_target_id = " . $explode[1])->fetchField();

    return $alt;
  }

  public function custom_general_render_gallery_tags($field) {
    print_r($field);
  }

  public function custom_general_activity_message() {
    $uid = \Drupal::currentUser()->id();

    $query = \Drupal::database()->query("SELECT b.body_value AS message, nfd.nid AS nid FROM node_field_data AS nfd
      LEFT JOIN node__body AS b ON b.entity_id = nfd.nid
      LEFT JOIN node__field_is_seen AS nfis ON nfis.entity_id = nfd.nid
      WHERE nfd.type = 'activity' AND nfis.field_is_seen_value = 0")->fetchAll();


    foreach ($query as $msg) {
      drupal_set_message(strip_tags($msg->message));

      // if seen then update status to 1.
      $node = Node::load($msg->nid);
      $node->field_is_seen->value = 1;
      $node->save();
    }

    return;
  }

  public function custom_general_logo_path() {
    return file_url_transform_relative(file_create_url(theme_get_setting('logo.url')));
  }

  public function custom_general_print_username() {
    $uid = \Drupal::currentUser()->id();

    $username = \Drupal::database()->query("SELECT name FROM users_field_data WHERE uid = " . $uid)->fetchField();

    return $username;
  }

  public function custom_general_get_current_uid() {
    return \Drupal::currentUser()->id();
  }

  public function custom_general_render_menu($menu_name) {
    $menu_tree = \Drupal::menuTree();

    // Build the typical default set of menu tree parameters.
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);

    // Load the tree based on this set of parameters.
    $tree = $menu_tree->load($menu_name, $parameters);
    
    // Transform the tree using the manipulators you want.
    $manipulators = array(
      // Only show links that are accessible for the current user.
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      // Use the default sorting of menu links.
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    $tree = $menu_tree->transform($tree, $manipulators);

    // Finally, build a renderable array from the transformed tree.
    $menu = $menu_tree->build($tree);

    $menu['#attributes']['class'] = 'menu ' . $menu_name;

    return array('#markup' => drupal_render($menu));
  }
}