<?php

namespace Drupal\project_management\Install;

use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\NodeType;

class TaxonomyInstaller {

  /**
   * Creates the taxonomy vocabularies and predefined terms.
   */
  public function createTaxonomyVocabularies() {
	// Create vocabularies for projects and tasks.
	$project_vocabulary = $this->createVocabulary('project_management_project_category', 'Project Categories');
	$task_vocabulary = $this->createVocabulary('project_management_task_category', 'Task Categories');

	// Add predefined terms.
	$this->createTaxonomyTerms($project_vocabulary, ['Development', 'Marketing', 'Consulting', 'Design']);
	$this->createTaxonomyTerms($task_vocabulary, ['To Do', 'In Progress', 'On Hold', 'Complete', 'On Going']);

	// Attach taxonomy field to the "task" content type.
	$this->addTaxonomyFieldToTask();
  }

  /**
   * Creates a taxonomy vocabulary if it does not exist.
   */
  private function createVocabulary($vid, $name) {
	$vocabulary = Vocabulary::load($vid);
	if (!$vocabulary) {
	  $vocabulary = Vocabulary::create([
		'vid' => $vid,
		'name' => $name,
	  ]);
	  $vocabulary->save();
	}
	return $vocabulary;
  }

  /**
   * Creates predefined taxonomy terms.
   */
  private function createTaxonomyTerms($vocabulary, array $terms) {
	foreach ($terms as $term_name) {
	  $existing_terms = \Drupal::entityTypeManager()
		->getStorage('taxonomy_term')
		->loadByProperties(['name' => $term_name, 'vid' => $vocabulary->id()]);
	  
	  if (empty($existing_terms)) {
		$new_term = Term::create([
		  'name' => $term_name,
		  'vid' => $vocabulary->id(),
		]);
		$new_term->save();
	  }
	}
  }

  /**
   * Adds a taxonomy reference field to the "task" content type.
   */
  private function addTaxonomyFieldToTask() {
	if (!NodeType::load('task')) {
	  return; // Ensure "task" content type exists.
	}

	$field_name = 'field_project_management_task_category';

	if (!\Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'task')[$field_name] ?? false) {
	  $field_storage = FieldStorageConfig::create([
		'field_name' => $field_name,
		'entity_type' => 'node',
		'type' => 'entity_reference',
		'settings' => ['target_type' => 'taxonomy_term'],
	  ]);
	  $field_storage->save();

	  $field_instance = FieldConfig::create([
		'field_storage' => $field_storage,
		'bundle' => 'task',
		'label' => 'Task Category',
		'settings' => ['handler_settings' => ['target_bundles' => ['project_management_task_category' => 'project_management_task_category']]],
	  ]);
	  $field_instance->save();
	}
  }
}
