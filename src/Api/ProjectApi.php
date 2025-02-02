<?php

namespace Drupal\project_management\Api;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectApi {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * ProjectApi constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
	$this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get all projects as a JSON response.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getProjects() {
	// Retrieve all projects
	$projects = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'project']);
	$project_data = [];

	foreach ($projects as $project) {
	  // Gather relevant fields for the project
	  $project_data[] = [
		'id' => $project->id(),
		'title' => $project->getTitle(),
		'status' => $project->get('field_status')->value, // Example field
		'created' => $project->getCreatedTime(),
		// Add other fields as needed
	  ];
	}

	// Return the project data as a JSON response
	return new JsonResponse($project_data);
  }

  /**
   * A hook for additional project data, allowing other modules to alter the API response.
   *
   * @return array
   */
  public function alterProjectData(array $project_data) {
	// Allow other modules to modify project data
	\Drupal::moduleHandler()->alter('project_management', 'alter_project_data', $project_data);
	return $project_data;
  }
}
