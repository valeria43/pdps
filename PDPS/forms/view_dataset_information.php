<?php

function is_dataset_id_valid($var)
{
	$msg = is_numeric($var);
	
	if($msg == TRUE)
	{
		return $msg;
	}
	if($msg == FALSE)
	{
		drupal_goto('node/dataset_list');
	}
}


function view_dataset_information_form($form, &$form_state)
{
    if(isset($_GET['ds_id']))
    {
        $_SESSION['ds_id'] = $_GET['ds_id'];
        $msg = is_dataset_id_valid($_SESSION['ds_id']);
      
    }
    
    $dataset_id = $_SESSION['ds_id'];
    //$_SESSION['ds_id'] = NULL;
    $form['#tree'] = TRUE;
    
    global $user;
    
   $cms_client = new CMS_Client();
   $dataset_info = $cms_client->delegate_display_get_dataset_information($dataset_id, $user->uid);
    
    include 'navigation_bar.php';
    
    $form['dataset_id'] = array(
        '#type' => 'hidden',
        '#value' => $dataset_id,
    );
    
    $form['datasetBox'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Dataset Information'),
        '#collapsible' => FALSE, 
        '#collapsed'   => FALSE,
    );
    
    $form['dataset_name'] = array(
        '#type' => 'hidden',
        '#value' => $dataset_info['name'],
    );
    
    $form['datasetBox']['datasetName'] = array(
        '#type' => 'textfield',
        '#title' => t('Dataset Name'),
        '#default_value' => $dataset_info['name'],
    );
    
    $form['datasetBox']['projectName'] = array(
        '#type' => 'textfield',
        '#title' => t('Project Name'),
        '#default_value' => $dataset_info['project_name'],
    );
    
    
    $form['datasetBox']['datasetOwner'] = array(
        '#type' => 'textfield',
        '#title' => t('Owner Name'),
        '#default_value' => $dataset_info['owner_name']
    );
    
    $form['datasetBox']['creationDate'] = array(
        '#type'   => 'date',
        '#title' => t('Creation Date'),
        '#default_value' => $dataset_info['creation_date'],
    );
    
    $form['datasetBox']['versionNumber'] = array(
        '#type' => 'textfield',
        '#title' => t('Version Number'),
        '#default_value' => $dataset_info['version_number'],
    );
    
    $form['datasetBox']['datasetDescription'] = array(
        '#type' => 'textarea',
        '#title' => t('Dataset Description'),
        '#default_value' => $dataset_info['description'],
        '#description' => t('Describe the contents of the data set'),
    );
    
    $form['datasetLocationBox'] = array(
            '#type'        => 'fieldset',
            '#title'       => t('Dataset Location'),
            '#collapsible' => FALSE, 
            '#collapsed'   => FALSE,
            '#prefix' 	  => '<div id="files-fieldset-wrapper">',
            '#suffix' 	  => '</div>',
    );
    
    
    $form['datasetLocationBox']['dataset_type_location'] = array(
            '#type'    => 'radios',
            '#title'   => 'Select the Type of Location for the Dataset',
            '#options' => drupal_map_assoc(array(t('URL'), t('File'), t('File System Location'))),
            '#default_value' => $dataset_info['dataset_location_type'],
            '#description' => t('Select where the data set is located'),
    );
    
    $default_value_url = '';
    $default_value_file_system_location = '';
    
    if($dataset_info['dataset_location_type'] == 'URL')
    {
        $default_value_url = $dataset_info['dataset_location_name'];
    }
    if($dataset_info['dataset_location_type'] == 'File System Location')
    {
        $default_value_file_system_location = $dataset_info['dataset_location_name'];
    }
    
    $form['datasetLocationBox']['url'] = array(
        '#type' => 'textfield',
        '#title' => 'URL',
        '#default_value'  => $default_value_url,
        '#description' => t('Ex. www.myserver.edu/dataset/data'),
        '#states' => array(
            'visible' => array(
                ':input[name="datasetLocationBox[dataset_type_location]"]' => array('value' => t('URL')),
            ),
        ),
    );
    
    $form['datasetLocationBox']['file_system'] = array(
        '#type' => 'textfield',
        '#title' => 'File Location Name',
        '#description' => t('Ex. C://Documents/Datasts/Data'),
        '#default_value'   => $default_value_file_system_location,
        '#states' => array(
            'visible' => array(
                ':input[name="datasetLocationBox[dataset_type_location]"]' => array('value' => t('File System Location')),
            ),
        ),
    );
    
	if(empty($dataset_info['total_number_of_files']))
	{
		$form['delete_number_of_files'] = array(
            '#type' => 'hidden',
            '#default_value' => 0,
        );
	}
    
    if($dataset_info['dataset_location_type'] == 'File' && $dataset_info['total_number_of_files'] > 0)
    {
        $form['delete_number_of_files'] = array(
            '#type' => 'hidden',
            '#default_value' => $dataset_info['total_number_of_files'],
        );
        
        $i = 0;
        global $base_url;
        $link = $base_url.'/sites/default/files/DATASETS';
        while($i < $dataset_info['total_number_of_files'] )
        {
            $file_link = $link.'/'.$dataset_info['name'].'/'.$dataset_info['files'][$i].'';
            $form['datasetLocationBox']['file'][$i] = array(
                '#type' => 'checkbox',
                '#title'   => "<b></b><a href='{$file_link}'>{$dataset_info['files'][$i]}</a><br />",
                '#description' => 'Delete',
                '#states' => array(
                    'visible' => array(
                        ':input[name="datasetLocationBox[dataset_type_location]"]' => array('value' => t('File')),
                    ),
                ),
            );
            
            $form['datasetLocationBox']['file_id'][$i] = array(
                '#type' => 'hidden',
                '#default_value' => $dataset_info['files_id'][$i],
            );
            
            $form['datasetLocationBox']['filename'][$i] = array(
                '#type' => 'hidden',
                '#default_value' => $dataset_info['files'][$i],
            );
            
            $i++;
        }
    }
    
    if(empty($form_state['number_of_files'])) 
    {
        $form_state['number_of_files'] = 1;
    }
    
    $form['total_number_of_files'] = array(
        '#type'  => 'hidden',
        '#value' => $form_state['number_of_files'],
    );
    
    for($j = 0; $j < $form_state['number_of_files']; $j++) 
    {
        $form['datasetLocationBox']['new_file'][$j] = array(
            '#type'            => 'managed_file',
            '#upload_location' => 'public://temp_files',
            '#upload_validators' => array(
                'file_validate_extensions' => array('gif png jpg txt jpeg rtf docx xml doc zip rar pdf pptx ppt rar sql ppt bin avi c java cpp cpp csv dat cmd tiff data xlsx xls'),
            ),
            '#states' => array(
              'visible' => array(
                       ':input[name="datasetLocationBox[dataset_type_location]"]' => array('value' => t('File')),
              ),
            ),
        );
    }
    
    $form['datasetLocationBox']['add_more_files'] = array(
        '#type' => 'submit',
        '#value' => t('Add File'),
        '#submit' => array('add_one_more_file'),
        '#ajax' => array(
          'callback' => 'view_add_more_files_callback',
          'wrapper' => 'files-fieldset-wrapper',
        ),
        '#states' => array(
            'visible' => array(
              ':input[name="datasetLocationBox[dataset_type_location]"]' => array('value' => t('File')),
            ),
        ),
    );
    
    if($form_state['number_of_files'] > 1)
    {
      $form['datasetLocationBox']['remove_file'] = array(
        '#type' => 'submit',
        '#value' => t('Remove File'),
        '#submit' => array('remove_one_more_file'),
        '#ajax' => array(
          'callback' => 'view_add_more_files_callback',
          'wrapper' => 'files-fieldset-wrapper',
        ),
        '#states' => array(
          'visible' => array(
            ':input[name="datasetLocationBox[dataset_type_location]"]' => array('value' => t('File')),
          ),
        ),
      );
    }
    
    $form['datasetPublicationBox'] = array(
            '#type'        => 'fieldset',
            '#title'       => t('Publication'),
            '#collapsible' => FALSE, 
            '#collapsed'   => FALSE,
    );
    
    $form['datasetPublicationBox']['publicationLocation'] = array(
        '#type' => 'textfield',
        '#title' => t('Pulbication Location'),
        '#default_value' => $dataset_info['publication_location'],
        '#prefix' => '<table><tr><td>',
        '#suffix' => '</td>'
    );
    
    $form['datasetPublicationBox']['publicationDate'] = array(
        '#type' => 'date',
        '#title' => t('Pulbication Date'),
        '#default_value' => $dataset_info['publication_date'],
        '#prefix' => '<td>',
        '#suffix' => '</td></tr>',
    );
    
    $form['datasetPublicationBox']['archieveLocation'] = array(
        '#type' => 'textfield',
        '#title' => t('Archieve Location'),
        '#default_value' => $dataset_info['archieve_location'],
        '#prefix' => '<tr><td>',
        '#suffix' => '</td>'
    );
    
    $form['datasetPublicationBox']['archieveDate'] = array(
        '#type' => 'date',
        '#title' => t('Archieve Date'),
        '#default_value' => $dataset_info['archieve_date'],
        '#prefix' => '<td>',
        '#suffix' => '</td></tr></table>',
    );
    
    $form['patent_information'] = array(
    '#type' => 'fieldset',
    '#title' => t('Patent Information'),
    '#collapsible' => TRUE, 
  );
  
  $form['patent_information']['is_pantented_protected'] = array(
    '#type' => 'radios',
    '#default_value' => $dataset_info['is_patented_protected'],
    '#options' => drupal_map_assoc(array(t('No'), t('Yes'))),
    '#title' => t('Is the Dataset Patented Protected')
  );
  
    if($dataset_info['is_patented_protected'] == 'Yes')
    {
        $form['patent_information']['patent_number'] = array(
            '#type' => 'textfield',
            '#title' => t('Patent Number'),
            '#default_value' => $dataset_info['patent_number'],
            '#states' => array(
                'visible' => array(
                    ':input[name="patent_information[is_pantented_protected]"]' => array('value' => t('Yes')),
                ),
            ),
        );
    }
    else
    {
        $form['patent_information']['patent_number'] = array(
            '#type' => 'textfield',
            '#title' => t('Patent Number'),
            '#states' => array(
                'visible' => array(
                    ':input[name="patent_information[is_pantented_protected]"]' => array('value' => t('Yes')),
                ),
            ),
        );
    }
    
    $form['additionalInformaiton'] = array(
        '#type' => 'fieldset',
        '#title' => t('Additional Informaiton'),
        '#collapsible' => TRUE, 
    );
 
    $form['additionalInformaiton']['reference'] = array(
       '#type' => 'textarea',
       '#title' => 'Reference',
       '#default_value' => $dataset_info['reference'], 
       '#rows' => 2,
    );
    
    $form['additionalInformaiton']['keywords'] = array(
       '#type' => 'textarea',
       '#title' => 'Keywords',
       '#default_value' => $dataset_info['keywords'], 
       '#rows' => 2,
    );
       
    $form['done_dataset'] = array(
    	'#type' => 'submit', '#value' => t('Back')
    );
    
    $form['save_dataset'] = array(
    	'#type' => 'submit', '#value' => t('Save')
    );

    $form['delete_edit_dataset'] = array(
    	'#type' => 'submit', 
		'#value' => t('Delete'),
		'#attributes' => array('onclick' => 
										'if(!confirm("Do You Want to Delete the Dataset?"))
										{
												return false;
										}
										'
                                            ),
    ); 

    return $form;  
}

