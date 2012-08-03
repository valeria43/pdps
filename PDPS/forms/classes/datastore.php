<?php

include 'database_interface.php';

class Datastore implements Datastore_Interface
{
	//GET
    public function get_file_information_to_store($file_id)
    {
	$result  = db_query('SELECT filename,uri FROM file_managed WHERE fid = '.$file_id);
	
	$file_information = array();
	
	foreach($result as $record)
	{
	    $file_information['filename'] = $record->filename;
	    $file_information['uri'] 	  = $record->uri;
	}
	return $file_information;
    }
    
        
    function get_file_name($file_id)
    {
	$query = 'SELECT filename FROM pdps_files WHERE file_id='.$file_id.'';
	$result = db_query($query);
	foreach($result as $record)
	{
	    $filename = $record->filename;
	}
	
	return $filename;
    }
	
	function get_user_datasets($user_id)
	{
		$result = db_query("SELECT dataset_id,name FROM pdps_dataset WHERE user_id = ".$user_id."");
		$user_datasets = array();
		$i=0;
		foreach($result as $record)
		{
			$user_datasets[$i]['dataset_name'] = $record->name;
			$user_datasets[$i]['dataset_id'] = $record->dataset_id;
			$i++;
		}
		
		$user_datasets['total_number_of_datasets'] = $i;
		
		return $user_datasets;
	}
	
	function get_dataset_collaborators($dataset_id)
	{
	    $datastore_instance = new Datastore();
	    $result  = db_query("SELECT collaborator_id,role,collaborator_dataset_contribution FROM pdps_dataset_collaborators WHERE dataset_id = {$dataset_id}");
	    $dataset_member = array();
	    $i = 0;
	    foreach($result as $record)
	    {
			$collaborator_name 		    	= $datastore_instance->get_collaborator_name($record->collaborator_id);
			$dataset_member[$i]['name'] 	= $collaborator_name;
			$dataset_member[$i]['id'] 	    	= $record->collaborator_id;
			$dataset_member[$i]['role_type']    = $record->role;
			$dataset_member[$i]['contribution'] = $record->collaborator_dataset_contribution;
			
			$i++;
	    }
	    $dataset_member['total'] = $i;
	    
	    return $dataset_member;
	}

	public function get_collaborator_name($collaborator_id)
	{
		$result  = db_query("SELECT collaborator_name FROM pdps_collaborator WHERE collaborator_id =$collaborator_id");
		
		foreach($result as $record)
		{
			$collaborator_name = $record->collaborator_name;
		}
		
		return $collaborator_name;
	}
	
	public function get_collaborator_list($user_id,$collaborators_list)
	{
	    $query = "SELECT collaborator_id,collaborator_name FROM pdps_collaborator WHERE user_id = ".$user_id."";
	    $result = db_query($query);

	    foreach($result as $record)
	    {
	        $collaborators_list[$record->collaborator_id] = $record->collaborator_name;
	    }    
	    return $collaborators_list;
	}

	function get_role($role_id)
	{
		if($role_id == 'r1')
		{
			$role_name = 'PI';
		}
		if($role_id == 'r2')
		{
			$role_name = 'Co-PI';
		}
		if($role_id == 'r3')
		{
			$role_name = 'Investigator';
		}
		if($role_id == 'r4')
		{
			$role_name = 'Staff';
		}
		
		return $role_name;

	}
	
	function get_document_types()
	{
	    $query = "SELECT * FROM pdps_document_type";
	    $result = db_query($query);
	    $document_types = array();
	    
	    foreach($result as $record)
	    {
	        $document_types[$record->document_type_id] = $record->document_type_name;
	    }    
	    return $document_types;
	}
    
    public function get_user_collaborators($user_id)
    {
        $query = "SELECT collaborator_id, collaborator_name,collaborator_email,collaborator_affiliation FROM pdps_collaborator WHERE user_id={$user_id}";
        $result = db_query($query);
        $i = 0;
        
        foreach($result as $record)
        {
	    	$collaborator[$i]['id'] = $record->collaborator_id;
	    	$collaborator[$i]['name'] = $record->collaborator_name;
	    	$collaborator[$i]['email'] = $record->collaborator_email;
	    	$collaborator[$i]['affiliation'] = $record->collaborator_affiliation;
	    	$i++;
        }
        
        $collaborator['total'] = $i;
        
        return $collaborator;  
    }
    
    public function get_dataset_information($dataset_id, $user_id)
    {
    	$result  = db_query("SELECT * FROM pdps_dataset WHERE dataset_id = {$dataset_id} AND user_id = {$user_id}");
    	return $result;
    }
    
