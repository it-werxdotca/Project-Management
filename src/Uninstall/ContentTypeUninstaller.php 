<?php

namespace Drupal\project_management\Uninstall;

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Uninstalls content types and fields.
 */
class ContentTypeUninstaller {

  /**
   * Runs the uninstallation process.
   */
  public function uninstall() {
	$this->deleteNodeType('project_management_project');
	$this->deleteNodeType('project_management_task');
	$this->removeProjectCategoryField();
  }

  /**
   * Deletes a node type if it exists.
   */
  private function deleteNodeType($type) {
	if ($node_type = NodeType::load($type)) {
	  $node_type->delete();
	}
  }

  /**
   * Removes the taxonomy reference field from 'Project'.
   */
  private function removeProjectCategoryField() {
	$field_name = 'field_project_category';
	$content_type = 'project_management_project';

	if ($field = FieldConfig::loadByName('node', $content_type, $field_name)) {
	  $field->delete();
	}
	if ($storage = FieldStorageConfig::loadByName('node', $field_name)) {
	  $storage->delete();
	}
  }
}
