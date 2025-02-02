<?php

namespace Drupal\project_management\Uninstall;

use Drupal\comment\Entity\CommentType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Uninstalls the comment type for tasks.
 */
class CommentUninstaller {

  /**
   * Runs the uninstallation process.
   */
  public function uninstall() {
	$this->deleteTaskCommentType();
	$this->removeCommentFieldFromTask();
  }

  /**
   * Deletes the comment type for 'Task'.
   */
  private function deleteTaskCommentType() {
	if ($comment_type = CommentType::load('task_comment')) {
	  $comment_type->delete();
	}
  }

  /**
   * Removes the comment field from 'Task'.
   */
  private function removeCommentFieldFromTask() {
	$field_name = 'field_task_comments';
	$content_type = 'project_management_task';

	if ($field = FieldConfig::loadByName('node', $content_type, $field_name)) {
	  $field->delete();
	}
	if ($storage = FieldStorageConfig::loadByName('node', $field_name)) {
	  $storage->delete();
	}
  }
}
