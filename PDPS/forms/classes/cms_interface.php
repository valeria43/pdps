<?php

/*
 *This class interfaces with the user class interfaces.
 *
 */

interface CMS_Interface
{
    //ADD
    public function delegate_add_dataset($dataset);
    public function delegate_add_collaborator($collaborator);
    public function delegate_add_document($document);
    public function delegate_add_dataset_collaborator($dataset_id, $add_collaborator);
    //DELETE
    public function delegate_dataset_documents($delete_dataset_documents);
    public function delegate_delete_temp_files();
    public function delegate_delete_user_collaborators($user_id,$delete_user_collaborators,$total);
    public function delegate_remove_dataset_collaborator($dataset_id,$collaborator_id);
    public function delegate_remove_dataset($dataset_id,$dataset_name,$user_id);
    /*public function delete_dataset();
    public function delete_collaborator();
    public function delete_document();*/
    
    //RETRIEVE INFORMATION
    /*public function get_dataset();
    public function get_collaborator();
    public function get_document();*/
    
    //UPDATE
    public function delegate_update_dataset_document($update_document, $update_type);
    public function delegate_update_dataset_collaborators($update_collaborators);
    public function delegate_update_dataset_information($dataset_information);
    public function delegate_update_user_collaborators($user_id,$collaborator_list,$total);
	
    //GET
    public function delegate_display_get_dataset_collaborators($dataset_id);
    public function delegate_get_collaborator_list($user_id);
    public function delegate_get_document_types();
    public function delegate_get_user_collaborators($user_id);
    public function delegate_display_get_dataset_information($dataset_id, $user_id);
    public function delegate_display_get_dataset_name($dataset_id);
    public function delegate_display_get_document_information($document_id);
    public function delegate_display_get_dataset_documents($dataset_id);
    
	
	//CHECK
	public function delegate_check_collaborator_for_dataset($dataset_id,$collaborator);
	public function delegate_check_dataset_name($dataset_name, $user_id,$dataset_id);
    
}

?>