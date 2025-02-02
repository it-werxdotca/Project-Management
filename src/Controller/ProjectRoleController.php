<?php

namespace Drupal\project_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\project_management\Service\UserRoleProjectService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Database\Connection;

/**
 * Controller for managing project roles.
 */
class ProjectRoleController extends ControllerBase {

  /**
   * The user-role project service.
   *
   * @var \Drupal\project_management\Service\UserRoleProjectService
   */
  protected $userRoleProjectService;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructor.
   */
  public function __construct(UserRoleProjectService $userRoleProjectService, Connection $database) {
	$this->userRoleProjectService = $userRoleProjectService;
	$this->database = $database;
  }

  /**
   * Dependency Injection.
   */
  public static function create(ContainerInterface $container) {
	return new static(
	  $container->get('project_management.user_role_project_service'),
	  $container->get('database')
	);
  }

  /**
   * Assigns a user a role in a project.
   */
  public function assignRole(AccountInterface $account, string $role, int $project_id) {
	$current_user = $this->currentUser();

	// Only allow administrators and project managers to assign roles.
	if (!$current_user->hasPermission('administer site configuration') &&
		!$current_user->hasPermission('manage project roles')) {
	  return new Response($this->t('Access Denied'), 403);
	}

	$this->userRoleProjectService->assignUserRole($account, $role, $project_id);

	return ['#markup' => $this->t('User @uid assigned role @role in project @project_id.', [
	  '@uid' => $account->id(),
	  '@role' => $role,
	  '@project_id' => $project_id,
	])];
  }

  /**
   * Removes a user from a project.
   */
  public function removeUserRole(AccountInterface $account, string $role, int $project_id) {
	$current_user = $this->currentUser();

	// Check if the current user is an administrator.
	if ($current_user->hasPermission('administer site configuration')) {
	  $this->userRoleProjectService->removeUserRole($account, $role, $project_id);
	  return new Response($this->t('User removed from project.'));
	}

	// Check if the user is a project manager and if they created the project.
	if ($current_user->hasPermission('manage project roles') && $this->isProjectCreator($current_user->id(), $project_id)) {
	  $this->userRoleProjectService->removeUserRole($account, $role, $project_id);
	  return new Response($this->t('User removed from project.'));
	}

	return new Response($this->t('Access Denied'), 403);
  }

  /**
   * Checks if the user is the creator of the project.
   */
  protected function isProjectCreator(int $uid, int $project_id): bool {
	$query = $this->database->select('project_management_projects', 'p')
	  ->fields('p', ['creator_id'])
	  ->condition('p.id', $project_id)
	  ->execute()
	  ->fetchField();

	return $query == $uid;
  }

  /**
   * Lists user roles and projects.
   */
  public function listRoles(AccountInterface $account) {
	$roles = $this->userRoleProjectService->getUserRolesAndProjects($account);

	$output = '<ul>';
	foreach ($roles as $role) {
	  $projects = implode(', ', json_decode($role->project_ids, TRUE));
	  $output .= "<li>{$role->role}: {$projects}</li>";
	}
	$output .= '</ul>';

	return ['#markup' => $output];
  }
}
