<?php

namespace Drupal\project_management\Install;

use Drupal\user\Entity\UserRole;

class RolesInstallser {

  /**
   * Creates the necessary roles for the project management system.
   */
  public function createRoles() {
	$roles = ['Project Manager', 'Team Member', 'Accounting', 'Client'];

	foreach ($roles as $role) {
	  if (!user_role_load_by_name($role)) {
		$role_object = UserRole::create(['id' => strtolower(str_replace(' ', '_', $role)), 'label' => $role]);
		$role_object->save();
	  }
	}
  }
}
