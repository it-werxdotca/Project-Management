<?php

namespace Drupal\project_management\Uninstall;

use Drupal\user\Entity\UserRole;

/**
 * Uninstalls roles for the project management system.
 */
class RolesUninstaller {

  /**
   * Runs the uninstallation process.
   */
  public function uninstall() {
	$this->deleteRoles();
  }

  /**
   * Deletes the roles created for project management.
   */
  private function deleteRoles() {
	$roles = ['Project Manager', 'Team Member', 'Accounting', 'Client'];

	foreach ($roles as $role) {
	  if ($role_object = UserRole::load(strtolower(str_replace(' ', '_', $role)))) {
		$role_object->delete();
	  }
	}
  }
}
