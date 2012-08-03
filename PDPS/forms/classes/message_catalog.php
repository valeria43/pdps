<?php

class Message_Catalog
{

    public function message_insert_database_record($insert_record, $dataset_name)
    {
	if($insert_record > 0)
	{
	    $message = "The dataset $dataset_name has been added to the system";
	}
	else
	{
	    $message = "Dataset cannot be added";    
	}
	
	return $message;
    }
	
	public function message_insert_collaborator($collaborator_name, $insert_message)
	{
		if($insert_message > 0)
		{
			$message = "$collaborator_name has been added";
		}
		else
		{
			$message = "$collaborator_name could not be added";
		}
		
		return $message;
	}
	
	public function message_insert_document($document_name, $insert_message)
	{	
		if($insert_message > 0)
		{
			$message = "Document $document_name has been added";
		}
		else
		{
			$message = "Document $document_name could not be added";
		}
		return $message;
	}
	
	public function message_update_dataset_information($dataset_name,$update_message,$file_message)
	{
		if($update_message == 1 && $file_message > 0)
		{
			$message = "The dataset $dataset_name has been updated";
		}
		if($update_message == 1 && $file_message == 0)
		{
			$message = "The dataset $dataset_name has been updated";
		}
		if($update_message == 0 && $file_message > 0)
		{
			$message = "The dataset $dataset_name has been updated";
		}
		if($update_message == 0 &&  $file_message == 0)
		{
			$message = "No changes have been submitted";
		}
		
		return $message;
	}
	
	public function message_update_dataset_document($document_name,$update_message)
	{
		if($update_message == 1)
		{
			$message = "Document $document_name has been updated";
		}
		else
		{
			$message = "Document $document_name could not been updated";
		}
		return $message;
	}
	
	public function message_update_dataset_collaborators($update_message)
	{
	
		if($update_message['1'] > 0)
		{
			$message = "Dataset Collaborator(s) have been updated";
		}
		if($update_message['0'] == $update_message['total'])
		{
			$message = "No changes have been submited";
		}
		return $message;
	}
	
	public function message_delete_dataset_documents($delete_doc,$total)
	{
		$i = 0;
		$count = 0;
		while($i < $total)
		{
			if($delete_doc[$i]['doc'] == 1 && $delete_doc[$i]['doc_file'] == 1 && $delete_doc[$i]['file']== 1)
			{
				$count++;
			}
			$i++;
		}
		
		if($count > 0)
		{
			$message = 'Document(s) have been deleted';
			return $message;
		}
		
	}
	
	public function message_remove_dataset($delete_message)
	{
		if($delete_message == 1)
		{
			$message = "Dataset has been deleted";
		}
		else 
		{
			$message = "Dataset could not be deleted";
		}
		
		return $message;
	}
	
	public function meessage_add_dataset_collaborator($insert_message)
	{
		if($insert_message == 0)
		{
			$message = "Collaborator has been added to Dataset";
		}
		else
		{
			$message = "Collaborator could not be added to Dataset";
		}
		return $message;
	}
	
	public function message_update_user_collaborators($update_count,$total)
	{
		if($update_count['1'] > 0)
		{
			$message = "Collaborator(s) have been updated";
		}
		if($update_count['0'] == $total)
		{
			$message = "Collaborator(s) could not be updated";
		}
		if($update_count['1'] == 0)
		{
			$message = "No changes have been submitted";
		}
		
		return $message;
	}
	
	public function message_delete_user_collaborators($one)
	{
		if($one > 0)
		{
			$message = "Collaborator(s) have been deleted";
		}
		else
		{
			$message = "Could not delete collaborator(s)";	
		}
		
		return $message;
	}
	
	public function message_remove_dataset_collaborator($delete_message)
	{
		if($delete_message == 1)
		{
			$message = 'Collaborator(s) have been deleted';
		}
		else 
		{
			$message = 'Collaborator(s) could not be deleted';
		}
			
		return $message;
	}
}
?>