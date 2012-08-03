<?php
include 'datastore.php';
/*
 * This class manages all related with files and folders.
 * The class will manage the following:
 * 	- Creation of Files
 * 	- Add/Delete files
 *
 */

class File_Manager 
{
    /*
     * $type is the type of file to store:
     * 	- Dataset
     * 	-Document
     * 	$number_of_files: the number of files to add
     *  $files the actual files to add
     *
     */
    public function add_file($type, $number_of_files, $files,$dataset_id)
    {
	//$file_manager = new File_Manager();
	if($type == 'document')
	{
	    $destination        = getcwd().'/sites/modules/PDPS/documents';
	    $datastore_instance = new Datastore();
	    $result             = $datastore_instance->get_file_information_to_store($files['file_id']);
	    $dataset_name       = $datastore_instance->get_dataset_name($dataset_id);

	    $filename 	     	     = $result['filename'];
            $uri                     = $result['uri'];
	    $destination2 	     = 'public://DATASETS/'.$dataset_name.'/Documents';
	    $destination3 	     = 'public://DATASETS/'.$dataset_name.'/Documents/'.$filename;
	    $datasotre 	             = new Datastore();
	    $file_id 		     = $datasotre->insert_file_record(1,$dataset_id,$destination2,$filename);
	    $source       	     = new stdClass();
	    $source->uri  	     = $uri;
	    $file_to_move_location   = getcwd().'/site/default/files/temp_files/'.$filename.'';
	    
	    $file_manager = new File_Manager();
	    $file_manager->move_file($uri,$destination3);
	    
	    return $file_id;

	}
	if($type == 'dataset')
	{
	    //$folder_name = $file['name'];
	    //create_dataset_folder($folder_name);
	    $total_number_of_files = $number_of_files;
	    $j = 0;
	
	    while($j < $total_number_of_files)
	    {
		
		$destination = getcwd().'/sites/modules/PDPS/documents';
		//$result      = db_query('SELECT filename,uri FROM file_managed WHERE fid = '.$files['file_id'][$j]);
		$datastore_instance = new Datastore();
		$result = $datastore_instance->get_file_information_to_store($files['file_id'][$j]);
		
		$dataset_name = $files['name'];

		$filename 	         = $result['filename'];
		$uri                     = $result['uri'];
		$destination2 	         = 'public://DATASETS/'.$dataset_name.'';
		$destination3 		 = 'public://DATASETS/'.$dataset_name.'/'.$filename;		
		$datasotre 		 = new Datastore();
		$file_id 		 = $datasotre->insert_file_record(2,$dataset_id,$destination2,$filename);
		$source       	     	 = new stdClass();
		$source->uri  	     	 = $uri;
		$file_to_move_location   = getcwd().'/site/default/files/temp_files'.$filename.'';
		$file_manager = new File_Manager();
		$file_manager->move_file($uri,$destination3);
		$j++;
	    }
	    
	    return $file_id;
	}
    }
    
    public function get_dataset_files($dataset_id)
    {
        $result  = db_query("SELECT file_location,filename,file_id FROM pdps_files WHERE file_type_id = {$dataset_id} AND file_type = 2");
        $i=0;
        
        $files = array();
        
        foreach($result as $record)
        {
            $files[$i]['location'] = $record->file_location;
            $files[$i]['filename'] = $record->filename;
            $files[$i]['file_id']  = $record->file_id;
            $i++;
        }
        $files['total'] = $i;
        
        return $files;
    }
    
    public function move_file($old_file_destination,$new_file_destination)
    {
		if (copy($old_file_destination,$new_file_destination))
		{
		    unlink($old_file_destination);
		}
     
    }
    
    /*
     * $folder_name: the name of the folder to create
     * the name of the folder is the name of the dataset
	 * If the folder is not created the it return false
	 * If the folder is created then it return true
     *
     */
    public function create_dataset_folder($folder_name)
    {
		$this_directory = getcwd();
		$uri 	        = $this_directory.'/sites/default/files/DATASETS/'.$folder_name.'';
		$folder_is_created = is_dir($uri);
		$created_folder_for_dataset = FALSE;
		if($folder_is_created == FALSE)
		{
			$msg = drupal_mkdir($uri, $mode =  FALSE,$recursive = FALSE,$context = NULL);
			drupal_chmod($uri,$mode=NULL);
			
			$document_uri = $this_directory.'/sites/default/files/DATASETS/'.$folder_name.'/Documents';
			drupal_mkdir($document_uri, $mode =  FALSE,$recursive = FALSE,$context = NULL);
			drupal_chmod($document_uri,$mode=NULL);
			$created_folder_for_dataset = TRUE;
		}
		
		return $created_folder_for_dataset;
	
    }
    
    function delete_file($dataset_name,$file_name)
    {
		$this_directory = getcwd();
		$dir 	        = $this_directory.'/sites/default/files/DATASETS/'.$dataset_name.'/Documents/'.$file_name;
		$message 	    = unlink($dir);
		return $message;
    }
    
    function delete_dataset_file($dataset_name,$file_name)
    {
		$this_directory = getcwd();
		$dir	   = $this_directory.'/sites/default/files/DATASETS/'.$dataset_name.'/'.$file_name;
		$message = unlink($dir);
    }
    
    function rename_dataset_file($previous_dataset_name, $new_dataset_name)
    {
		$this_directory = getcwd();
		$old_folder_name	   = $this_directory.'/sites/default/files/DATASETS/'.$previous_dataset_name;
		$new_folder_name	   = $this_directory.'/sites/default/files/DATASETS/'.$new_dataset_name;
		rename($old_folder_name, $new_folder_name);
    }
    
    
    /*
     * This function checks if the folder to be created
     * hasn't already been created.
     */
    public function is_folder_created($folder_directory)
    {
		$response = is_dir($folder_directory);
		
		return $response;
    }
    
    public function delete_temporary_files()
    {
      	$deleted_record = db_delete('file_managed')->condition('uid',1,'=')->execute();
      
    	$this_directory = getcwd();
     	$dir	   = $this_directory.'/sites/default/files/temp_files/*';
     	foreach(glob($dir.'*.*') as $v)
      	{
        	unlink($v);
      	}
      
      	return $deleted_record;
    }

	

   /* public function delete_temporary_files()
    {
	$deleted_record = db_delete('file_managed')->condition('uid',1,'=')->execute();
	return $deleted_record;
    }*/
}

?>