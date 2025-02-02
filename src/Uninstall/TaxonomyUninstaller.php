<?php

namespace Drupal\project_management\Uninstall;

use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;

/**
 * Uninstalls taxonomy vocabularies and terms.
 */
class TaxonomyUninstaller {

  /**
   * Runs the uninstallation process.
   */
  public function uninstall() {
	$this->deleteProjectCategoryVocabulary();
  }

  /**
   * Deletes the 'Project Category' vocabulary and its terms.
   */
  private function deleteProjectCategoryVocabulary() {
	$vid = 'project_category';

	if ($vocab = Vocabulary::load($vid)) {
	  // Delete all terms in the vocabulary.
	  $terms = \Drupal::entityTypeManager()
		->getStorage('taxonomy_term')
		->loadByProperties(['vid' => $vid]);

	  foreach ($terms as $term) {
		$term->delete();
	  }

	  // Delete the vocabulary.
	  $vocab->delete();
	}
  }
}