function view_dataset_information_form_validate($form,&$form_state)
{
    $is_save_button_clicked = $form_state['clicked_button']['#id'] == 'edit-save-dataset';
	global $user;
	$user_id = $user->uid;
    if($is_save_button_clicked == 1)
    {
    	$dataset_id 	  = $form_state['values']['dataset_id']; 
        $dataset_name     = $form_state['values']['datasetBox']['datasetName'];//['datasetBox']['datasetName']
        $dataset_location = $form_state['values']['datasetLocationBox']['dataset_type_location'];
        $is_dataset_under_a_patent = $form_state['values']['patent_information']['is_pantented_protected'];
        $dataset_description = $form_state['values']['datasetBox']['datasetDescription'];
		$cms_client_instance = new CMS_Client();
		$is_dataset_name_valid = $cms_client_instance->delegate_check_dataset_name($dataset_name, $user_id,$dataset_id);
		if($is_dataset_name_valid == FALSE)
		{
			form_set_error('datasetName','A dataset with that name already exists');
		}
        if(empty($dataset_name))
         {
                 //drupal_set_message('A dataset exist with that name');
                 form_set_error('datasetName','Dataset Needs a name');
         }
         if(empty($dataset_description))
         {
            form_set_error('datasetDescription','Enter a description for the dataset');
         }
         
         if($dataset_location == 'URL')
         {
            $dataset_location_url = $form_state['values']['datasetLocationBox']['url'];
            if($dataset_location_url == NULL)
            {
               form_set_error('dataset_url', 'Need to enter a URL');
            }
         }
         if($dataset_location == 'File')
         {
            $total_number_of_files =  $form_state['values']['datasetLocationBox']['new_file'][0];
            $file_id = empty($form_state['values']['files_fieldset']['file'][0]);
            $total_files = $form_state['values']['delete_number_of_files'];
            $index = 0;
            $count = 0;
            while($index < $total_files)
            {
                $delete_file = $form_state['values']['datasetLocationBox']['file'][$index];
                if($delete_file == 1)
                {
                    $count++;
                }
                $index++;
            }
            if($count == $total_files)
            {
                if($file_id == TRUE && $total_number_of_files == 0)
                {
                    form_set_error('new_file][0','Need to Upload the Data set Files ');
                }
            }
         }
         if($dataset_location == 'File System Location')
         {
             $dataset_location_file_name = $form_state['values']['datasetLocationBox']['file_system'];
             if($dataset_location_file_name == NULL)
             {
                form_set_error('dataset_file_system','Need to enter a file name');
             }
         }
         if($is_dataset_under_a_patent == 'Yes')
         {
            $patent_number = $form_state['values']['patent_information']['patent_number'];
            if($patent_number == NULL)
            {
               form_set_error('patent_number','Need to enter a patent number');
            }
         }
    }
}

