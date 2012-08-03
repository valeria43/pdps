<?php

class Display_Manager
{
	public function get_user_datasets($user_id)
	{
		global $base_url;
		
		$datastore_instance = new Datastore();
		$user_datasets = $datastore_instance->get_user_datasets($user_id);
		$pdps_base_url = $base_url.'/viewDatasetInformation';
		$i = 0;
		$links = '<table>';
		
		while($i < $user_datasets['total_number_of_datasets'])
		{
			$links .= '<tr>
					<td><a href='.$pdps_base_url.'?ds_id='.$user_datasets[$i]['dataset_id'].'>'.$user_datasets[$i]['dataset_name'].'</td>
					<td><a href='.$base_url.'/viewCollaborators?ds_id='.$user_datasets[$i]['dataset_id'].'>Collaborators</td>
					<td><a href='.$base_url.'/viewDocuments?ds_id='.$user_datasets[$i]['dataset_id'].'>Documents</a></td>
				  </tr>';
			$i++;
		}
		
		$links .= '</table>';
		
		return $links;
	}
	
	public function display_get_dataset_collaborators($dataset_id)
	{
		$datastore_instance = new Datastore();
		$members_list = $datastore_instance->get_dataset_collaborators($dataset_id);
		
		return $members_list;
	}
	
	public function display_get_collaborator_list($user_id)
	{
	    $datastore_instance = new Datastore();
	    $collaborators_list[0] = 'Select Collaborator';
	    $collaborators_list = $datastore_instance->get_collaborator_list($user_id,$collaborators_list);
	       
	    return $collaborators_list;
	}
	
	
	public function display_get_document_types()
	{
		$datastore_instance = new Datastore();
		$document_types = $datastore_instance->get_document_types();
		return $document_types;
	}
	
	public function display_get_user_collaborators($user_id)
	{
		$datastore_instance = new Datastore();
		$user_collaborators = $datastore_instance->get_user_collaborators($user_id);
		return $user_collaborators;
	}
	
	public function display_get_dataset_information($dataset_id, $user_id)
	{
	
		$datastore_instance = new Datastore();
	    //$result  = db_query("SELECT * FROM pdps_dataset WHERE dataset_id = {$dataset_id} AND user_id = {$user_id}");
	    $result = $datastore_instance->get_dataset_information($dataset_id, $user_id);
	    
	   $dataset_information = array();
	   $creation_date    = array();
	   $publication_date = array();
	   $archieve_date    = array();
		
	    foreach($result as $record)
	    {
	        $dataset_information['name']                   = $record->name;
			$dataset_information['project_name'] 	       = $record->project_name;
	        $dataset_information['owner_name']             = $record->owner_name;
	        $creation_date['year']                         = $record->creation_date_year;
	        $creation_date['month']                        = $record->creation_date_month;
	        $creation_date['day']                          = $record->creation_date_day;
	        $dataset_information['creation_date']          = $creation_date;
	        $dataset_information['version_number']         = $record->version_number;
	        $dataset_information['description']            = $record->description;
	        $dataset_information['dataset_location_name']  = $record->dataset_location_name;
	        $dataset_information['publication_location']   = $record->publication_location;
	        $publication_date['year']                      = $record->publication_date_year;
	        $publication_date['month']                     = $record->publication_date_month;
	        $publication_date['day']                       = $record->publication_date_day;
	        $dataset_information['publication_date']       = $publication_date;
	        $archieve_date['year']                         = $record->archieve_date_year;
	        $archieve_date['month']                        = $record->archieve_date_month;
	        $archieve_date['day']                          = $record->archieve_date_day;
	        $dataset_information['archieve_location']      = $record->archieve_location;
	        $dataset_information['archieve_date']          = $archieve_date;
	        $dataset_information['reference']              = $record->reference;
	        $dataset_information['keywords']               = $record->keywords;
	        
	        if($record->is_patented_protected == 0)
	        {
	            $dataset_information['is_patented_protected']  = 'No';
	        }
	        if($record->is_patented_protected == 1)
	        {
	            $dataset_information['is_patented_protected']  = 'Yes';
	            $dataset_information['patent_number']          = $record->patent_number;
	        }
	        
	        if($record->dataset_location_type == 1 || $record->dataset_location_type == 3)
	        {
	            $dataset_information['dataset_location_type']  = 'URL';
	            $dataset_information['dataset_location_name']  = $record->dataset_location_name;
	        }
	        if($record->dataset_location_type == 2)
	        {
	        	global $base_url;
	            $dataset_information['dataset_location_type']  = 'File';
	            $file_manager = new File_Manager();
	            $files = $file_manager->get_dataset_files($dataset_id);
	            $i = 0;
	            $link = $base_url.'/sites/default/files/DATASETS';
	            while($i < $files['total'])
	            {
	                $dataset_information['files'][$i] = $files[$i]['filename'];
	                $dataset_information['files_id'][$i] = $files[$i]['file_id'];
	                $i++;
	            }
	            $dataset_information['total_number_of_files'] = $i;
	        }
	        if($record->dataset_location_type == 3)
	        {
	            $dataset_information['dataset_location_type'] = 'File System Location';
	            $dataset_information['dataset_location_name']  = $record->dataset_location_name;
	        }
	        
	    }
	    return $dataset_information;
	}
	
	public function display_get_dataset_name($dataset_id)
	{
		$datastore_instance = new Datastore();
	    $dataset_name = $datastore_instance->get_dataset_name($dataset_id);
	    return $dataset_name;
	}
	
	public function display_get_document_information($document_id)
	{
		$datastore_instance = new Datastore();
		$document_information = $datastore_instance->get_document_information($document_id);
		return $document_information;
	}
	
	public function display_get_dataset_documents($dataset_id)
	{
		$datastore_instance = new Datastore();
		$dataset_documents = $datastore_instance->get_dataset_documents($dataset_id);
		return $dataset_documents;
	}
	
}
?>