    public function get_dataset_name($dataset_id)
    {
        $result = db_query('SELECT name FROM pdps_dataset WHERE dataset_id='.$dataset_id.'');
        
        foreach($result as $record)
        {
    		$dataset_name = $record->name;
        }
        return $dataset_name;
    }
    
    public function get_document_information($document_id)
    {
        	$result  = db_query("SELECT file_id,document_name,document_type_id,document_application,document_description,new_other_dataset_type FROM pdps_dataset_documents WHERE document_id = {$document_id} ");
            $datastore_instance = new Datastore();
            $document = array();
            
            foreach($result as $record)
            {
        	$document['name'] = $record->document_name;
        	$document['type'] = $record->document_type_id;
        	$document['application'] = $record->document_application;
        	$document['description'] = $record->document_description;
        	$file =  $datastore_instance->get_file_information($record->file_id);
        	$document['file_id'] = $record->file_id;
        	$document['file_location'] = $file['location'];
        	$document['filename'] = $file['filename'];
        	$document['new_type'] = $record->new_other_dataset_type;
            }
            
            return $document;
    }
    
    public function get_dataset_documents($dataset_id)
    {
        $query = db_query("SELECT document_id,document_name,document_description,file_id FROM pdps_dataset_documents WHERE dataset_id={$dataset_id}");
        $i = 0;
        $documents = array();
        
        foreach($query as $record)
        {
    	$documents[$i]['document_id'] = $record->document_id;
    	$documents[$i]['name'] 	      = $record->document_name;
    	$documents[$i]['description'] = $record->document_description;
    	$documents[$i]['file_id']     = $record->file_id;
    	$i++;
        }
        $documents['total'] = $i;
        return $documents;
    }
    
    public function get_file_information($file_id)
    {
        $result = db_query("SELECT file_location,filename FROM pdps_files WHERE file_id={$file_id} AND file_type=1");
        //$file = new array();
        foreach($result as $record)
        {
	    	$file['location'] = $record->file_location;
	    	$file['filename'] = $record->filename;
        }
        
        return $file;
    }
    
    public function insert_dataset_record($dataset)
    {
	$dataset_record = array();
	$dataset_record['user_id']		  = $dataset['user'];
	$dataset_record['name']			  = $dataset['name'];
	$dataset_record['project_name' ] 	  = $dataset['project_name'];
	$dataset_record['owner_name'] 		  = $dataset['owner'];
	$dataset_record['creation_date_month'] 	  = $dataset['creation_date']['month'];
	$dataset_record['creation_date_day'] 	  = $dataset['creation_date']['day'];
	$dataset_record['creation_date_year'] 	  = $dataset['creation_date']['year'];
	$dataset_record['version_number'] 	  = $dataset['version_number'];
	$dataset_record['description'] 		  = $dataset['description'];
	$dataset_record['publication_location']   = $dataset['publication_location'];
	$dataset_record['publication_date_month'] = $dataset['publication_date']['month'];
	$dataset_record['publication_date_day']   = $dataset['publication_date']['day'];
	$dataset_record['publication_date_year']  = $dataset['publication_date']['year'];
	$dataset_record['archieve_location'] 	  = $dataset['archieve_location'];
	$dataset_record['archieve_date_month']    = $dataset['archieve_date']['month'];
	$dataset_record['archieve_date_day']      = $dataset['archieve_date']['day'];
	$dataset_record['archieve_date_year']     = $dataset['archieve_date']['year'];
	$dataset_record['reference']    	  = $dataset['reference'];
	$dataset_record['keywords']    	  	  = $dataset['keywords'];

	
	/*
	 * There a 3 locations where the user could store the dataset,
	 * the following locaitons type are the following:
	 * 	- URL => 1
	 * 	- Dataset => 2
	 *	- File Location => 3
	 */
	if($dataset['location_type'] == 'URL')
	{
	    $dataset_record['dataset_location_type'] = 1;
	    $dataset_record['dataset_location_name'] = $dataset['url'];
	}
	if($dataset['location_type'] == 'File')
	{
	    $dataset_record['dataset_location_type'] = 2;
	}
	if($dataset['location_type'] == 'File System Location')
	{
	    $dataset_record['dataset_location_type'] = 3;
	    $dataset_record['dataset_location_name'] = $dataset['file_location_path'];
	}
	
	if($dataset['is_patented_protected'] == 'Yes')
	{
	    $dataset_record['is_patented_protected'] = 1;
	    $dataset_record['patent_number']         = $dataset['patent_number'];
	}
	if($dataset['is_patented_protected'] == 'No')
	{
	    $dataset_record['is_patented_protected'] = 0;
	}
	
	$dataset_id = db_insert('pdps_dataset')->fields($dataset_record)->execute();
	return $dataset_id;
    }
    
