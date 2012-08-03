<?php

function view_documents_form($form, &$form_state)
{
	/*if(isset($_GET['ds_id']))
	{
		$_SESSION['ds_id'] = $_GET['ds_id'];
	}
	$dataset_id = $_SESSION['ds_id'];
	$_SESSION['ds_id'] = NULL;*/
	$dataset_id = $_GET['ds_id'];
	$msg = is_dataset_id_valid($dataset_id);
	
	 include 'navigation_bar.php';
	
	$form['dataset_id'] = array(
		'#type' => 'hidden',
		'#value' => $dataset_id,
	);
    
	$form['docLabel'] = array(
        '#type' 	   => 'fieldset',
        '#title'       => t('Documents'),
        '#collapsible' => FALSE, 
        '#collapsed'   => FALSE,
    );
    $cms_client = new CMS_Client();
	$documents = $cms_client->delegate_display_get_dataset_documents($dataset_id);
	if($documents['total'] >0)
	{
		$i = 0;
		global $base_url;
		while($i < $documents['total'])
		{
			$short_description = substr($documents[$i]['description'],0,277);
			$form['docLabel']['document'][$i] = array(
				'#markup' =>'
					<a href='.$base_url.'/viewDocument?doc_id='.$documents[$i]['document_id'].'&ds_id='.$dataset_id.'>'.$documents[$i]['name'].'</a><br />'.$short_description.'...<br /><br />
				',
			);
			$i++;
		}
	}
    
    $form['done_view_document'] = array(
    	'#type' => 'submit', 
		'#value' => t('Back')
    );
    
    $form['add_new_document'] = array(
    	'#type' => 'submit', 
		'#value' => t('Add')
    );

    if($documents['total'] >= 1)
    {
	$form['delete_view_datasets'] = array(
	    '#type' => 'submit', 
	    '#value' => t('Delete Documents')
	);
    }
    
    return $form;
}

function view_documents_form_submit($form, &$form_state)
{
	if($form_state['clicked_button']['#id'] == 'edit-add-new-document')
	{
		$dataset_id = $form_state['values']['dataset_id'];
		$values = array(
			'query' =>array('ds_id' => $dataset_id)
		);
		drupal_goto('addDocument',$values);
	}
	if($form_state['clicked_button']['#id'] == 'edit-delete-view-datasets')
	{
	    $dataset_id = $form_state['values']['dataset_id'];
		$values = array(
			'query' =>array('ds_id' => $dataset_id)
		);
		//drupal_set_message('Drupal Delete Documents');
		drupal_goto('deleteDatasetDocuments',$values);
	}
	if($form_state['clicked_button']['#id'] == 'edit-done-view-document')//Done
	{
	    drupal_goto('node/dataset_list');
	}
	
}



?>