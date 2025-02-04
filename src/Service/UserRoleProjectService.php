<?php

namespace Drupal\project_management\Service;

use Drupal\Core\Database\Connection;
use Drupal\user\UserInterface;

/**
 * Service for managing user project assignments.
 */
class UserProjectService {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs the service.
   */
  public function __construct(Connection $database) {
	$this->database = $database;
  }

  /**
   * Assigns a user to a project.
   */
  public function assignUserToProject(UserInterface $user, int $project_id, int $creator_id) {
	$uid = $user->id();

	// Check if the user-project entry exists.
	$query = $this->database->select('project_management_users', 'pmu')
	  ->fields('pmu', ['id', 'project_ids'])
	  ->condition('uid', $uid)
	  ->execute()
	  ->fetchAssoc();

	if ($query) {
	  // Update existing record, append project_id.
	  $project_ids = json_decode($query['project_ids'], TRUE) ?? [];
	  if (!in_array($project_id, $project_ids)) {
		$project_ids[] = $project_id;
	  }

	  $this->database->update('project_management_users')
		->fields(['project_ids' => json_encode($project_ids)])
		->condition('id', $query['id'])
		->execute();
	} 
	else {
	  // Insert new record.
	  $this->database->insert('project_management_users')
		->fields([
		  'uid' => $uid,
		  'project_ids' => json_encode([$project_id]),
		  'creator_id' => $creator_id,
		])
		->execute();
	}
  }

  /**
   * Retrieves all projects for a user.
   */
  public function getUserProjects(UserInterface $user) {
	return $this->database->select('project_management_users', 'pmu')
	  ->fields('pmu', ['project_ids', 'creator_id'])
	  ->condition('uid', $user->id())
	  ->execute()
	  ->fetchAll();
  }

  /**
   * Unassigns a user from a specific project.
   */
  public function removeUserFromProject(UserInterface $user, int $project_id) {
	$uid = $user->id();

	$query = $this->database->select('project_management_users', 'pmu')
	  ->fields('pmu', ['id', 'project_ids'])
	  ->condition('uid', $uid)
	  ->execute()
	  ->fetchAssoc();

	if ($query) {
	  $project_ids = json_decode($query['project_ids'], TRUE) ?? [];
	  $project_ids = array_diff($project_ids, [$project_id]); // Remove the project.

	  if (!empty($project_ids)) {
		$this->database->update('project_management_users')
		  ->fields(['project_ids' => json_encode($project_ids)])
		  ->condition('id', $query['id'])
		  ->execute();
	  } 
	  else {
		// Delete if no projects remain.
		$this->database->delete('project_management_users')
		  ->condition('id', $query['id'])
		  ->execute();
	  }
	}
  }
}