    public function insert_dataset_collaborator_record($dataset_id, $collaborator_id, $role, $collaborator_dataset_contribution)
    {
	db_insert('pdps_dataset_collaborators')->fields(
	    array(
	        'dataset_id'         		    => $dataset_id,
		'collaborator_id'       	    => $collaborator_id,
		'role' 				    => $role,
		'collaborator_dataset_contribution' => $collaborator_dataset_contribution,
	    )
	)->execute();
    }
    
    public function insert_file_record($type_of_file, $dataset_id,$file_location,$filename)
    {
	$file_id = db_insert('pdps_files')->fields(
	    array(
		'file_type'     => $type_of_file,
		'file_type_id' 	=> $dataset_id, 
		'file_location' => $file_location,
		'filename' 	=> $filename,
	    )
	)->execute();
	
	return $file_id;
    }
    
   /* public function get_dataset_name($dataset_id)
    {
		$query = db_query('SELECT name FROM pdps_dataset WHERE dataset_id = '.$dataset_id.'');
	
	foreach($query as $record)
	{
	    $dataset_name = $record->name;
	}
	
	return $dataset_name;
    }*/
    
    public function insert_collaborator_record($collaborator)
    {
	$collaborator_id = db_insert('pdps_collaborator')->fields(
	    array(
			'user_id' 		   => $collaborator['user'],
	        'collaborator_name'        => $collaborator['name'],
			'collaborator_email'       => $collaborator['email'],
			'collaborator_affiliation' => $collaborator['affiliation'],	
	    )
	)->execute();
	
	return $collaborator_id;
    }
    
    public function insert_document_for_dataset($document)
    {
		$insert['dataset_id'] 	       	= $document['dataset_id'];
		$insert['file_id']             	= $document['new_file_id'];
		$insert['document_name']        = $document['name'];
		$insert['document_type_id']     = $document['type'];
		$insert['document_application'] = $document['application'];
		$insert['document_description'] = $document['description'];
		
		if($document['type'] == 0)
		{
			$insert['new_other_dataset_type'] = $document['new_type'];
		}
		
		$document_id = db_insert('pdps_dataset_documents')->fields($insert)->execute();
		return $document_id;
	
    }
    
    public function insert_dataset_collaborator($dataset_id, $add_collaborator)
    {
    	$result = db_insert('pdps_dataset_collaborators')->fields(
    			array(
    			'dataset_id'			    => $dataset_id,
    			'collaborator_id'		    => $add_collaborator['collaborator'],
    			'role' 				    => $add_collaborator['role'],
    			'collaborator_dataset_contribution' => $add_collaborator['contribution'],
    			)
    		)->execute();
    	return $result;
    }
    
    public function delete_document($dataset_id,$document_id)
    {
		$and = db_and()->condition('dataset_id',$dataset_id)->condition('document_id',$document_id);
		$delete_document = db_delete('pdps_dataset_documents')->condition($and)->execute();
		return $delete_document;
    }
    
    public function delete_document_file($file_id)
    {
		$delete_document_file = db_delete('pdps_files')->condition('file_id',$file_id)->execute();
		return $delete_document_file;
    }
    
    function delete_dataset_files($dataset_id,$file_id)
    {
	$and = db_and()->condition('file_type_id',$dataset_id)->condition('file_id',$file_id);
	
	$msg = db_delete('pdps_files')->condition($and)->execute();
    }
    
    function delete_collaborator_from_user_dataset($dataset_id, $collaborator_id)
    {
        $and = db_and()->condition('dataset_id',$dataset_id)->condition('collaborator_id',$collaborator_id);
    	
        $msg = db_delete('pdps_dataset_collaborators')->condition($and)->execute();
    }
    
    public function delete_user_collaborator($user_id,$collaborator_id)
    {
        $and = db_and()->condition('user_id',$user_id)->condition('collaborator_id',$collaborator_id);
    	
        $msg = db_delete('pdps_collaborator')->condition($and)->execute();
    	return $msg;
    }
    
    public function delete_dataset_collaborator($dataset_id,$collaborator_id)
    {
        $and = db_and()->condition('dataset_id',$dataset_id)->condition('collaborator_id',$collaborator_id);
        $delete_response = db_delete('pdps_dataset_collaborators')->condition($and)->execute();
        return $delete_response;
    }
    
