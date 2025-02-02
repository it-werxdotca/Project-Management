<?php

namespace Drupal\project_management\Service;

use Drupal\Core\Database\Connection;
use Drupal\user\UserInterface;

/**
 * Service for managing user roles in projects.
 */
class UserRoleProjectService {

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
   * Assigns a user to a role and project.
   */
  public function assignUserRole(UserInterface $user, string $role, int $project_id) {
	$uid = $user->id();

	// Check if the user-role entry exists.
	$query = $this->database->select('project_management_user_roles', 'pmur')
	  ->fields('pmur', ['id', 'project_ids'])
	  ->condition('uid', $uid)
	  ->condition('role', $role)
	  ->execute()
	  ->fetchAssoc();

	if ($query) {
	  // Update existing record, append project_id.
	  $project_ids = json_decode($query['project_ids'], TRUE) ?? [];
	  if (!in_array($project_id, $project_ids)) {
		$project_ids[] = $project_id;
	  }

	  $this->database->update('project_management_user_roles')
		->fields(['project_ids' => json_encode($project_ids)])
		->condition('id', $query['id'])
		->execute();
	} 
	else {
	  // Insert new record.
	  $this->database->insert('project_management_user_roles')
		->fields([
		  'uid' => $uid,
		  'role' => $role,
		  'project_ids' => json_encode([$project_id]),
		])
		->execute();
	}
  }

  /**
   * Retrieves all roles and projects for a user.
   */
  public function getUserRolesAndProjects(UserInterface $user) {
	return $this->database->select('project_management_user_roles', 'pmur')
	  ->fields('pmur', ['role', 'project_ids'])
	  ->condition('uid', $user->id())
	  ->execute()
	  ->fetchAll();
  }

  /**
   * Unassigns a user from a specific project role.
   */
  public function removeUserRole(UserInterface $user, string $role, int $project_id) {
	$uid = $user->id();

	$query = $this->database->select('project_management_user_roles', 'pmur')
	  ->fields('pmur', ['id', 'project_ids'])
	  ->condition('uid', $uid)
	  ->condition('role', $role)
	  ->execute()
	  ->fetchAssoc();

	if ($query) {
	  $project_ids = json_decode($query['project_ids'], TRUE) ?? [];
	  $project_ids = array_diff($project_ids, [$project_id]); // Remove the project.

	  if (!empty($project_ids)) {
		$this->database->update('project_management_user_roles')
		  ->fields(['project_ids' => json_encode($project_ids)])
		  ->condition('id', $query['id'])
		  ->execute();
	  } 
	  else {
		// Delete if no projects remain.
		$this->database->delete('project_management_user_roles')
		  ->condition('id', $query['id'])
		  ->execute();
	  }
	}
  }
}
