<?php

include 'file_manager.php';

class Metadata_Manager 
{
    public function add_dataset($dataset)
    {
	

	$folder_name = $dataset['name'];
	$type = 'dataset';

//CREATE THE FOLDER FOR THE DATASET
	$file_manager = new File_Manager();
	$datset_folder_is_created = $file_manager->create_dataset_folder($folder_name);
	
	if($datset_folder_is_created == TRUE)
	{
		//ADD THE METADATA OF THE DATASET
		$datastore = new Datastore();
		$dataset_id = $datastore->insert_dataset_record($dataset);
		//IF THE USER SELECTED TO ADD THE DATASET FILES IN THE SYSTEM
		//MOVE THE FILES TO THE FOLDER
		if($dataset['location_type'] == 'File')
		{
			$number_of_files = $dataset['number_of_files'];
			$file_manager->add_file($type, $number_of_files, $dataset,$dataset_id);
		}
		
		//ADD THE COLLABORATORS FOR THE DATASET
		if($dataset['number_of_collaborators'] == 1)
		{
			if($dataset[0]['collaborator_name'] != 0)
			{
			$metadata_mgr = new Metadata_Manager();
			$metadata_mgr->add_collaborator_to_datataset($dataset, $dataset_id, 0);
			}
		}
		if($dataset['number_of_collaborators'] > 1)
		{
			$total_number_of_collaborators = $dataset['number_of_collaborators'];
			$i = 0;
			
			$metadata_mgr = new Metadata_Manager();

			while($i < $total_number_of_collaborators)
			{
			$metadata_mgr->add_collaborator_to_datataset($dataset,$dataset_id,$i);
			$i++;
			}
		}
		
		$message_catalog_instance = new Message_Catalog();
		$message['dataset_id'] = $dataset_id;
		$message['status_message'] =  $message_catalog_instance->message_insert_database_record($dataset_id,$dataset['name']);
		$message['flag'] = 1;
	}
	else
	{
		$message['status_message'] = 'Dataset With that Name Already Exists';
		$message['flag'] = 0;
	}
	
	
	return $message;
	
    }
    
    public function add_collaborator_to_datataset($collaborator,$dataset_id,$collaborator_index)
    {
        $collaborator_id 	   	   = $collaborator[$collaborator_index]['collaborator_name'];
        $collaborator_role     	           = $collaborator[$collaborator_index]['collaborator_role'];
        $collaborator_dataset_contribution = $collaborator[$collaborator_index]['collaborator_description'];
	
	$datastore = new Datastore();
	$datastore->insert_dataset_collaborator_record($dataset_id, $collaborator_id, $collaborator_role, $collaborator_dataset_contribution);

    }
    
    public function add_document($document)
    {
		$dataset_id = $document['dataset_id'];
        $name 	    = $document['name'];
        $file_id    = $document['file_id'];
        $type 	    = $document['type'];
        $app        = $document['application'];
        $des        = $document['description'];
        
        $type = 'document';
	
		$file_mgr = new File_Manager();
	
        $file_id = $file_mgr->add_file($type, 1, $document,$dataset_id);
		$document['new_file_id'] = $file_id;
		$datastore = new Datastore();
		$insert_message = $datastore->insert_document_for_dataset($document);
		$message_instance = new Message_Catalog();
		$message_string = $message_instance->message_insert_document($document['name'], $insert_message);
		return $message_string;
    }
    
    public function add_collaborator($collaborator)
    {
		$datastore_instance = new Datastore();
		$insert_message = $datastore_instance->insert_collaborator_record($collaborator);
		$message_instance = new Message_Catalog();
		$message_string = $message_instance->message_insert_collaborator($collaborator['name'], $insert_message);
		return $message_string;
    }
    
    public function add_dataset_collaborator($dataset_id, $add_collaborator)
    {
    	$datastore_instance = new Datastore();
    	$insert_response = $datastore_instance->insert_dataset_collaborator($dataset_id, $add_collaborator);
    	
    	$message_catalog_instance = new Message_Catalog();
    	$message_string = $message_catalog_instance->meessage_add_dataset_collaborator($insert_response);
    	return $message_string;
    }
    