    public function delete_dataset($dataset_id,$dataset_name,$user_id)
    {
        
        $and = db_and()->condition('dataset_id',$dataset_id)->condition('user_id',$user_id);
        
        $delete_dataset               = db_delete('pdps_dataset')->condition($and)->execute();
        $delete_dataset_collaborators = db_delete('pdps_dataset_collaborators')->condition('dataset_id',$dataset_id)->execute();
        $delete_dataset_documents     = db_delete('pdps_dataset_documents')->condition('dataset_id',$dataset_id)->execute();
        $delete_dataset_file          = db_delete('pdps_files')->condition('file_type_id',$dataset_id)->execute();

    	return $delete_dataset;
        
        //delete_dataset_folder($dataset_name);
        
    }
    
    
    //UPDATE
    public function update_dataset_document_record($update_document, $update_type,$new_file_id)
    {
		$update['document_name']        = $update_document['name'];
		$update['document_type_id']     = $update_document['document_type'];
		$update['document_application'] = $update_document['application'];
		$update['document_description'] = $update_document['description'];
		
		if($update_document['document_type'] == 0)
		{
			$update['new_other_dataset_type'] = $update_document['new_type'];
		}
		
		if($update_type == 0)
		{
			
			$and = db_and()->condition('document_id',$update_document['document_id'])->condition('dataset_id',$update_document['dataset_id']);
				
			$msg = db_update('pdps_dataset_documents')->fields($update)->condition($and)->execute();
		}
		if($update_type == 1)
		{
			$update['file_id'] = $new_file_id;
			$and = db_and()->condition('document_id',$update_document['document_id'])->condition('dataset_id',$update_document['dataset_id']);
			$msg = db_update('pdps_dataset_documents')->fields($update)->condition($and)->execute();
		}
		
		return $msg;
    }
    
    function update_dataset_collaborators($update_members)
    {
		$i = 0;
		$one = 0;
		$zero = 0;
		while($i < $update_members['total'])
		{
			$and = db_and()->condition('dataset_id',$update_members['dataset_id'])->condition('collaborator_id',$update_members[$i]['member_id']);
			$update = db_update('pdps_dataset_collaborators')->fields(
				array(
					'role' 				=> $update_members[$i]['role'],
					'collaborator_dataset_contribution' => $update_members[$i]['contribution'],
				)
			)->condition($and)->execute();
			
			if($update == 0)
			{
				$zero++;
			}
			if($update == 1)
			{
				$one++;
			}
			$i++;
		}
		
		$update_status['0'] = $zero;
		$update_status['1'] = $one;
		$update_status['total'] = $update_members['total'];
		
		return $update_status;
    }
    
    public function update_dataset_information($dataset_id,$user_id,$update)
    {
		$and = db_and()->condition('dataset_id',$dataset_id)->condition('user_id',$user_id);
	
		$msg = db_update('pdps_dataset')->fields($update)->condition($and)->execute();
		
		return $msg;
    }
    
    public function update_collaborator($user_id,$collaborator,$index)
    {
    	$and = db_and()->condition('user_id',$user_id)->condition('collaborator_id',$collaborator[$index]['id']);
    	$update = db_update('pdps_collaborator')->fields(
    	    array(
    		    'collaborator_name'        => $collaborator[$index]['name'],
    		    'collaborator_email'       => $collaborator[$index]['email'],
    		    'collaborator_affiliation' => $collaborator[$index]['affiliation'],
    	    )
        )->condition($and)->execute();
    	return $update;	
    }
	
	//CHECK	
	function check_is_collaborator_belongs_in_dataset($dataset_id,$collaborator_id)
	{
		$result = db_query("SELECT collaborator_id FROM pdps_dataset_collaborators WHERE dataset_id=$dataset_id AND collaborator_id=$collaborator_id");
		
		$response = FALSE;
		
		foreach($result as $record)
		{
			$response = $record->collaborator_id;
		}
		
		return $response;
	}
	
	public function check_dataset_name($dataset_name2,$user_id,$dataset_id)
	{
		$result =  db_query("SELECT name,dataset_id FROM pdps_dataset WHERE user_id ={$user_id} AND name='{$dataset_name2}'");
		
		$is_name_valid = TRUE;
		
		foreach($result as $record)
		{
			$response = $record->name;
			$dataset_id2 = $record->dataset_id;
			if($response == $dataset_name2 && $dataset_id != $dataset_id2)//Name is already taken
			{
				$is_name_valid = FALSE;
			}
		}
	
		return $is_name_valid;
	}
}

?>