<?php

function collaborator_list_form($form, &$form_state)
{
    if(isset($_GET['dl']))
    {
	$delete = $_GET['dl'];
	
	$html_table = '<table>
			    <tr>
				<td>Delete</td>
				<td>Name</td>
				<td>E-mail</td>
				<td>Affiliation</td>
			    <tr>';
    }
    else
    {
	$delete = 0;
	
	$html_table = '<table>
			    <tr>
				<td><b>Name</b></td>
				<td><b>E-mail</b></td>
				<td><b>Affiliation</b></td>
			    <tr>';
    }

    global $user;
    $user_id = $user->uid;

    $form['#tree'] = TRUE;
    
    $form['collaboratorBox'] = array(
	'#type'        => 'fieldset',
	'#title'       => t('Collaborators'),
	'#collapsible' => FALSE, 
	'#collapsed'   => FALSE,
    );
    
    $cms_instance = new CMS_Client();
    $collaborator = $cms_instance->delegate_get_user_collaborators($user_id);

    $total = $collaborator['total'];
    $i= 0;
    
    $form['collaboratorBox']['table_header'] = array(
	'#markup' => $html_table,
    );
    
    while($i < $total)
    {
	if($delete == 1)
	{
	    $form['collaboratorBox'][$i]['delete_collaborator'] = array(
		'#type' => 'checkbox',
		'#prefix' => '<tr><td>',
		'#suffix' => '</td>',
	    );
	    
	    $form['collaboratorBox'][$i]['name'] = array(
		'#type' => 'textfield',
		'#default_value' => $collaborator[$i]['name'],
		'#prefix' => '<td>',
		'#suffix' => '</td>',
	    );
	    
	    $form['collaboratorBox'][$i]['id'] = array(
		'#type' => 'hidden',
		'#default_value' => $collaborator[$i]['id'],
	    );
	    
	    $form['collaboratorBox'][$i]['email'] = array(
		'#type' => 'textfield',
		'#default_value' => $collaborator[$i]['email'],
		'#prefix' => '<td>',
		'#suffix' => '</td>',
	    );
	    
	    $form['collaboratorBox'][$i]['affiliation'] = array(
		'#type' => 'textfield',
		'#default_value' => $collaborator[$i]['affiliation'],
		'#prefix' => '<td>',
		'#suffix' => '</td></tr>',
	    );
	}
	if($delete == 0)
	{
	    $form['collaboratorBox'][$i]['name'] = array(
		'#type' => 'textfield',
		'#default_value' => $collaborator[$i]['name'],
		'#prefix' => '<tr><td>',
		'#suffix' => '</td>',
	    );
	    
	    $form['collaboratorBox'][$i]['id'] = array(
		'#type' => 'hidden',
		'#default_value' => $collaborator[$i]['id'],
	    );
	    
	    $form['collaboratorBox'][$i]['email'] = array(
		'#type' => 'textfield',
		'#default_value' => $collaborator[$i]['email'],
		'#prefix' => '<td>',
		'#suffix' => '</td>',
	    );
	    
	    $form['collaboratorBox'][$i]['affiliation'] = array(
		'#type' => 'textfield',
		'#default_value' => $collaborator[$i]['affiliation'],
		'#prefix' => '<td>',
		'#suffix' => '</td></tr>',
	    );
	}
	
	$i++;
    }
    
    $form['collaboratorBox']['table_footer'] = array(
	'#markup' => '</table>',
    );
    
    $form['total_number_of_collaborators'] = array(
	'#type' => 'hidden',
	'#default_value' => $total,
    );
    
    if($delete == 0)
    {
		$form['save_collaborator'] = array(
		    '#type' => 'submit', '#value' => t('Save')
		);
		
		$form['delete_collaborator'] = array(
		    '#type' => 'submit', '#value' => t('Delete Collaborator(s)'),
		);
    }
    
    if($delete == 1)
    {
		$form['delete_collaborator_from_list'] = array(
    	'#type' => 'submit',
		'#value' => t('Delete Collaborator(s)'),
		);
    }
    
    $form['cancel_collaborator'] = array(
    	'#type' => 'submit', '#value' => t('Cancel')
    );
    
    return $form;
}


