<?php

function add_dataset_collaborator_form($form, &$form_state)
{
    $dataset_id = $_GET['ds_id'];
    $msg = is_dataset_id_valid($dataset_id);
    
    $form['dataset_id'] = array(
        '#type' => 'hidden',
        '#value' => $dataset_id,
    );

    
    include 'navigation_bar.php';
    
    $form['#tree'] = TRUE;
    
    $form['collaboratorBox'] = array(
	'#type' => 'fieldset',
	'#title' => t('Collaborator'),
    );
    
    global $user;
    $cms_instance = new CMS_Client();
    $collaborator_list = $cms_instance->delegate_get_collaborator_list($user->uid);
    
    $form['collaboratorBox']['collaborator'] = array(
      '#type' => 'select',
      '#title' => t('Collaborator'),
      '#options' => $collaborator_list,
   );
    
   $form['collaboratorBox']['role'] = array(
	'#type' => 'select',
	'#title' => t('Role'),
	'#options' => array(
	    'r0' => t('Select Role'),
	    'r1' => t('PI'),
	    'r2' => t('Co-PI'),
	    'r3' => t('Technician'),
	    'r4' => t('Staff'),
	    'r5' => t('Research Assistant'),
	    'r6' => t('Collaborator'),
	    'r7' => t('Owner'),
	),
   );
   
   $form['collaboratorBox']['collaborator_description'] = array(
     '#type' => 'textarea',
     '#title' => t('Contribution'),
   );
   
    $form['done_add_collaborators'] = array(
    	'#type' => 'submit', '#value' => t('Back')
    );
    
    $form['add_another_collaborators'] = array(
    	'#type' => 'submit', '#value' => t('Add')
    );
    
    return $form;
}

function add_dataset_collaborator_form_submit($form, &$form_state)
{
    if($form_state['clicked_button']['#id'] == 'edit-done-add-collaborators')//Done
    {
		$add_collaborator['dataset_id'] = $form_state['values']['dataset_id'];
		
		$values = array('query' =>array('ds_id' => $add_collaborator['dataset_id']));
        
        drupal_goto('viewCollaborators',$values);
    }
    if($form_state['clicked_button']['#id'] == 'edit-add-another-collaborators')//Done
    {
		$add_collaborator['dataset_id'] = $form_state['values']['dataset_id'];
		$add_collaborator['collaborator'] = $form_state['values']['collaboratorBox']['collaborator'];
		$add_collaborator['role'] 	= $form_state['values']['collaboratorBox']['role'];
		$add_collaborator['contribution'] = $form_state['values']['collaboratorBox']['collaborator_description'];
		
		$cms_instance = new CMS_Client();
		$insert_message = $cms_instance->delegate_add_dataset_collaborator($add_collaborator['dataset_id'],$add_collaborator);
		drupal_set_message($insert_message);
		$values = array('query' =>array('ds_id' => $add_collaborator['dataset_id']));
			
		drupal_goto('addDatasetCollaborator',$values);
    }
}

function add_dataset_collaborator_form_validate($form, &$form_state)
{
	$is_add_collaborator_button_clicked = $form_state['clicked_button']['#id'] == 'edit-add-another-collaborators';
	if($is_add_collaborator_button_clicked == TRUE)
	{
		$collaborator = $form_state['values']['collaboratorBox']['collaborator'];
		$role 	      = $form_state['values']['collaboratorBox']['role'];
		$collaborator_contribution = empty($form_state['values']['collaboratorBox']['collaborator_description']);
		$dataset_id = $form_state['values']['dataset_id'];
		$cms_client_instance = new CMS_Client();
		
		
		if($collaborator == 0)
		{
			form_set_error('collaborator','Need to select a collaborator');
			
		}
		if($collaborator != 0)
		{
			$is_collaborator_already_in_dataset = $cms_client_instance->delegate_check_collaborator_for_dataset($dataset_id,$collaborator);
			if($collaborator == $is_collaborator_already_in_dataset)
			{
				form_set_error('collaborator','This collaborator already belongs to this dataset');
			}
		}
		if($role == 'r0')
		{
			form_set_error('role','Need select a role for the collaborator');
		}
		if($collaborator_contribution == TRUE)
		{
			form_set_error('collaborator_descrition','Need to enter a contribution for the collaborator in the dataset');
		}
	}
}

?>