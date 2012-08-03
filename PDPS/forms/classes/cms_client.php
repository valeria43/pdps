<?php
include 'metadata_manager.php';
/*
*This class manages the request from the 
*user interface.
*/

include 'cms_interface.php';

class CMS_Client implements CMS_Interface
{
    //ADD
    public function delegate_add_dataset($dataset)
    {
       $metadata_instance = new Metadata_Manager();
       $message = $metadata_instance->add_dataset($dataset);
       return $message;
    }
    
    public function delegate_add_collaborator($collaborator)
    {
        $metadata_instance = new Metadata_Manager();
        $message_string = $metadata_instance->add_collaborator($collaborator); 
		return $message_string;
    }
    
    public function delegate_add_document($document)
    {
       $metadata_instance = new Metadata_Manager();
       $message_string = $metadata_instance->add_document($document);   
	   return $message_string;
    }
    
    public function delegate_add_dataset_collaborator($dataset_id, $add_collaborator)
    {
    	$metadata_instance = new Metadata_Manager();
    	$insert_response = $metadata_instance->add_dataset_collaborator($dataset_id, $add_collaborator);
    	return $insert_response;
    }
    
    //DELETE
    public function delegate_delete_temp_files()
    {
    	$file_manager_instance = new File_Manager();
    	$delete_temp_files = $file_manager_instance->delete_temporary_files();
    }
    
    public function delegate_delete_user_collaborators($user_id,$delete_user_collaborators,$total)
    {
    	$metadata_instance = new Metadata_Manager();
    	$message = $metadata_instance->delete_user_collaborators($user_id,$delete_user_collaborators,$total);
    	return $message;
    }
    
    public function delegate_remove_dataset($dataset_id,$dataset_name,$user_id)
    {
    	$metadata_instance = new Metadata_Manager();
    	$message_string = $metadata_instance->remove_dataset($dataset_id,$dataset_name,$user_id);
    	return $message_string;
    }
    
    public function delegate_dataset_documents($delete_dataset_documents)
    {
        $metadata_instance = new Metadata_Manager();
        $message_string = $metadata_instance->delete_dataset_documents($delete_dataset_documents);
		return $message_string;
    }
    
    public function delegate_remove_dataset_collaborator($dataset_id,$collaborator_id)
    {
    	$metadata_instance = new Metadata_Manager();
    	$message = $metadata_instance->remove_dataset_collaborator($dataset_id,$collaborator_id);
    	return $message;
    }
    
    //UPDATE
    public function delegate_update_dataset_document($update_document, $update_type)
    {
        $metadata_instance = new Metadata_Manager();
		$message_string = $metadata_instance->update_dataset_document($update_document, $update_type);
		return $message_string;
    }
    
    public function delegate_update_dataset_collaborators($update_collaborators)
    {
        $datastore_instance = new Datastore();
        $update_message = $datastore_instance->update_dataset_collaborators($update_collaborators);
		
		$message_instance = new Message_Catalog();
		$message_instance = $message_instance->message_update_dataset_collaborators($update_message);
		return $message_instance;
    }
    
    public function delegate_update_dataset_information($dataset_information)
    {
        $metadata_instance = new Metadata_Manager();
        $message_string = $metadata_instance->update_dataset_information($dataset_information);
		return $message_string;	
    }
    
    public function delegate_update_user_collaborators($user_id,$collaborator_list,$total)
    {
    	$metadata_instance = new Metadata_Manager();
    	$message = $metadata_instance->update_user_collaborators($user_id,$collaborator_list,$total);
    	return $message;
    }
    
    //CHECK 	
	
	public function delegate_check_collaborator_for_dataset($dataset_id,$collaborator_id)
	{
		$metadata_instance = new Metadata_Manager();
        $message_string = $metadata_instance->check_collaborator_in_dataset($dataset_id,$collaborator_id);//check_collaborator_in_dataset
		return $message_string;	
	}
	
	public function delegate_check_dataset_name($dataset_name, $user_id,$dataset_id)
	{
		$metadata_instance = new Metadata_Manager();
        $message_string = $metadata_instance->check_dataset_name($dataset_name, $user_id,$dataset_id);
		return $message_string;
	}
	
	//GET
	public function delegate_display_get_dataset_collaborators($dataset_id)
	{
		$display_manager_instance = new Display_Manager();
		$managers_list = $display_manager_instance->display_get_dataset_collaborators($dataset_id);
        return $managers_list;
                
	}
	
	public function delegate_get_collaborator_list($user_id)
	{
		$display_manger_instance = new Display_Manager();
		$collaborators = $display_manger_instance->display_get_collaborator_list($user_id);
		return $collaborators;	
	}
	
	public function delegate_get_document_types()
	{
		$display_manger_instance = new Display_Manager();
		$document_types = $display_manger_instance->display_get_document_types();
		return $document_types;
	}
	
	public function delegate_get_user_collaborators($user_id)
	{
		$display_manger_instance = new Display_Manager();
		$user_collaborators = $display_manger_instance->display_get_user_collaborators($user_id);
		return $user_collaborators;
	}
	
	public function delegate_display_get_dataset_information($dataset_id, $user_id)
	{
		$display_manager = new Display_Manager();
		$display_dataset_information = $display_manager->display_get_dataset_information($dataset_id, $user_id);
		return $display_dataset_information;
	}
	
	public function delegate_display_get_dataset_name($dataset_id)
	{
		$display_manager = new Display_Manager();
		$dataset_name = $display_manager->display_get_dataset_name($dataset_id);
		return $dataset_name;
	}
	
	public function delegate_display_get_document_information($document_id)
	{
		$display_manager = new Display_Manager();
		$document = $display_manager->display_get_document_information($document_id);
		return $document;
	}
	
	public function delegate_display_get_dataset_documents($dataset_id)
	{
		$display_instance = new Display_Manager();
		$dataset_documents = $display_instance->display_get_dataset_documents($dataset_id);
		return $dataset_documents; 
	}

}

?>