function collaborator_list_form_submit($form, &$form_state)
{
    global $user;
    $user_id = $user->uid;
    
    if($form_state['clicked_button']['#id'] == 'edit-save-collaborator')
    {
		$total = $form_state['values']['total_number_of_collaborators'];
		$i = 0;
		
		while($i < $total)
		{
			$collaborator_list[$i]['id']          = $form_state['values']['collaboratorBox'][$i]['id'];
			$collaborator_list[$i]['name']        = $form_state['values']['collaboratorBox'][$i]['name'];
			$collaborator_list[$i]['email']       = $form_state['values']['collaboratorBox'][$i]['email'];
			$collaborator_list[$i]['affiliation'] = $form_state['values']['collaboratorBox'][$i]['affiliation'];
			$i++;
		}
		$cms_instance = new CMS_Client();
		$message_string = $cms_instance->delegate_update_user_collaborators($user_id,$collaborator_list, $total);
		drupal_set_message($message_string);
	}
	if($form_state['clicked_button']['#id'] == 'edit-delete-collaborator')
	{
		$values = array('query' =>array('dl' => 1));
				
		drupal_goto('node/collaboratorList',$values);
	}//delete_collaborator_from_list
	if($form_state['clicked_button']['#id'] == 'edit-delete-collaborator-from-list')
	{
	
		$total = $form_state['values']['total_number_of_collaborators'];
		$i = 0;
		$j = 0;
		
		while($i < $total)
		{
			$delete_collaborator = $form_state['values']['collaboratorBox'][$i]['delete_collaborator'];
			
			if($delete_collaborator == 1)
			{
				$delete_user_collaborator[$j] = $form_state['values']['collaboratorBox'][$i]['id'];
				$j++;
			}
			$i++;
		}
		$cms_instance = new CMS_Client();
		$message_string = $cms_instance->delegate_delete_user_collaborators($user_id,$delete_user_collaborator,$j);
		drupal_set_message($message_string);
		
		$values = array('query' =>array('dl' => 1));
				
		drupal_goto('node/collaboratorList',$values);
    }//node/dataset_list
    if($form_state['clicked_button']['#id'] == 'edit-cancel-collaborator')
    {
		drupal_goto('node/dataset_list');
    }
}

function collaborator_list_form_validate($form,&$form_state)
{
	$is_delete_button_clicked = $form_state['clicked_button']['#id'] == 'edit-delete-collaborator-from-list';
	$is_save_button_clicked   = $form_state['clicked_button']['#id'] == 'edit-save-collaborator';
	if($is_save_button_clicked == TRUE)
	{
		$total = $form_state['values']['total_number_of_collaborators'];
		$i = 0;
		
		while($i < $total)
		{
			$is_valid_email = valid_email_address($form_state['values']['collaboratorBox'][$i]['email']);
			if($is_valid_email == FALSE)
			{
				form_set_error("$i][email","Not A Valid E-mail");
			}	
			$i++;
		}	
	}
	if($is_delete_button_clicked == TRUE)
	{
		$total = $form_state['values']['total_number_of_collaborators'];
		$i = 0;
		$j = 0;
		
		while($i < $total)
		{
			$delete_collaborator = $form_state['values']['collaboratorBox'][$i]['delete_collaborator'];//['collaboratorBox'][$i]['delete_collaborator']
			
			if($delete_collaborator == 0)
			{
				$j++;
			}
			$i++;
		}
		if($j == $total)
		{
			$k = $total-1;
			form_set_error("$k][delete_collaborator","No Collaborators Were Selected to be Deleted ");
	
		}
	}
}

?>