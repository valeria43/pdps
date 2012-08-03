<?php

function view_dataset_document_form($form, &$form_state)
{
    if(isset($_GET['ds_id']) && isset($_GET['doc_id']))
    {
        $_SESSION['ds_id'] = $_GET['ds_id'];
        $msg = is_dataset_id_valid($_SESSION['ds_id']);
        $_SESSION['doc_id'] = $_GET['doc_id'];

    }
    
    $dataset_id = $_SESSION['ds_id'];
    $document_id = $_SESSION['doc_id'];
    
     include 'navigation_bar.php';
    
    $form['document_id'] = array(
        '#type' => 'hidden',
        '#value' => $document_id,
    );
    
    $form['dataset_id'] = array(
		'#type' => 'hidden',
		'#value' => $dataset_id,
    );
    
   $cms_instance = new CMS_Client;
   $dataset_name = $cms_instance->delegate_display_get_dataset_name($dataset_id);
    
    $form['#tree'] = TRUE;
    
    $document = $cms_instance->delegate_display_get_document_information($document_id);
    
    $form['documentBox'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Document Information'),
        '#collapsible' => FALSE, 
		'#collapsed'   => FALSE,
        '#prefix'      => '<div id="documents-fieldset-wrapper">',
        '#suffix'      => '</div>',
    );


    $form['documentBox']['documentName']  = array(
        '#type'  => 'textfield',
		'#default_value' => $document['name'], 
    );
    
    $form['documentBox']['fileDocumentLabel'] = array(
        '#markup' => '<b>File</b>',    
    );
    
    global $base_url;
    $link = $base_url.'/sites/default/files/DATASETS';
    $file_link = $link.'/'.$dataset_name.'/Documents/'.$document['filename'];
   
    $form['documentBox']['previous_file'] = array(
		'#type' => 'checkbox',
		'#title' => '<a href='.$file_link.'>'.$document['filename'].'</a>',
		'#description' => 'Delete',
    );
    
    $form['previous_file_id'] = array(
	'#type' => 'hidden',
	'#value' => $document['file_id'],
    );
    
    $form['previous_file_name'] = array(
	'#type' => 'hidden',
	'#value' => $document['filename'],
    );
    
    $form['documentBox']['uploadfileDocumentLabel'] = array(
        '#markup' => '<b>Upload Another Document</b><br />',    
    );
    
    $form['documentBox']['fid'] = array(
        '#type'            => 'managed_file',
        '#upload_location' => 'public://temp_files',
		'#upload_validators' => array(
                'file_validate_extensions' => array('gif png jpg txt jpeg rtf docx xml doc zip rar pdf pptx ppt rar sql ppt bin avi c java cpp cpp csv dat cmd tiff data xlsx xls'),
        ),
    );
    
    $document_types = $cms_instance->delegate_get_document_types();
    
    $form['documentBox']['documentType']  = array(
        '#type'    => 'select',
        '#title'   => t('Document Type'),
        '#options' => $document_types,
		'#default_value' => $document['type'],
    );
    
    $form['documentBox']['documentTypeOthter'] = array(
	'#type' => 'textfield',
	'#title' => 'Other Document Type',
	'#default_value' => $document['new_type'],
	'#states' => array(
	    'visible' => array(
	      ':input[name="documentBox[documentType]"]' => array('value' => 0),
	    ),
	  ),
	);
    
   $form['documentBox']['documentApplication']  = array(
        '#type'        => 'textfield',
        '#title'       => t('Document Application'),
        '#description' => t('Enter the name of the application in which the document can be open with.'),
		'#default_value' => $document['application'],
    );
    
    $form['documentBox']['documentDescription']  = array(
        '#type'  => 'textarea',
        '#title' => t('Document Description'),
		'#default_value' => $document['description'],
    );

    $form['done_document'] = array(
	'#type'  => 'submit',
        '#value' => t('Back'),
    );
    
    $form['save_document'] = array(
	'#type'  => 'submit',
        '#value' => t('Save'),
    );
    
    
    return $form;

}

function view_dataset_document_form_validate($form,&$form_state)
{
   $is_back_button_clicked = $form_state['clicked_button']['#id'] == 'edit-done-document';
   $is_add_button_clicked  = $form_state['clicked_button']['#id'] == 'edit-save-document';
    if($is_add_button_clicked == 1)
    {
        $file_id 			  = empty($form_state['values']['documentBox']['fid']);
        $document_name  	  = empty($form_state['values']['documentBox']['documentName']);
        $document_application = empty($form_state['values']['documentBox']['documentApplication']);
	$delete_file 		  = $form_state['values']['documentBox']['previous_file'];
	$document_description = empty($form_state['values']['documentBox']['documentDescription']);
	$document_type = $form_state['values']['documentBox']['documentType'];
	
	if($delete_file == 1 && $file_id == TRUE)
	{
	    form_set_error('fid','Need to Upload the File for the Document');
	}
        if($document_name == TRUE)
        {
            form_set_error('documentName','Need to enter a name for the document');
        }
        if($document_application == TRUE)
        {
            form_set_error('documentApplication','Need to enter the name of the application');
        }
	if($document_description == TRUE)
        {
            form_set_error('documentDescription','Need to enter a description for the document');
        }
	 if($document_type == 0)
        {
            $other_document_type = empty($form_state['values']['documentBox']['documentTypeOthter']);
            if($other_document_type == TRUE)
            {
                form_set_error('documentTypeOthter', 'Need to enter a name for the other document type');
            }
        }
    }
}

function view_dataset_document_form_submit($form, &$form_state)
{
    if($form_state['clicked_button']['#id'] == 'edit-done-document')
    {
	$dataset_id = $form_state['values']['dataset_id'];
	$cms_instance = new CMS_Client;
	$cms_instance->delegate_delete_temp_files();
	$values = array(
				'query' =>array(
						    'ds_id'  => $dataset_id,
						)
				);
	drupal_goto('viewDocuments', $values);
    }
    if($form_state['clicked_button']['#id'] == 'edit-save-document')
    {
		$update_document['file_name'] 	  = $form_state['values']['previous_file_name'];
		$update_document['document_id']   = $form_state['values']['document_id'];
		$update_document['dataset_id']    = $form_state['values']['dataset_id'];
		$update_document['name']          = $form_state['values']['documentBox']['documentName'];
		$update_document['document_type'] = $form_state['values']['documentBox']['documentType'];
		$update_document['application']   = $form_state['values']['documentBox']['documentApplication'];
		$update_document['description']   = $form_state['values']['documentBox']['documentDescription'];
		
		if($update_document['document_type'] == 0)
		{
		    $update_document['new_type'] = $form_state['values']['documentBox']['documentTypeOthter'];
		}
		
		$delete_file = $form_state['values']['documentBox']['previous_file'];
		$cms_instance = new CMS_Client;
		
		if($delete_file == 1)
		{
		    $update_document['file_id'] = $form_state['values']['documentBox']['fid'];
		    
		    $message_string = $cms_instance->delegate_update_dataset_document($update_document, 1);
		    //update_dataset_document($update_document, 1);
		}
		else
		{
		    $message_string = $cms_instance->delegate_update_dataset_document($update_document, 0);
		}
		
		drupal_set_message($message_string);
		$cms_instance->delegate_delete_temp_files();		
		$values = array(
				'query' =>array(
						    'doc_id' => $update_document['document_id'],
						    'ds_id'  => $update_document['dataset_id'],
						)
				);
			
		drupal_goto('viewDocument',$values);
    }
}

?>