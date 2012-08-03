<?php

interface Datastore_Interface
{
    
    public function insert_dataset_record($dataset);
    
    public function insert_collaborator_record($collaborator);
    
    public function insert_dataset_collaborator($dataset_id, $add_collaborator);
    
    //GET
    public function get_file_information_to_store($file_id);
    
    public function get_dataset_name($dataset_id);
    
    public function get_file_name($file_id);
	
    public function get_user_datasets($user_id);
	
    public function get_dataset_collaborators($dataset_id);
    
    public function get_collaborator_list($user_id,$collaborators_list);
    
    public function get_document_types();
    
    public function get_user_collaborators($user_id);
    
    public function get_dataset_information($dataset_id, $user_id);
    
    public function get_file_information($file_id);
    
    public function get_document_information($document_id);
    
    public function get_dataset_documents($dataset_id);
    //DELETE
    public function delete_document($dataset_id,$document_id);
    
    public function delete_document_file($file_id);
    
    public function delete_dataset_files($dataset_id,$file_id);
    
    public function delete_collaborator_from_user_dataset($dataset_id, $collaborator_id);
    
    public function delete_user_collaborator($user_id,$collaborator_id);
    
    public function delete_dataset_collaborator($dataset_id,$collaborator_id);
    
    public function delete_dataset($dataset_id,$dataset_name,$user_id);
    
    
    //UPDATE
    public function update_dataset_document_record($update_document, $update_type,$new_file_id);
    public function update_dataset_collaborators($update_members);
    public function update_dataset_information($dataset_id,$user_id,$update);
    public function update_collaborator($user_id,$collaborator,$index);
	
	//CHECK
	public function check_is_collaborator_belongs_in_dataset($dataset_id,$collaborator_id);
	public function check_dataset_name($dataset_name2,$user_id,$dataset_id);
    
}

?>