<?php

function delete_dataset_collaborator_form($form, &$form_state)
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
            '#type'        => 'fieldset',
            '#title'       => t('Collaborators'),
            '#collapsible' => FALSE, 
            '#collapsed'   => FALSE,
    );

    $i = 0;
    $str = '';

    $form['collaboratorBox']['table_header'] = array(
        '#markup'   => '
            <table>
                <tr>
		    <td><b>Delete</b></td>
                    <td><b>Collaborator Name</b></td>
                    <td><b>Role</b></td>
		    <td><b>Contribution</b></td>
		</td>
        ',    
    );
    
	$cms_client_instance = new CMS_Client();
	$members_list = $cms_client_instance->delegate_display_get_dataset_collaborators($dataset_id);
    
   while($i < $members_list['total'])
    {
	$form['collaboratorBox'][$i]['delete_collaborator'] = array(
	    '#type' => 'checkbox',
	    '#prefix' => '<tr><td>',
	    '#suffix' => '</td>',
	);
	
	$form['collaboratorBox'][$i]['member_name'] = array(
        '#markup'   => '<td>'.$members_list[$i]['name'].'</td>',
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
	
    $form['total_collaborators'] = array(
	'#type' => 'hidden',
	'#value' => $i,
    );
    
    $form['collaboratorBox']['table_footer'] = array(
        '#markup'   => '</table>',
    );
    
    $form['total_collaborators'] = array(
		'#type' => 'hidden',
		'#value' => $i,
    );
    
    $form['done_delete_collaborators'] = array(
    	'#type' => 'submit', '#value' => t('Back')
    );
    
	if($members_list['total'] >= 1)
	{
		$form['delete_collaborators'] = array(
			'#type' => 'submit', 
			'#value' => t('Delete Collaborators'),
			/*'#attributes' => array('onclick' => 
								'if(!confirm("Do You Want to Delete the Collaborator(s)?"))
								{
									return false;
								}
								'
							),*/
		);
	}
    
    return $form;

}

function delete_dataset_collaborator_form_validate($form, &$form_state)
{
	$is_delete_button_clicked = $form_state['clicked_button']['#id'] == 'edit-delete-collaborators';
	
	if($is_delete_button_clicked == 1)
	{
		$i = 0;
		$count = 0;
		$total_collaborators = $form_state['values']['total_collaborators'];
		while($i < $total_collaborators)
		{
			$delete_member = $form_state['values']['collaboratorBox'][$i]['delete_collaborator'];
			if($delete_member == 0)
			{
				$count++;
			}
			$i++;
		}
		if($count == $total_collaborators)
		{
			$k = $total_collaborators-1;
			form_set_error("$k][delete_collaborator", "No Collabortor(s) were selected to be deleted");
		}
	}
}

function delete_dataset_collaborator_form_submit($form, &$form_state)
{
    if($form_state['clicked_button']['#id'] == 'edit-delete-collaborators')
    {
		$i = 0;
		$dataset_id    = $form_state['values']['dataset_id'];

		$total_collaborators = $form_state['values']['total_collaborators'];

		while($i < $total_collaborators)
		{
			$delete_member = $form_state['values']['collaboratorBox'][$i]['delete_collaborator'];
			if($delete_member == 1)
			{
				$delete_member_id = $form_state['values']['collaboratorBox'][$i]['member_id'];
				$cms_instance = new CMS_Client();
				$message = $cms_instance->delegate_remove_dataset_collaborator($dataset_id,$delete_member_id);
				drupal_set_message($message);
			}
			
			$i++;
		}
		
		$values = array('query' =>array('ds_id' => $dataset_id));
				
		drupal_goto('deleteDatasetCollaborators',$values);
    }
    if($form_state['clicked_button']['#id'] == 'edit-done-delete-collaborators')
    {
		$dataset_id = $form_state['values']['dataset_id'];
		
		$values = array('query' =>array('ds_id' => $dataset_id));
				
		drupal_goto('viewCollaborators',$values);
    }
}



?>