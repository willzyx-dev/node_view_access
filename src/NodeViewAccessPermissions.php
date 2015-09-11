<?php

/**
 * @file
 * Contains \Drupal\node_view_access\NodeViewAccessPermissions.
 */

namespace Drupal\node_view_access;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\NodeType;

/**
 * Defines a class containing permission callbacks.
 */
class NodeViewAccessPermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of node type permissions.
   *
   * @return array
   *   An array of permissions for all node types.
   */
  public function nodeTypePermissions() {
    $perms = array();
    // Generate node permissions for all node types.
    foreach (NodeType::loadMultiple() as $type) {
      $perms += $this->buildPermissions($type);
    }

    return $perms;
  }

  /**
   * Builds views permissions for a given type.
   *
   * @param \Drupal\node\Entity\NodeType $type
   *   The machine name of the node type.
   *
   * @return array
   *   An array of permission names and descriptions.
   */
  protected function buildPermissions(NodeType $type) {
    $type_id = $type->id();
    $type_params = array('%type_name' => $type->label());

    return array(
      "view $type_id content" => array(
        'title' => $this->t('%type_name: View content', $type_params),
      ),
      "view own $type_id content" => array(
        'title' => $this->t('%type_name: View own content', $type_params),
      ),
    );
  }

}
