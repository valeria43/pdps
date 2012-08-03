<?php

function delete_dataset_documents_form($form, &$form_state)
{
    $dataset_id = $_GET['ds_id'];
    $msg = is_dataset_id_valid($dataset_id);
    
    $form['dataset_id'] = array(
		'#type' => 'hidden',
		'#value' => $dataset_id,
	);
    
     include 'navigation_bar.php';
    
    $form['#tree'] = TRUE;
    
	$form['docLabel'] = array(
        '#type' 	   => 'fieldset',
        '#title'       => t('Documents'),
        '#collapsible' => FALSE, 
        '#collapsed'   => FALSE,
    );
	
	$cms_instance = new CMS_Client();
	$documents = $cms_instance->delegate_display_get_dataset_documents($dataset_id);
	$form['total_number_of_documents'] = array(
	    '#type' => 'hidden',
	    '#value' => $documents['total'],
	);
	if($documents['total'] >0)
	{
		$i = 0;
		while($i < $documents['total'])
		{
			$short_description = substr($documents[$i]['description'],0,277);
			$form['docLabel']['delete_document'][$i] = array(
			    '#type' => 'checkbox',
			    //'#title' => $documents[$i]['name'],
			    '#title' => '<a href=http://localhost/drupal7.12/viewDocument?doc_id='.$documents[$i]['document_id'].'&ds_id='.$dataset_id.'>'.$documents[$i]['name'].'</a>',
			    '#description' => $short_description.'...',
			);
			
			$form['docLabel']['document_name'][$i] = array(
			    '#type' => 'hidden',
			    '#default_value' => $documents[$i]['name'],
			);
			
			$form['docLabel']['document_id'][$i] = array(
			    '#type' => 'hidden',
			    '#default_value' => $documents[$i]['document_id'],
			);//$documents[$i]['file_id']
			
			
			
			$form['docLabel']['file_id'][$i] = array(
			    '#type' => 'hidden',
			    '#default_value' => $documents[$i]['file_id'],
			);
			$i++;
		}
	}
	
    $form['total_documents'] = array(
	'#type' => 'hidden',
	'#value' => $documents['total'],
    );
    
    $form['done_delete_documents'] = array(
    	'#type' => 'submit', 
	'#value' => t('Back')
    );

    if($documents['total'] >= 1)
    {
		$form['delete_documents'] = array(
			'#type' => 'submit', 
			'#value' => t('Delete Documents'),
			/*'#attributes' => array('onclick' => 
								'if(!confirm("Do You Want to Delete the Document(s)?"))
								{
									return false;
								}
								'
							),*/
		);
    }

    return $form;
}

function delete_dataset_documents_form_validate($form, &$form_state)
{
	$is_delete_button_clicked = $form_state['clicked_button']['#id'] == 'edit-delete-documents';
	if($is_delete_button_clicked == 1)
	{
		$i= 0;
		$total_number_of_documents =  $form_state['values']['total_number_of_documents'];
		$total_number_of_documents_to_delete = 0;

		while($i < $total_number_of_documents)
		{
			$delete_document    = $form_state['values']['docLabel']['delete_document'][$i];
			if($delete_document == 0)
			{
				
				$total_number_of_documents_to_delete++;
			}
			$i++;
		}
		$k = $total_number_of_documents-1;
		if($total_number_of_documents_to_delete == $total_number_of_documents)
		{
			form_set_error("$k][documentName","No document(s) were selected to be deleted");
		}
	}
}

function delete_dataset_documents_form_submit($form, &$form_state)
{
    if($form_state['clicked_button']['#id'] == 'edit-done-delete-documents')
    {
	$dataset_id  = $form_state['values']['dataset_id'];
	$values = array(
			    'query' =>array('ds_id' => $dataset_id)
			);
			
	drupal_goto('viewDocuments',$values);
    }
    if($form_state['clicked_button']['#id'] == 'edit-delete-documents')
    {
		$i= 0;
		$total_number_of_documents =  $form_state['values']['total_number_of_documents'];
		$file_name          = $form_state['values']['docLabel']['document_name'][$i];
		$dataset_id         = $form_state['values']['dataset_id'];
		$delete_document    = $form_state['values']['docLabel']['delete_document'][$i];
		$total_number_of_documents_to_delete = 0;
		
		$delete_dataset_documents['dataset_id']   = $form_state['values']['dataset_id'];

		while($i < $total_number_of_documents)
		{
			$delete_document    = $form_state['values']['docLabel']['delete_document'][$i];
			if($delete_document == 1)
			{
			$delete_dataset_documents['document_id'][$total_number_of_documents_to_delete]  = $form_state['values']['docLabel']['document_id'][$i];
			$delete_dataset_documents['file_id'][$total_number_of_documents_to_delete]      = $form_state['values']['docLabel']['file_id'][$i];
			$total_number_of_documents_to_delete++;
			}
			$i++;
		}
		
		$delete_dataset_documents['total_documents_to_delete'] = $total_number_of_documents_to_delete;
		$cms_instance = new CMS_Client;
		$message_string = $cms_instance->delegate_dataset_documents($delete_dataset_documents);
		drupal_set_message($message_string);
		$values = array( 'query' =>array('ds_id' => $dataset_id));
				
		drupal_goto('deleteDatasetDocuments',$values);
    }
    
}


?>