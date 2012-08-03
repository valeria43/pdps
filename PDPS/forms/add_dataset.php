<?php


function add_dataset_form($form, &$form_state)
{
 
 $form['#tree'] = TRUE;
  
 if(empty($form_state['num_files']))
 {
   $form_state['num_files'] = 1;
 }
    
 $a = $form_state['num_files'];
 $b = 0;
   
 $form['datasetBox'] = array(
   '#type'        => 'fieldset',
   '#title'       => t('Dataset Information'),
   '#collapsible' => TRUE, 
   '#collapsed'   => FALSE,
 );
 
 $form['datasetBox']['datasetName'] = array(
   '#type'        => 'textfield',
   '#title'       => t('Dataset Name'),
 );
 
 $form['datasetBox']['projectName'] = array(
   '#type'  => 'textfield',
   '#title' => t('Project Name'),
 );
	 
 $form['datasetBox']['datasetOwner'] = array(
    '#type' => 'textfield',
    '#title' => t('Owner Name'),
 );
  
 $form['datasetBox']['creationDate'] = array(
   '#type'  => 'date',
   '#title' => t('Creation Date'),
 );
	 
 $form['datasetBox']['versionNumber'] = array(
   '#type'  => 'textfield',
   '#title' => t('Version Number'),
 );
 
 $form['datasetBox']['datasetDescription'] = array(
   '#type'        => 'textarea',
   '#title'       => t('Dataset Description'),
   '#description' => t('Describe the contents of the data set'),
 );
 //Dataset Location
 $form['files_fieldset'] = array(
   '#type' 	  => 'fieldset',
   '#title' 	  => t('Dataset Location'),
   '#collapsible' => TRUE, 
   '#prefix' 	  => '<div id="files-fieldset-wrapper">',
   '#suffix' 	  => '</div>',
 );

 if(empty($form_state['number_of_files'])) 
 {
   $form_state['number_of_files'] = 1;
 }
 
 $form['files_fieldset']['dataset_type_location'] = array(
    '#type'    => 'radios',
    '#title'   => 'Select the Type of Location for the Dataset',
    '#options' => drupal_map_assoc(array(t('URL'), t('File'), t('File System Location'))),
    '#description' => t('Select where the data set is located'),
    '#default_value' => 'URL',
  );
 
 $form['files_fieldset']['dataset_url'] = array(
   '#type'   => 'textfield',
   '#title'  => 'Dataset URL Location',
   '#description' => t('Ex. www.myserver.edu/dataset/data'),
   '#states' => array(
       'visible' => array(
	 ':input[name="files_fieldset[dataset_type_location]"]' => array('value' => t('URL')),
       ),
     ),
 );
 
 $form['files_fieldset']['dataset_file_system'] = array(
   '#type' => 'textfield',
   '#title' => 'File Location',
   '#description' => t('Ex. C://Documents/Datasts/Data'),
   '#states' => array(
     'visible' => array(
	':input[name="files_fieldset[dataset_type_location]"]' => array('value' => t('File System Location')),
     ),
   ),
  
 );
 
 $form['total_number_of_files'] = array(
   '#type'  => 'hidden',
   '#value' => $form_state['number_of_files'],
 );
  
 for($i = 0; $i < $form_state['number_of_files']; $i++) 
 {
   $form['files_fieldset']['file'][$i] = array(
     '#type'            => 'managed_file',
     '#upload_location' => 'public://temp_files',
     '#upload_validators' => array(
	'file_validate_extensions' =>array('gif png jpg txt jpeg rtf docx xml doc zip rar pdf pptx ppt rar sql ppt bin avi c java cpp cpp csv dat cmd tiff data xlsx xls'),
     ),
     '#states' => array(
       'visible' => array(
		':input[name="files_fieldset[dataset_type_location]"]' => array('value' => t('File')),
       ),
     ),
   );
 }
  
 $form['files_fieldset']['add_more_files'] = array(
   '#type' => 'submit',
   '#value' => t('Add Another File'),
   '#submit' => array('add_one_more_file'),
   '#ajax' => array(
     'callback' => 'add_more_files_callback',
     'wrapper' => 'files-fieldset-wrapper',
   ),
   '#states' => array(
       'visible' => array(
	 ':input[name="files_fieldset[dataset_type_location]"]' => array('value' => t('File')),
       ),
     ),
 );
  
 if($form_state['number_of_files'] > 1)
 {
   $form['files_fieldset']['remove_file'] = array(
     '#type' => 'submit',
     '#value' => t('Remove One File'),
     '#submit' => array('remove_one_more_file'),
     '#ajax' => array(
       'callback' => 'add_more_files_callback',
       'wrapper' => 'files-fieldset-wrapper',
     ),
     '#states' => array(
       'visible' => array(
	 ':input[name="files_fieldset[dataset_type_location]"]' => array('value' => t('File')),
       ),
     ),
   );
 }
 
 $form['datasetPublicationAndArchieveBox'] = array(
   '#type'        => 'fieldset',
   '#title'       => t('Dataset Publication & Archieve Information'),
   '#collapsible' => TRUE,
 );
 
 $form['datasetPublicationAndArchieveBox']['publicationLocation'] = array(
   '#type'  => 'textfield',
   '#title' => t('Publication Location'),
   '#prefix' => '<table><tr><td>',
   '#suffix' => '</td>',
 );
 
 $form['datasetPublicationAndArchieveBox']['publicationDate'] = array(
   '#type'  => 'date',
   '#title' => t('Publication Date'),
   '#prefix' => '<td>',
   '#suffix' => '</td></tr>',
 );
 
 $form['datasetPublicationAndArchieveBox']['archieveLocation'] = array(
   '#type'  => 'textfield',
   '#title' => t('Archieve Location'),
   '#prefix' => '<tr><td>',
   '#suffix' => '</td>',
 );
 
 $form['datasetPublicationAndArchieveBox']['archieveDate'] = array(
   '#type'  => 'date',
   '#title' => t('Archieve Date'),
   '#prefix' => '<td>',
   '#suffix' => '</td></tr></table>',
 ); 
  
 
 //PATENT INFORMATION
 
  $form['patent_information'] = array(
    '#type' => 'fieldset',
    '#title' => t('Patent Information'),
    '#collapsible' => TRUE, 
  );
  
  $form['patent_information']['is_pantented_protected'] = array(
    '#type' => 'radios',
    '#default_value' => 'No',
    '#options' => drupal_map_assoc(array(t('No'), t('Yes'))),
    '#title' => t('Is the Dataset Patented Protected')
  );
  
  $patent_number_default = !empty($form_state['values']['patent_information']['patent_number']) ? $form_state['values']['patent_information']['patent_number'] : NULL;
  $form['patent_information']['patent_number'] = array(
    '#type' => 'textfield',
    '#title' => t('Patent Number'),
    '#default_value' => $patent_number_default,
    '#states' => array(
      'visible' => array(
        ':input[name="patent_information[is_pantented_protected]"]' => array('value' => t('Yes')),
      ),
    ),
  );
	
  $form['names_fieldset'] = array(
   '#type' => 'fieldset',
   '#title' => t('Collaborators'),
   '#collapsible' => TRUE, 
   '#prefix' => '<div id="names-fieldset-wrapper">',
   '#suffix' => '</div>',
  );
   
 if (empty($form_state['num_names'])) 
 {
   $form_state['num_names'] = 1;
 }
    
   
 $form['names_fieldset']['table'] = array(
   '#markup' => '
     <table>
       <tr>
         <td>Name</td>
         <td>Role</td>
         <td>Description</td>
       </tr>
   ',
 );
 
 $number_of_collaborators = $form_state['num_names'];
 $i = $form_state['num_names'];
 $j = 0;
	 
 $form['total_number_of_coll'] = array(
   '#type' => 'hidden',
   '#value' => $number_of_collaborators,
 );
 
 while($j < $i)
 {
    global $user;
    $cms_instance = new CMS_Client();
    $collaborator_list =  $cms_instance->delegate_get_collaborator_list($user->uid);
    $collaborator_default = !empty($form_state['values']['names_fieldset'][$j]['collaborator']) ? $form_state['values']['names_fieldset'][$j]['collaborator']: NULL;
    $form['names_fieldset'][$j]['collaborator'] = array(
      '#type' => 'select',
      '#options' => $collaborator_list,
      '#prefix' => '<tr><td>',
      '#suffix' => '</td>',
   );
    
   $form['names_fieldset'][$j]['roles'] = array(
    '#type' => 'select',
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
   
   $form['names_fieldset'][$j]['collaborator_description'] = array(
     '#type' => 'textarea',
     '#prefix' => '<td>',
     '#suffix' => '</td></tr>',
   );
			 
   $j++;
 }
 
 $form['total_number_of_coll'] = array(
   '#type' => 'hidden',
   '#value' => $number_of_collaborators,
 );
 
 if ($form_state['num_names'] > 1) 
 {
   $form['names_fieldset']['add_name'] = array(
     '#type' => 'submit',
     '#value' => t('Add one more'),
     '#submit' => array('collaborator_table_add_one'),
     '#ajax' => array(
       'callback' => 'collaborator_table_callback',
       'wrapper' => 'names-fieldset-wrapper',
     ),
     '#prefix' => '<tr><td >',
     '#suffix' => '</td>',
   );
   
   $form['names_fieldset']['remove_name'] = array(
     '#type' => 'submit',
     '#value' => t('Remove one'),
     '#submit' => array('collaborator_table_remove_one'),
     '#ajax' => array(
       'callback' => 'collaborator_table_callback',
       'wrapper' => 'names-fieldset-wrapper',
     ),
     '#prefix' => '<td colspan=2>',
     '#suffix' => '</td></tr>',
   );
 }
 else
 {
   $form['names_fieldset']['add_name'] = array(
     '#type' => 'submit',
     '#value' => t('Add one more'),
     '#submit' => array('collaborator_table_add_one'),
     '#ajax' => array(
       'callback' => 'collaborator_table_callback',
       'wrapper' => 'names-fieldset-wrapper',
      ),
      '#prefix' => '<tr><td colspan=3>',
      '#suffix' => '</td></tr>',
   ); 
 }
 
 $form['names_fieldset']['end_table'] = array(
   '#markup' => '
     </table>
   ',
 );
 
 $form['additionalInformaiton'] = array(
   '#type' => 'fieldset',
   '#title' => t('Additional Informaiton'),
   '#collapsible' => TRUE, 
 );
 
 $form['additionalInformaiton']['reference'] = array(
    '#type' => 'textarea',
    '#title' => 'Reference',
    '#rows' => 2,
 );
 
 $form['additionalInformaiton']['keywords'] = array(
    '#type' => 'textarea',
    '#title' => 'Keywords',
    '#rows' => 2,
 );
 
 $form['add_dataset'] = array(
   '#type' => 'submit',
   '#value' => t('Add Dataset'),
 );
 
 $form['cancel_add_dataset'] = array(
   '#type' => 'submit',
   '#value' => t('Cancel'),
 );
 
   return $form;
}

function add_dataset_form_validate($form,&$form_state)
{
    $is_add_button_clicked = $form_state['clicked_button']['#id'] == 'edit-add-dataset';
	global $user;
	$user_id = $user->uid;
    if($is_add_button_clicked == 1)
    {
	 $dataset_name     = $form_state['values']['datasetBox']['datasetName'];
	 $dataset_location = $form_state['values']['files_fieldset']['dataset_type_location'];
	 $is_dataset_under_a_patent = $form_state['values']['patent_information']['is_pantented_protected'];
	 $dataset_description = empty($form_state['values']['datasetBox']['datasetDescription']);
	$cms_client_instance = new CMS_Client();
	$is_dataset_name_valid = $cms_client_instance->delegate_check_dataset_name($dataset_name, $user_id);
	 
	 if(empty($dataset_name))
	 {
		 form_set_error('datasetName','Enter name for the dataset');
	 }
	  if($is_dataset_name_valid == FALSE)
		 {
			form_set_error('datasetName','A dataset with that name already exists');
		 }
	 if($dataset_description == TRUE)
	 {
	   form_set_error('datasetDescription','Enter a description for the dataset');
	 }
	 
	 if($dataset_location == 'URL')
	 {
	    $dataset_location_url = $form_state['values']['files_fieldset']['dataset_url'];
	    if($dataset_location_url == NULL)
	    {
	       form_set_error('dataset_url', 'Need to enter a URL');
	    }
	 }
	 if($dataset_location == 'File')
	 {
	    $total_number_of_files =  $form_state['values']['total_number_of_files'];
	    $file_id = empty($form_state['values']['files_fieldset']['file'][0]);
	     if($file_id ==  TRUE && $total_number_of_files == 1)
	     {
		 form_set_error('file][0','Need to Upload the Data set Files');
	     }
	 }
	 if($dataset_location == 'File System Location')
	 {
	     $dataset_location_file_name = $form_state['values']['files_fieldset']['dataset_file_system'];
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

function collaborator_table_callback($form, $form_state) 
{
  return $form['names_fieldset'];
}
 
function collaborator_table_add_one($form, &$form_state) 
{
   $form_state['num_names']++;
   $form_state['rebuild'] = TRUE;
}
 
function collaborator_table_remove_one($form, &$form_state) 
{
   if($form_state['num_names'] > 1)
   {
     $form_state['num_names']--;
   }
   $form_state['rebuild'] = TRUE;
}
 
function add_more_files_callback($form, $form_state) 
{
  return $form['files_fieldset'];
}

function add_one_more_file($form, &$form_state)
{
  $form_state['number_of_files']++;
  $form_state['rebuild'] = TRUE;
}

function remove_one_more_file($form, &$form_state)
{
  if ($form_state['number_of_files'] > 1)
  {
    $form_state['number_of_files']--;
  }
  
  $form_state['rebuild'] = TRUE;
}

function add_dataset_form_submit($form, &$form_state)
{

  if($form_state['clicked_button']['#id'] == 'edit-add-dataset')//Done
  {
   
     global $user;
     
     $dataset = array();
     /*
      * Retrieve the dataset information of the dataset
      */  
     
     $dataset['user'] 		= $user->uid;
     $dataset['name'] 		= str_replace(" ", "_", $form_state['values']['datasetBox']['datasetName']);
     $dataset['project_name']   = $form_state['values']['datasetBox']['projectName'];
     $dataset['owner'] 		= $form_state['values']['datasetBox']['datasetOwner'];
     $dataset['creation_date']  = $form_state['values']['datasetBox']['creationDate'];
     $dataset['version_number'] = $form_state['values']['datasetBox']['versionNumber'];
     $dataset['description'] 	= $form_state['values']['datasetBox']['datasetDescription'];
     $dataset['reference'] 	= $form_state['values']['additionalInformaiton']['reference'];
     $dataset['keywords'] 	= $form_state['values']['additionalInformaiton']['keywords'];
     
     /*
      * Retrieve the location of the dataset.
      * The location of the dataset can be one of the following
      *  - URL
      *  - File(s)
      */
     
     $dataset_type_location = $form_state['values']['files_fieldset']['dataset_type_location'];
     
     if($dataset_type_location == 'URL')
     {
	
	$dataset['location_type'] = $dataset_type_location;
	$dataset['url']           =  $form_state['values']['files_fieldset']['dataset_url'];
     }
     if($dataset_type_location == 'File System Location')
     {
	$dataset['location_type']      = $dataset_type_location;
	$dataset['file_location_path'] =  $form_state['values']['files_fieldset']['dataset_file_system'];
     }
     elseif($dataset_type_location == 'File')
     {
       /*
	* Move the dataset files added to add a dataset
	*  - Move the dataset file(s) to the folder created
	*    for the dataset
	*/
       $dataset['location_type']   = $dataset_type_location;
       $dataset['number_of_files'] = $form_state['values']['total_number_of_files'];
       $total_number_of_files =  $form_state['values']['total_number_of_files'];
       $j = 0;
	
       while($j < $total_number_of_files)
      {
	 $file_id = $form_state['values']['files_fieldset']['file'][$j];
	 $dataset['file_id'][$j] = $file_id;
	 $j++;
      }
     }
     
     /*
      * Retrieve the information of the Dataset Publication
      *
      */
     
     $dataset['publication_location'] = $form_state['values']['datasetPublicationAndArchieveBox']['publicationLocation'];
     $dataset['publication_date']     = $form_state['values']['datasetPublicationAndArchieveBox']['publicationDate'];
     
     /*
      * Retrieve the Archive Information of the Dataset
      */
     
     $dataset['archieve_location'] = $form_state['values']['datasetPublicationAndArchieveBox']['archieveLocation'];
     $dataset['archieve_date'] 	   = $form_state['values']['datasetPublicationAndArchieveBox']['archieveDate'];
     
     $is_patented_protected = $form_state['values']['patent_information']['is_pantented_protected'];
     
     if($is_patented_protected == 'Yes')
     {
	$dataset['is_patented_protected'] = $is_patented_protected;
	$dataset['patent_number'] 	  = $form_state['values']['patent_information']['patent_number'];
     }
     if($is_patented_protected == 'No')
     {
	$dataset['is_patented_protected'] = $is_patented_protected;
     }
     $number = $form_state['values']['total_number_of_coll'];
     $dataset['number_of_collaborators'] = $form_state['values']['total_number_of_coll'];
     $i = 0;
	 
     while($i < $number)
     {
	$dataset[$i]['collaborator_name'] 	 = $form_state['values']['names_fieldset'][$i]['collaborator'];
	$dataset[$i]['collaborator_role'] 	 = $form_state['values']['names_fieldset'][$i]['roles'];
	$dataset[$i]['collaborator_description'] = $form_state['values']['names_fieldset'][$i]['collaborator_description'];
	$i++;
     }	 
   
     $cms_instance = new CMS_Client;
     $message = $cms_instance->delegate_add_dataset($dataset);
     drupal_set_message($message['status_message']);
     $cms_instance->delegate_delete_temp_files();
	 if($message['flag'] == 1)
	 {
		$values = array('query' =>array('ds_id' => $message['dataset_id']));
		drupal_goto('viewDatasetInformation',$values);
	 }
	 if($message['flag'] == 0)
	 {
		$form_state['redirect'] = 'node/addDataset';
	 }
  }
  if($form_state['clicked_button']['#id'] == 'edit-cancel-add-dataset')//Done
  {
	$cms_instance = new CMS_Client;
	$cms_instance->delegate_delete_temp_files();
	$form_state['redirect'] = 'node/dataset_list';
  }
 
}

?>