    //DELETE
    function delete_dataset_documents($delete_dataset_documents)
    {
		$datastore = new Datastore();
		$file_manager = new File_Manager();
		
		$i = 0;
		
		while($i < $delete_dataset_documents['total_documents_to_delete'])
		{
			$delete_doc 	 = $datastore->delete_document($delete_dataset_documents['dataset_id'],$delete_dataset_documents['document_id'][$i]);
			$filename 		 = $datastore->get_file_name($delete_dataset_documents['file_id'][$i]);
			$delete_doc_file = $datastore->delete_document_file($delete_dataset_documents['file_id'][$i]);
			$dataset_name    = $datastore->get_dataset_name($delete_dataset_documents['dataset_id']);
			$delete_file     = $file_manager->delete_file($dataset_name,$filename);
			$delete_document[$i]['doc']      = $delete_doc;
			$delete_document[$i]['doc_file'] = $delete_doc_file;
			$delete_document[$i]['file']     = $delete_file;
			$i++;
		}
		
		
		$message_instance = new Message_Catalog();
		$message_string =  $message_instance->message_delete_dataset_documents($delete_document,$i);
		return $message_string;
		
    }
    
    public function delete_user_collaborators($user_id,$delete_user_collaborators,$total)
    {
        $i=0;
        $j=0;
        $datastore_instance = new Datastore();
        $user_datasets = $datastore_instance->get_user_datasets($user_id);
        $total_datasets = $user_datasets['total_number_of_datasets'];
    	$one = 0;
    		
        while($i < $total)
        {
    		while($j < $total_datasets)
    		{
    			$datastore_instance->delete_collaborator_from_user_dataset($user_datasets[$j]['dataset_id'], $delete_user_collaborators[$i]);
    			
    			$j++;
    		}
    		$delete_message = $datastore_instance->delete_user_collaborator($user_id,$delete_user_collaborators[$i]);
    		if($delete_message == 1)
    		{
    			$one++;
    		}
    		$i++;
        }
    	
    	$message_catalog_instance = new Message_Catalog();
    	$message = $message_catalog_instance->message_delete_user_collaborators($one);
    	return $message;
    	
    }
    
    public function remove_dataset($dataset_id,$dataset_name,$user_id)
    {
    	$datastore_instance = new Datastore();
    	$delete_dataset = $datastore_instance->delete_dataset($dataset_id,$dataset_name,$user_id);
    
    	$message_catalog = new Message_Catalog();
    	$message = $message_catalog->message_remove_dataset($delete_dataset);
    	return $message;
        
        //delete_dataset_folder($dataset_name);
        
    }
    
    public function remove_dataset_collaborator($dataset_id,$collaborator_id)
    {
    	$datastore_instance = new Datastore();
    	$delete_message = $datastore_instance->delete_dataset_collaborator($dataset_id,$collaborator_id);
    	
    	$message_catalog_instance = new Message_Catalog();
    	$message = $message_catalog_instance->message_remove_dataset_collaborator($delete_message);
    	return $message;
    }
    
    
    //UPDATE
    public function update_dataset_document($update_document, $update_type)
    {
	$datastore = new Datastore();
	
	if($update_type == 0)
	{
	    $update_message = $datastore->update_dataset_document_record($update_document, 0,'');
	    
	}
	if($update_type == 1)
	{
	    $file_manager   = new File_Manager();
	    $new_file_id    = $file_manager->add_file('document', 1, $update_document,$update_document['dataset_id']);
	    $update_message = $datastore->update_dataset_document_record($update_document,1,$new_file_id);
	    $dataset_name   = $datastore->get_dataset_name($update_document['dataset_id']);
	    $file_manager->delete_file($dataset_name,$update_document['file_name']);
	}
    
	$message_instance = new Message_Catalog();
	$message_string = $message_instance->message_update_dataset_document($update_document['name'],$update_message);
	return $message_string;
    }
    