function view_add_more_files_callback($form, $form_state) 
{
  return $form['datasetLocationBox'];
}

function view_add_one_more_file($form, &$form_state)
{
  $form_state['number_of_files']++;
  $form_state['rebuild'] = TRUE;
}

function view_remove_one_more_file($form, &$form_state)
{
  if ($form_state['number_of_files'] > 1)
  {
    $form_state['number_of_files']--;
  }
  
  $form_state['rebuild'] = TRUE;
}

function view_dataset_information_form_submit($form, &$form_state)
{
    if($form_state['clicked_button']['#id'] == 'edit-done-dataset')//Done
    {
        $cms_instance = new CMS_Client;
        $cms_instance->delegate_delete_temp_files();
        $form_state['redirect'] = 'node/dataset_list';
    }
    if($form_state['clicked_button']['#id'] == 'edit-save-dataset')//Done
    {
        $previous_dataset_name = $form_state['values']['dataset_name'];
        $update_dataset_information['dataset_id'] = $form_state['values']['dataset_id'];
        $update_dataset_information['name'] =   str_replace(" ", "_", $form_state['values']['datasetBox']['datasetName']);
        $update_dataset_information['project_name'] = $form_state['values']['datasetBox']['projectName'];
        $update_dataset_information['owner_name'] = $form_state['values']['datasetBox']['datasetOwner'];
        $creation_date = $form_state['values']['datasetBox']['creationDate'];
        $update_dataset_information['creation_date_month'] = $creation_date['month'];
        $update_dataset_information['creation_date_day'] = $creation_date['day'];
        $update_dataset_information['creation_date_year'] = $creation_date['year'];
        $update_dataset_information['version_number']   = $form_state['values']['datasetBox']['versionNumber'];
        $update_dataset_information['description'] = $form_state['values']['datasetBox']['datasetDescription'];
        $update_dataset_information['publication_location'] = $form_state['values']['datasetPublicationBox']['publicationLocation'];
        $publication_date = $form_state['values']['datasetPublicationBox']['publicationDate'];
        $update_dataset_information['publication_date_month'] = $publication_date['month'];
        $update_dataset_information['publication_date_day'] = $publication_date['day'];
        $update_dataset_information['publication_date_year'] = $publication_date['year'];
        $update_dataset_information['archieve_location'] = $form_state['values']['datasetPublicationBox']['archieveLocation'];
        $archieve_date = $form_state['values']['datasetPublicationBox']['archieveDate'];
        $update_dataset_information['archieve_date_month'] = $archieve_date['month'];
        $update_dataset_information['archieve_date_day'] = $archieve_date['day'];
        $update_dataset_information['archieve_date_year'] = $archieve_date['year'];
		$update_dataset_information['reference'] 		= $form_state['values']['additionalInformaiton']['reference'];
		$update_dataset_information['keywords']  		= $form_state['values']['additionalInformaiton']['keywords'];
        $is_patented_protected = $form_state['values']['patent_information']['is_pantented_protected'];
        
        if($previous_dataset_name != $update_dataset_information['dataset_id'])
        {
            $update_dataset_information['rename_dataset'] = TRUE;
            $update_dataset_information['previous_dataset_name'] = $previous_dataset_name;
            $update_dataset_information['new_dataset_name'] = $update_dataset_information['name'];
        }
        
        if($is_patented_protected == 'No')
        {
            $update_dataset_information['is_patented_protected'] = 0;
        }
        if($is_patented_protected == 'Yes')
        {
            $update_dataset_information['is_patented_protected'] = 1;
            $update_dataset_information['patent_number'] = $form_state['values']['patent_information']['patent_number'];
        }
        
        $dataset_location_type = $form_state['values']['datasetLocationBox']['dataset_type_location'];
        if($dataset_location_type == 'File')
        {
            
			$update_dataset_information['location_type'] = 'File';
			$total_number_of_files = $form_state['values']['total_number_of_files'];
			
			//Add New Files
			$file_id = $form_state['values']['datasetLocationBox']['new_file'][0];

			$i = 0;
			$update_dataset_information['location_type']   = $dataset_location_type;
			$update_dataset_information['number_of_files'] = $form_state['values']['total_number_of_files'];

			while($i < $total_number_of_files)
			{
				$update_dataset_information['file_id'][$i] = $form_state['values']['datasetLocationBox']['new_file'][$i];
				$i++;
			}
			$update_dataset_information['total_files_to_add'] = $i;
			
			if($i >= 1 && $form_state['values']['datasetLocationBox']['new_file'][0] != 0)
			{
				$update_dataset_information['add_new_files'] = 'yes';
			}
			else
			{
				$update_dataset_information['add_new_files'] = 'no';
			}

			//Delete Files
			$j = 0;
			//$number_of_files = $form_state['values']['number_of_files'];
			$number_of_files = $form_state['values']['delete_number_of_files'];
			$number_of_files_to_delete = 0;
			
			while($j < $number_of_files)
			{
				$delete_file = $form_state['values']['datasetLocationBox']['file'][$j];
				if($delete_file == 1)
				{
					$delete_file_id = $form_state['values']['datasetLocationBox']['file_id'][$j];
					$update_dataset_information['delete_file_id'][$number_of_files_to_delete]= $delete_file_id;
					$delete_file_name = $form_state['values']['datasetLocationBox']['filename'][$j];
					$update_dataset_information['delete_file_name'][$number_of_files_to_delete]= $delete_file_name;
					$number_of_files_to_delete++;   
				}
				$j++;
            }
            if($number_of_files_to_delete >= 1)
            {
                $update_dataset_information['delete_files'] = 'yes';
                $update_dataset_information['total_number_of_files_to_delete'] = $number_of_files_to_delete;
            }
            else
            {
                $update_dataset_information['delete_files'] = 'no';
            }
        }
        if($dataset_location_type == 'URL')
        {
            $update_dataset_information['location_type'] = 'URL';
            $update_dataset_information['url'] = $form_state['values']['datasetLocationBox']['url'];
        }
        if($dataset_location_type == 'File System Location')
        {
            $update_dataset_information['location_type'] = 'File System Location';
            $update_dataset_information['file_system_location'] = $form_state['values']['datasetLocationBox']['file_system'];
        }
        
        $cms_client_instance = new CMS_Client();
        
        $message_string = $cms_client_instance->delegate_update_dataset_information($update_dataset_information);
        
		drupal_set_message($message_string);
		
        $values = array('query' =>array('ds_id' => $update_dataset_information['dataset_id']));
        
        $cms_client_instance->delegate_delete_temp_files();
        drupal_goto('viewDatasetInformation',$values);
    }
    if($form_state['clicked_button']['#id'] == 'edit-delete-edit-dataset')
    {
        global $user;
        $user_id 		= $user->uid;
        $dataset_id 	= $form_state['values']['dataset_id'];
        $dataset_name 	= $form_state['values']['datasetBox']['datasetName'];
        $cms_instance 	= new CMS_Client;
        $message_string = $cms_instance->delegate_remove_dataset($dataset_id,$dataset_name,$user_id);
		drupal_set_message($message_string);
		$cms_instance->delegate_delete_temp_files();
        $form_state['redirect'] = 'node/dataset_list';
    }
    
}

?>