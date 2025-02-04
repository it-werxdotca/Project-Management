<?php
 
 namespace Drupal\project_management\Form;
 
 use Drupal\Core\Form\FormBase;
 use Drupal\Core\Form\FormStateInterface;
 use Drupal\node\Entity\Node;
 use Drupal\user\Entity\User;
 
 class ProjectRoleForm extends FormBase {
 
   /**
	* {@inheritdoc}
	*/
   public function getFormId() {
	 return 'project_management_project_roles';
   }
 
   /**
	* {@inheritdoc}
	*/
   public function buildForm(array $form, FormStateInterface $form_state, $project_id = NULL) {
	 $project = Node::load($project_id);
	 $users = \Drupal::entityTypeManager()->getStorage('user')->loadMultiple();
	 
	 $form['project'] = [
	   '#type' => 'value',
	   '#value' => $project_id,
	 ];
	 
	 $form['roles'] = [
	   '#type' => 'select',
	   '#title' => $this->t('Assign Roles'),
	   '#options' => [
		 'client' => 'Client',
		 'team_member' => 'Team Member',
		 'accounting' => 'Accounting',
	   ],
	 ];
 
	 $form['users'] = [
	   '#type' => 'select',
	   '#title' => $this->t('Select User'),
	   '#options' => array_map(function ($user) {
		 return $user->getUsername();
	   }, $users),
	 ];
 
	 $form['submit'] = [
	   '#type' => 'submit',
	   '#value' => $this->t('Assign Role'),
	 ];
 
	 return $form;
   }
 
 	/**
	* {@inheritdoc}
	*/
   public function submitForm(array &$form, FormStateInterface $form_state) {
	 $project_id = $form_state->getValue('project');
	 $user_id = $form_state->getValue('users');
	 $role = $form_state->getValue('roles');
   
	 // Get the current user.
	 $current_user = \Drupal::currentUser();
   
	 // Check if the current user has the required role (Project Manager or Administrator).
	 if ($current_user->hasRole('project_manager') || $current_user->hasRole('administrator')) {
	   // Load the user entity.
	   $user = User::load($user_id);
	   
	   if ($user) {
		 // Check if the user already has the role.
		 if (!$user->hasRole($role)) {
		   // Add the role to the user.
		   $user->addRole($role);
		   $user->save();
		   
		   // Provide feedback.
		   \Drupal::messenger()->addMessage($this->t('Role assigned to @user for the project.', ['@user' => $user->getUsername()]));
		 }
		 else {
		   // The user already has the role.
		   \Drupal::messenger()->addMessage($this->t('@user already has the role.', ['@user' => $user->getUsername()]));
		 }
	   }
	   else {
		 \Drupal::messenger()->addError($this->t('User not found.'));
	   }
	 }
	 else {
	   // Current user doesn't have the required roles.
	   \Drupal::messenger()->addError($this->t('You do not have the required permissions to assign roles.'));
	 }
   }
 }
