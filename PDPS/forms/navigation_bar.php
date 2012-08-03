<?php

$form['navigation_bar'] = array(
        '#markup' => "
            <table style='background-color:rgb(51,153,255);border-color:black;'>
                <tr>
					<td><a href=node/dataset_list><font color='black' size='4'>Dataset List</font></a></td>
                    <td><a href=viewDatasetInformation?ds_id=$dataset_id><font color='black' size='4'>Dataset Information</font></a></td>
                    <td><a href=viewCollaborators?ds_id=$dataset_id><font color='black' size='4'>Collaborators</font></a></td>
                    <td><a href=viewDocuments?ds_id=$dataset_id><font color='black' size='4'>Documents</font></a></td>
                </tr>
            </table>
        "
    );

?>