    function update_dataset_information($update_dataset_information)
    {
	$file_manager_instance = new File_Manager();
	$datastore_instance = new Datastore();
	
	$update['name']                   = $update_dataset_information['name'];
	$update['project_name']           = $update_dataset_information['project_name'];
	$update['owner_name']             = $update_dataset_information['owner_name'];
	$update['creation_date_month']    = $update_dataset_information['creation_date_month'];
	$update['creation_date_day']      = $update_dataset_information['creation_date_day'];
	$update['creation_date_year']     = $update_dataset_information['creation_date_year'];
	$update['version_number']         = $update_dataset_information['version_number'];
	$update['description']            = $update_dataset_information['description'];
	$update['publication_location']   = $update_dataset_information['publication_location'];
	$update['publication_date_month'] = $update_dataset_information['publication_date_month'];
	$update['publication_date_day']   = $update_dataset_information['publication_date_day'];
	$update['publication_date_year']  = $update_dataset_information['publication_date_year'];
	$update['archieve_location']      = $update_dataset_information['archieve_location'];
	$update['archieve_date_month']    = $update_dataset_information['archieve_date_month'];
	$update['archieve_date_day']      = $update_dataset_information['archieve_date_day'];
	$update['archieve_date_year']     = $update_dataset_information['archieve_date_year'];
	$update['reference'] 			  = $update_dataset_information['reference'];
	$update['keywords'] 			  = $update_dataset_information['keywords'];
	
	if($update_dataset_information['rename_dataset'] == TRUE)
	{
        $file_manager_instance->rename_dataset_file($update_dataset_information['previous_dataset_name'], $update_dataset_information['new_dataset_name']);
	}
	
	if($update_dataset_information['is_patented_protected'] == 1)
	{
	    $update['is_patented_protected']  = $update_dataset_information['is_patented_protected'];
	    $update['patent_number']          = $update_dataset_information['patent_number'];
	}
	if($update_dataset_information['is_patented_protected'] == 0)
	{
	    $update['is_patented_protected']  = $update_dataset_information['is_patented_protected'];
	}
	
	if($update_dataset_information['location_type'] == 'URL')
	{
	    $update['dataset_location_type'] = 1;
	    $update['dataset_location_name'] = $update_dataset_information['url'];
	}
	if($update_dataset_information['location_type'] == 'File System Location')
	{
	    $update['dataset_location_type'] = 3;
	    $update['dataset_location_name'] = $update_dataset_information['file_system_location'];
	}
	
	if($update_dataset_information['location_type'] == 'File')
	{
	    $update['dataset_location_type'] = 2;
	    if($update_dataset_information['delete_files'] == 'yes')
	    {
		$i = 0;
		while($i < $update_dataset_information['total_number_of_files_to_delete'])
		{
		    $delete_file_id = $update_dataset_information['delete_file_id'][$i];
		    $delete_file_name = $update_dataset_information['delete_file_name'][$i];
		    $datastore_instance->delete_dataset_files($update_dataset_information['dataset_id'],$delete_file_id);
		    $file_manager_instance->delete_dataset_file( $update['name'], $delete_file_name);
		    $i++;
		}
	    }
	   if($update_dataset_information['add_new_files']  == 'yes')
	    {
	    
		$total = $update_dataset_information['total_files_to_add'];
		$file_update_message = $file_manager_instance->add_file('dataset', $total, $update_dataset_information,$update_dataset_information['dataset_id']);
	    }
	    else
	    {
	    	$file_update_message = 1;	
	    }
	}
	
	global $user;
	$user_id = $user->uid;
	
	$update_message = $datastore_instance->update_dataset_information($update_dataset_information['dataset_id'],$user_id,$update);
	$message_instance = new Message_Catalog();
	$message_string = $message_instance->message_update_dataset_information($update_dataset_information['new_dataset_name'],$update_message,$file_update_message);
	return $message_string;
	}
	
	public function update_user_collaborators($user_id,$collaborator_list,$total)
	{
		$datastore_instance = new Datastore();
	    $i=0;
	    $update_count['0'] = 0;
		$update_count['1'] = 0;
		$zero = 0;
		$one = 0;
	    while($i < $total)
	    {
			$update_message = $datastore_instance->update_collaborator($user_id,$collaborator_list,$i);
			if($update_message == 0)
			{
				$zero++;
			}
			if($update_message == 1)
			{
				$one++;
			}
			$i++;
	    }
		
		$update_count['0'] = $zero;
		$update_count['1'] = $one;
		
		$message_catalog_instance = new Message_Catalog();
		$message = $message_catalog_instance->message_update_user_collaborators($update_count,$total);
		
		return $message;
	}
	
	//CHECK
	public function check_collaborator_in_dataset($dataset_id,$collaborator_id)
	{
		$datstore_instance = new Datastore();
		$msg = $datstore_instance->check_is_collaborator_belongs_in_dataset($dataset_id,$collaborator_id);//check_is_collaborator_belongs_in_dataset($dataset_id,$collaborator_id)
		return $msg;
	}
	
	public function check_dataset_name($dataset_name, $user_id,$dataset_id)
	{
		$datstore_instance = new Datastore();
		$dataset_name2= str_replace(" ", "_", $dataset_name);
		$msg = $datstore_instance->check_dataset_name($dataset_name2,$user_id,$dataset_id);
		return $msg;
	}
    
}
?>