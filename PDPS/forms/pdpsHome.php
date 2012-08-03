<?php

function pdps_home_form($form, &$form_state)
{
    
    global $user;
    
    $display_manager_instance = new Display_Manager();
	
    $links = $display_manager_instance->get_user_datasets($user->uid);
    
    $form['menu'] = array(
        '#markup' => $links,  
    );
    
    return $form;
    
}

?>