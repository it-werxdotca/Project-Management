<?php

namespace Drupal\project_management\Install;

use Drupal\Core\Database\SchemaObjectBase;

/**
 * Defines the database schema for the project_management module.
 */
class ProjectManagementDataInstall extends SchemaObjectBase {

  /**
   * Returns the schema definition for the project_management_user table.
   */
  public static function schema() {
	$schema['project_management_user'] = [
	  'description' => 'Stores user project assignments.',
	  'fields' => [
		'id' => [
		  'description' => 'Primary Key.',
		  'type' => 'serial',
		  'unsigned' => TRUE,
		  'not null' => TRUE,
		],
		'uid' => [
		  'description' => 'User ID for a team member or client role that is invited to the project by a project manager.',
		  'type' => 'int',
		  'unsigned' => TRUE,
		  'not null' => TRUE,
		],
		'project_ids' => [
		  'description' => 'Array of Project IDs (JSON encoded). Helpful if users are invited to several projects',
		  'type' => 'text',
		  'not null' => FALSE,
		],
		'creator_id' => [
		  'description' => 'ID of the project manager who assigned the project.',
		  'type' => 'int',
		  'unsigned' => TRUE,
		  'not null' => TRUE,
		],
	  ],
	  'primary key' => ['id'],
	  'indexes' => [
		'uid' => ['uid'],
	  ],
	  'foreign keys' => [
		'user' => [
		  'table' => 'users_field_data',
		  'columns' => ['uid' => 'uid'],
		],
		'creator' => [
		  'table' => 'users_field_data',
		  'columns' => ['creator_id' => 'uid'],
		],
	  ],
	];

	return $schema;
  }
}
