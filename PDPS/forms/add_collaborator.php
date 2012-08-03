<?php
include 'classes/cms_client.php';

function add_collaborator_form($form, &$form_submit)
{

    $form['#tree'] = TRUE;
    
    $form['collaboratorBox'] = array(
        '#type' => 'fieldset',
        '#title'       => t('Collaborator Information'),
		'#collapsible' => FALSE, 
		'#collapsed'   => FALSE,
    );
    
    $form['collaboratorBox']['name'] = array(
        '#type' => 'textfield',
        '#title' => t('Name'),
    );
    
    $form['collaboratorBox']['email'] = array(
        '#type' => 'textfield',
        '#title' => t('E-mail'),
    );
    
    $form['collaboratorBox']['affiliation'] = array(
        '#type' => 'textfield',
        '#title' => t('Affiliation'),
    );
    
   $form['add_collaborator'] = array(
	'#type' => 'submit',
	'#value' => t('Add')
    );
   
   $form['cancel_add_collaborator'] = array(
	'#type' => 'submit',
	'#value' => t('Cancel')
    );
   
    return $form;
}

function add_collaborator_form_validate($form,&$form_state)
{
    $is_add_button_clicked = $form_state['clicked_button']['#id'] == 'edit-add-collaborator';
    
    if($is_add_button_clicked == 1)
    {
	$mail = $form_state['values']['collaboratorBox']['email'];
	$is_valid_email = valid_email_address($mail);
    
	$collaborator_name =  $form_state['values']['collaboratorBox']['name'];
	
	if(empty($collaborator_name))
	{
	    form_set_error('name','Need to enter a name for the collaborator');
	}
	if($is_valid_email == FALSE)
	{
	    form_set_error('email','Not A Valid E-mail');
	}
	if(empty($mail))
	{
	    form_set_error('email','Enter an e-mail for the collaborator');
	}
    }
}

function add_collaborator_form_submit($form, &$form_state)
{
    if($form_state['clicked_button']['#id'] == 'edit-add-collaborator')
    {
		global $user;
		$collaborator['user'] 	     = $user->uid;
		$collaborator['name']        = $form_state['values']['collaboratorBox']['name'];
		$collaborator['email']       = $form_state['values']['collaboratorBox']['email'];
		$collaborator['affiliation'] = $form_state['values']['collaboratorBox']['affiliation'];
		
		$cms = new CMS_Client();
		$message_string = $cms->delegate_add_collaborator($collaborator);
		drupal_set_message($message_string);
    }
    if($form_state['clicked_button']['#id'] == 'edit-cancel-add-collaborator')
    {
		$form_state['redirect'] = 'node/dataset_list';
    }
}


?>