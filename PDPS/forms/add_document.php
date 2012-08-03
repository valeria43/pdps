<?php
//add_document_form
function add_document_form($form, &$form_state)
{
	if(isset($_GET['ds_id']))
	{
		$_SESSION['ds_id'] = $_GET['ds_id'];
		$msg = is_dataset_id_valid($_SESSION['ds_id']);
	}
		$form['#tree'] = TRUE;
		

		if(empty($form_state['number_of_documents'])) 
		{
			$form_state['number_of_documents'] = 1;
		}
		$dataset_id = $_SESSION['ds_id'];
		$_SESSION['ds_id'] = NULL;
		
		$form['dataset_id'] = array(
			'#type' => 'hidden',
			'#default_value' => $dataset_id,
		);

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
                        '#title' => 'Document Name',
		);
		
		$form['documentBox']['fileDocumentLabel'] = array(
			'#markup' => '<b>Select the Document to Upload</b><br />',    
		);
		
		$form['documentBox']['fid'] = array(
			'#type'            => 'managed_file',
			'#upload_location' => 'public://temp_files',
			'#size'            => 22,
            '#upload_validators' => array(
                'file_validate_extensions' =>array('gif png jpg txt jpeg rtf docx xml doc zip rar pdf pptx ppt rar sql ppt bin avi c java cpp cpp csv dat cmd tiff data xlsx xls'),
            ),
		);
		
		$cms_instance = new CMS_Client();
		$document_types = $cms_instance->delegate_get_document_types();
		
		$form['documentBox']['documentType']  = array(
			'#type'    => 'select',
			'#title'   => t('Document Type'),
			'#options' => $document_types,
			'#default_value' => 1,
		);
                
                $form['documentBox']['documentTypeOthter'] = array(
                    '#type' => 'textfield',
                    '#title' => 'Other Document Type',
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
		);
		
		$form['documentBox']['documentDescription']  = array(
			'#type'  => 'textarea',
			'#title' => t('Document Description'),
		);

		$form['done_add_document'] = array(
		'#type'  => 'submit',
			'#value' => t('Back'),
		);
		
		$form['add_another_document'] = array(
		'#type'  => 'submit',
			'#value' => t('Add'),
		);
		
		return $form;
}

function add_document_form_validate($form,&$form_state)
{
   $is_back_button_clicked = $form_state['clicked_button']['#id'] == 'edit-done-add-document';
   $is_add_button_clicked  = $form_state['clicked_button']['#id'] == 'edit-add-another-document';
    if($is_add_button_clicked == 1)
    {
        $file_id = empty($form_state['values']['documentBox']['fid']);
        $document_name = empty($form_state['values']['documentBox']['documentName']);
        $document_application = empty($form_state['values']['documentBox']['documentApplication']);
        $document_description = empty($form_state['values']['documentBox']['documentDescription']);
        $document_type = $form_state['values']['documentBox']['documentType'];
        if($document_type == 0)
        {
            $other_document_type = empty($form_state['values']['documentBox']['documentTypeOthter']);
            if($other_document_type == TRUE)
            {
                form_set_error('documentTypeOthter', 'Need to enter a name for the other document type');
            }
        }
        if($document_name == TRUE)
        {
            form_set_error('documentName','Need to enter a name for the document');
        }
        if($file_id == TRUE)
        {
            form_set_error('fid','Need to Upload the File for the Document');
        }
        if($document_application == TRUE)
        {
            form_set_error('documentApplication','Need to enter the name of the application');
        }
        if($document_description == TRUE)
        {
            form_set_error('documentDescription','Need to enter a description for the document');
        }
    }
}

function add_document_form_submit($form, &$form_state)
{
	if($form_state['clicked_button']['#id'] == 'edit-done-add-document')
	{
		$cms_instance = new CMS_Client;
		$cms_instance->delegate_delete_temp_files();
		$dataset_id  = $form_state['values']['dataset_id'];
		$values = array(
			'query' =>array('ds_id' => $dataset_id )
		);
		drupal_goto('viewDocuments',$values);
	}
    if($form_state['clicked_button']['#id'] == 'edit-add-another-document')
    {
        $document = array();
        
        $document['dataset_id']    = $form_state['values']['dataset_id'];
        $document['name']          = $form_state['values']['documentBox']['documentName'];
        $document['file_id']       = $form_state['values']['documentBox']['fid'];
        $document['type']          = $form_state['values']['documentBox']['documentType'];
        $document['application']   = $form_state['values']['documentBox']['documentApplication'];
        $document['description']   = $form_state['values']['documentBox']['documentDescription'];
	
        if($document['type'] == 0)
        {
            $document['new_type'] = $form_state['values']['documentBox']['documentTypeOthter']; 
        }
        
        $cms_instance = new CMS_Client;
        $message_string = $cms_instance->delegate_add_document($document);
		drupal_set_message($message_string);
        $cms_instance->delegate_delete_temp_files();
		$values = array('query' =>array('ds_id' => $document['dataset_id'] ));
		drupal_goto('addDocument',$values);
    }
    if($form_state['clicked_button']['#id'] == 'edit-add-document-cancel')
    {
    	$cms_instance = new CMS_Client;
    	$cms_instance->delegate_delete_temp_files();
		$dataset_id  = $form_state['values']['dataset_id'];
		$values = array(
			'query' =>array('ds_id' => $dataset_id )
		);
		drupal_goto('viewDocuments',$values);
    }
}


?>