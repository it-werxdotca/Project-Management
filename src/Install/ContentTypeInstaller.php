<?php

namespace Drupal\project_management\Install;

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\comment\Entity\CommentType;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;

/**
 * Handles the installation of content types, taxonomy, and comments.
 */
class ContentTypeInstaller {

  /**
   * Runs the installation process.
   */
  public function install() {
	$this->createContentTypes();
	$this->createTaskCommentType();
  }

  /**
   * Creates 'Project' and 'Task' content types.
   */
  private function createContentTypes() {
	$this->createNodeType('project_management_project', 'Project');
	$this->createNodeType('project_management_task', 'Task');
	$this->addTaxonomyFieldToProject();
  }

  /**
   * Creates a node type (content type).
   */
  private function createNodeType($type, $label) {
	if (!NodeType::load($type)) {
	  $node_type = NodeType::create([
		'type' => $type,
		'name' => $label,
	  ]);
	  $node_type->save();
	}
  }

  /**
   * Adds a taxonomy reference field to 'Project'.
   */
  private function addTaxonomyFieldToProject() {
	$field_name = 'field_project_management_project_category';
	$content_type = 'project_management_project';

	// Check if the field exists
	$fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $content_type);
	if (!isset($fields[$field_name])) {
	  // Create field storage
	  FieldStorageConfig::create([
		'field_name' => $field_name,
		'entity_type' => 'node',
		'type' => 'entity_reference',
		'settings' => ['target_type' => 'taxonomy_term'],
	  ])->save();

	  // Create field instance
	  FieldConfig::create([
		'field_name' => $field_name,
		'entity_type' => 'node',
		'bundle' => $content_type,
		'label' => 'Project Category',
		'settings' => ['handler_settings' => ['target_bundles' => ['project_category']]],
	  ])->save();
	}
  }

  /**
   * Creates a comment type and attaches it to the 'Task' content type.
   */
  private function createTaskCommentType() {
	$comment_type_id = 'project_management_task_comment';

	// Check if the comment type exists
	if (!CommentType::load($comment_type_id)) {
	  CommentType::create([
		'id' => $comment_type_id,
		'label' => 'Task Comment',
		'target_entity_type_id' => 'node',
		'target_bundle' => 'project_management_task',
		'field_type' => 'comment',
	  ])->save();
	}

	$this->addCommentFieldToTask($comment_type_id);
  }

  /**
   * Adds a comment field to 'Task'.
   */
  private function addCommentFieldToTask($comment_type_id) {
	$field_name = 'field_project_management_task_comments';
	$content_type = 'project_management_task';

	// Check if the field exists
	$fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $content_type);
	if (!isset($fields[$field_name])) {
	  // Create field storage
	  FieldStorageConfig::create([
		'field_name' => $field_name,
		'entity_type' => 'node',
		'type' => 'comment',
		'settings' => ['target_type' => 'comment'],
	  ])->save();

	  // Create field instance
	  FieldConfig::create([
		'field_name' => $field_name,
		'entity_type' => 'node',
		'bundle' => $content_type,
		'label' => 'Task Comments',
		'settings' => ['handler_settings' => ['comment_type' => $comment_type_id]],
	  ])->save();
	}
  }
}
