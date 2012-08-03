<?php

function view_dataset_collaborators_form($form, &$form_state)
{
    $dataset_id = $_GET['ds_id'];
    
    $msg = is_dataset_id_valid($dataset_id);
    
    $form['dataset_id'] = array(
        '#type' => 'hidden',
        '#value' => $dataset_id,
    );
    
    $form['#tree'] = TRUE;
    
     include 'navigation_bar.php';
    
    $form['collaboratorBox'] = array(
            '#type'        => 'fieldset',
            '#title'       => t('Collaborators'),
            '#collapsible' => FALSE, 
            '#collapsed'   => FALSE,
    );
    
    $cms_client_instance = new CMS_Client();
    
    $members_list = $cms_client_instance->delegate_display_get_dataset_collaborators($dataset_id);
    
    $i = 0;
    $str = '';

    //$form = $members_list['collaborator_form'];
    
    $form['collaboratorBox']['table_header'] = array(
        '#markup'   => '
            <table>
                <tr>
                    <td><b>Collaborator Name</b></td>
                    <td><b>Role</b></td>
					<td><b>Contribution</b></td>
				</tr>
        ',    
    );
    
   while($i < $members_list['total'])
    {
		$form['collaboratorBox'][$i]['member_name'] = array(
			'#markup'   => '<tr><td>'.$members_list[$i]['name'].'</td>',
		);
		$form['collaboratorBox'][$i]['member_id'] = array(
			'#type' => 'hidden',
			'#default_value' => $members_list[$i]['id'],
		);
		
		$form['collaboratorBox'][$i]['role'] = array(
		'#type' => 'select',
		'#default_value' => $members_list[$i]['role_type'],
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
		'#prefix' => '<td>',
		'#suffix' => '</td>',
		);
		
		$form['collaboratorBox'][$i]['contribution'] = array(
			'#type' => 'textarea',
			'#default_value' => $members_list[$i]['contribution'],
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);
	    
	    $i++;
	}
    
    $form['collaboratorBox']['table_footer'] = array(
        '#markup'   => '</table>',
    );
    
   $form['total_collaborators'] = array(
	'#type' => 'hidden',
	'#value' => $members_list['total'],
    );
    
    $form['done_collaborators'] = array(
    	'#type' => 'submit', '#value' => t('Back')
    );
    
	if($members_list['total'] >= 1)
    {
		$form['save_collaborators'] = array(
			'#type' => 'submit', '#value' => t('Save')
		);
	}
    
    $form['add_collaborators'] = array(
    	'#type' => 'submit', '#value' => t('Add Collaborators')
    );
    
    if($members_list['total'] >= 1)
    {
		$form['delete_collaborators'] = array(
			'#type' => 'submit', '#value' => t('Delete Collaborator')
		);
    }
    
    return $form;
}

function  view_dataset_collaborators_form_validate($form, &$form_state)
{
	$total_number_of_collaborators = $form_state['values']['total_collaborators'];

	$i = 0;
	
	while($i < $total_number_of_collaborators)
	{
	
		$member_role 	     = $form_state['values']['collaboratorBox'][$i]['role'];
		$member_contribution = empty($form_state['values']['collaboratorBox'][$i]['contribution']);
		
		if($member_role == 'r0')
		{
			form_set_error('role','Need to select a role for the collaborator');
		}
		if($member_contribution == TRUE)
		{
			form_set_error('contribution','Need to enter the contribution of the collaborator');
		}	
		
		$i++;
	}
}

function view_dataset_collaborators_form_submit($form, &$form_state)
{
    if($form_state['clicked_button']['#id'] == 'edit-done-collaborators')//Done
    {
		drupal_goto('node/dataset_list');
    }
    if($form_state['clicked_button']['#id'] == 'edit-add-collaborators')//Done
    {
		$dataset_id = $form_state['values']['dataset_id'];
		
		$values = array('query' =>array('ds_id' => $dataset_id));
				
		drupal_goto('addDatasetCollaborator',$values);
    }
    if($form_state['clicked_button']['#id'] == 'edit-save-collaborators')//Done
    {
	    $total_number_of_collaborators = $form_state['values']['total_collaborators'];
	    
	    $add_member['dataset_id'] = $form_state['values']['dataset_id'];
	    $i = 0;
	    
	    while($i < $total_number_of_collaborators)
	    {
			$add_member[$i]['member_id']    = $form_state['values']['collaboratorBox'][$i]['member_id'];
			$add_member[$i]['role'] 	    = $form_state['values']['collaboratorBox'][$i]['role'];
			$add_member[$i]['contribution'] = $form_state['values']['collaboratorBox'][$i]['contribution'];
			
			$i++;
	    }
	    
	    $add_member['total'] = $i;
	    
	    $cms_client_instance = new CMS_Client();
	    $message = $cms_client_instance->delegate_update_dataset_collaborators($add_member);
	    drupal_set_message($message);
	    $values = array('query' =>array('ds_id' => $add_member['dataset_id']));
		    
	    drupal_goto('viewCollaborators',$values);
    }
    if($form_state['clicked_button']['#id'] == 'edit-delete-collaborators')
    {
		$dataset_id = $form_state['values']['dataset_id'];
		$values = array('query' =>array('ds_id' => $dataset_id));
				
		drupal_goto('deleteDatasetCollaborators',$values);
    }
}


?>