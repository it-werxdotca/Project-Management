<?php

declare(strict_types=1);

namespace Drupal\project_management\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormAlterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Alters the Project content type form.
 */
class ProjectFormAlter implements FormAlterInterface {

  /**
   * {@inheritdoc}
   */
  public static function alterForm(array &$form, FormStateInterface $form_state, string $form_id): void {
	// Ensure we target the Project content type form.
	if ($form_id === 'node_project_form') {
	  if (!empty($form['field_invited_users'])) {
		// Set the autocomplete route to fetch users.
		$form['field_invited_users']['widget'][0]['target_id']['#autocomplete_route_name'] = 'entity.user.autocomplete';
	  }
	}
  }
}
