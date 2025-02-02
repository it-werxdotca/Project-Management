<?php

namespace Drupal\project_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\project_management\Api\ProjectApi;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectController extends ControllerBase {

  /**
   * The Project API service.
   *
   * @var \Drupal\project_management\Api\ProjectApi
   */
  protected $projectApi;

  /**
   * ProjectController constructor.
   *
   * @param \Drupal\project_management\Api\ProjectApi $project_api
   */
  public function __construct(ProjectApi $project_api) {
	$this->projectApi = $project_api;
  }

  /**
   * Factory method to instantiate the controller with dependencies.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   *
   * @return \Drupal\project_management\Controller\ProjectController
   *   The controller instance.
   */
  public static function create(ContainerInterface $container) {
	return new static(
	  $container->get('project_management.api')
	);
  }

  /**
   * API endpoint to get project data.
   */
  public function apiProjects() {
	// Fetch projects using the service
	$project_data = $this->projectApi->getProjects();

	// Allow other modules to alter the project data
	$project_data = $this->projectApi->alterProjectData($project_data);

	return $project_data;
  }
}
