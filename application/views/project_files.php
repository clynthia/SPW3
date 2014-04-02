<?php 
    $this->load->view('template_header');
    $this->load->helper('user_image');
    $this->load->helper('current_user'); 
    $this->load->helper('form');
    $this->load->helper('file');
    $order = isset($_POST['order']);
?>
        <div>
        <?php                
        if( isHeadProfessor($this) ) {
        ?>
           <h2>Files Repository</h2>
        <?php 
        } else {
        ?>
           <h3>My Project Repository</h3>             
        <?php 
        }
        ?>      
        <?php        
            if( isHeadProfessor($this) ) 
            {
                echo anchor('http://localhost/senior-projects/admin/milestones_view', 'Manage Milestones', array(
                                                     'style'   => 'float:right;margin-left: 8px',
                                                     'class'   => 'btn btn-primary'
                                                      ));       
            }                         
        ?>           
           <br><br>                              
        </div>
        <div class="well">
            <h4 style="padding-left:25px;">
                Upload: 
            </h4>
            <br>
              <?php               
              echo form_open_multipart('filescontroller/do_upload');
              ?>      
            <table style="width:60%;">
                <?php                
                if( isHeadProfessor($this) || isProfessor($this))
                {
                ?>
                <tr>
                    <td style="padding-left:30px;">
                       <b>Projects:</b>
                    </td>
                    <td style="padding-left:20px;">
                        <b>Milestones:</b>
                    </td>
               </tr>
                <?php
                }
                ?>      
               <tr>
                   <?php      
                   if(!isHeadProfessor($this) && !isProfessor($this)) 
                   {
                       ?>
                        <td >
                            <b style="margin-left:100px">Milestones:</b>
                        </td>
                        <?php
                   }
                    if(isHeadProfessor($this))
                    {  
                    ?>
                    <td style="padding-left:25px;">
                    <?php           
                        $projects = $this->db->query('SELECT id, title FROM spw_project
                                                    WHERE status = "APPROVED" OR status = "PENDING APPROVAL"
                                                    ORDER BY title');
                        $projects_list = array();
                        foreach ($projects->result_array() as $row)
                        {             
                             $projects_list[$row["title"]] = $row["title"];
                        }               
                        echo form_dropdown('projects', $projects_list);                                  
                        ?>              
                    </td>
                    <?php
                    }
                    elseif (isProfessor($this))
                    {  
                    ?>
                    <td style="padding-left:25px;">
                    <?php           
                        $projects = $this->db->query('SELECT id, title FROM spw_project
                                             WHERE mentor = "'.getCurrentUserId($this).'"
                                             ORDER BY title');
                        $projects_list = array();
                        foreach ($projects->result_array() as $row)
                        {             
                             $projects_list[$row["title"]] = $row["title"];
                        }               
                        echo form_dropdown('projects', $projects_list);                                  
                        ?>              
                    </td>
                    <?php
                    }
                    else {
                        $project_num = $this->db->query('SELECT project
                                                         FROM spw_user
                                                         WHERE id = "'.getCurrentUserId($this).'"');   
                        foreach($project_num->result_array() as $row)
                        {     
                            $projs = $this->db->query('SELECT id, title 
                                                        FROM spw_project
                                                        WHERE id = "'.$row['project'].'"
                                                        AND status = "APPROVED" OR status = "PENDING APPROVAL"');
                            $projects_list = array();                                                    
                            foreach ($projs->result_array() as $row)
                            {             
                                 $projects_list[$row["title"]] = $row["title"];
                            }               
                            echo form_hidden('projects', $projects_list[$row["title"]]);
                        }                       
                    }
                    ?>
                    <td style="padding-left:10px;">
                         <?php
                        $milestones = $this->db->query('SELECT milestone_id, milestone_name, due_date 
                                                        FROM spw_milestones
                                                        WHERE deleted = "false"
                                                        ORDER BY due_date');
                        $milestone_array = array();
                        foreach ($milestones->result_array() as $row)
                        {                  
                            $milestone_array[$row["milestone_name"]] = $row["milestone_name"];
                        } 
                        echo form_dropdown('milestones', $milestone_array);
                        ?>
                    </td>
                    <td>
                        <?php
                        echo form_submit(array(              
                                 'type'  => 'file',
                                 'class' => 'btn-small btn-info',
                                 'name'  => 'userfile',
                                 'style' => 'margin-left:10px;'
                        ));
                        ?>
                    </td>
                    <td>
                        <?php
                        echo form_submit(array(              
                                 'type'  => 'Submit',
                                 'class' => 'btn btn-primary',
                                 'value' => 'Upload File',   
                                 'style' => 'margin-left:10px;'
                        ));                                                   
                        ?> 
                    </td>    
               </tr>
            </table>
            <?php
            echo form_close();
            ?>
        </div>
        <div class="well">
        <?php

        if( isHeadProfessor($this) || isProfessor($this)) 
        {
            echo form_open('files/project_files', array(
                            'class' => $_SERVER['PHP_SELF'],
                            'method' => 'POST',
                            'id' => 'repo_view_form'
            ));       
            if ($order == 'View By Project')
            {
                echo form_submit(array(
                            'name'  => 'order',
                            'type'  => 'button',
                            'class' => 'btn btn-primary pull-left',
                            'value' => 'View By Milestone',
                            'style' => 'margin-left:30px;margin-top:25px;',
                            'onClick' => 'submit();'
                ));  
                        
            } else {
                echo form_submit(array(
                            'name'  => 'order',
                            'type'  => 'Submit',
                            'class' => 'btn btn-primary pull-left',
                            'value' => 'View By Project',
                            'style' => 'margin-left:30px;margin-top:25px;',                    
                ));         
            }
            echo form_close();   
            echo "<br><br>";
        }
        
//        ********************************************************************************
        echo form_open('filescontroller/download_delete_files', array(
                        'class' => '',
                        'id' => 'files_delete_form'
        ));
                    
        if(isHeadProfessor($this))
        {
            $projs = $this->db->query('SELECT id, title 
                                        FROM spw_project
                                        WHERE status = "APPROVED" OR status = "PENDING APPROVAL"
                                        ORDER BY title'); 
        } 
        elseif(isProfessor($this))
        {
            $projs = $this->db->query('SELECT id, title
                                        FROM spw_project
                                        WHERE mentor = "'.getCurrentUserId($this).'"
                                        AND status = "APPROVED" OR status = "PENDING APPROVAL"');
        }
        else
        {
            $project_num = $this->db->query('SELECT project
                                             FROM spw_user
                                             WHERE id = "'.getCurrentUserId($this).'"');   
            foreach($project_num->result_array() as $row)
            {     
                $projs = $this->db->query('SELECT id, title 
                                            FROM spw_project
                                            WHERE id = "'.$row['project'].'"
                                            AND status = "APPROVED" OR status = "PENDING APPROVAL"');
            }
        }
         
        $milestones = $this->db->query('SELECT milestone_id, milestone_name, due_date
                                        FROM spw_milestones    
                                        WHERE deleted = "false"
                                        ORDER BY due_date');                         
        $index = 1;
        $tree = new file_tree_library();
        if ($order == 'View By Project')
        {
            $tree->addToArrayAss(array(
                'id'    => $index, 
                'title'  => 'Projects',
                'ParentID' => 0,
                'category' => 'byProjects'
                )
            );
            
            foreach($projs->result_array() as $row)
            {     
                $index++;
                $projName = $row['title'];
                $tree->addToArrayAss(array(
                    'id'    => $index, 
                    'title'  => $projName,
                    'parentId' => 1,
                    'category' => 'project'
                    )
                );

                $parent = $index;
                foreach($milestones->result_array() as $row2)
                {     
                    $index++;
                    $milesName = $row2['milestone_name'];

                    $files = $this->db->query('SELECT id, file_name, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
                            FROM spw_uploaded_file
                            WHERE project_name = "'.$projName.'" AND milestone_name="'.$milesName.'"
                            ORDER BY upload_date');     

                    if($files->num_rows() > 0)
                    {
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $milesName,
                            'parentId' => $parent,
                            'category' => 'milestone',
                            'date' => $row2['due_date']
                        ));                    
                    }
                    else 
                    {
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $milesName,
                            'parentId' => $parent,
                            'icon'  => '',
                            'category' => 'milestone',
                            'date' => $row2['due_date']
                        )); 
                    }
                    $parent2 = $index;                                                        
                    foreach ($files->result_array() as $row3)
                    {                
                        $owner = $this->db->query('SELECT id, first_name, last_name
                                                   FROM spw_user
                                                   WHERE id = "'.$row3['uploaded_by_user'].'"');    
                        foreach ($owner->result_array() as $row4)
                        {      
                            $ownerID = $row4['id'];
                            $uploaded_by = $row4['first_name']." ".$row4['last_name'];
                        }
    //                    echo 'file_name: ' . $file_name;
    //                    $file_name = basename($row3['path_to_file']);
    //                    echo 'file_name basename: ' . $file_name;
    //                    echo readline();

                        $timestamp = $row3['upload_date'];
                        $due = substr($timestamp, 0, strrpos($timestamp, ' '));
                        $index++;
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $row3['file_name'],
                            'parentId' => $parent2,
                            'category' => 'file',
                            'code'  => $row3['id'],
                            'date' => $due,
                            'owner' => $uploaded_by,
                            'ownerID' => $ownerID
                            )
                        );                             
                    }
                }                
            }
        }
        else {
            $tree->addToArrayAss(array(
                'id'    => $index, 
                'title'  => 'Milestones',
                'ParentID' => 0,
                'category' => 'byMilestones'
                )
            );
            foreach($milestones->result_array() as $row)
            {     
                $index++;
                $milesName = $row['milestone_name'];

                $tree->addToArrayAss(array(
                    'id'    => $index, 
                    'title'  => $milesName,
                    'parentId' => 1,
                    'category' => 'milestone',
                    'date' => $row['due_date']
                    )
                );

                $parent = $index;
                foreach($projs->result_array() as $row2)
                {     
                    $index++;
                    $projName = $row2['title'];

                    $files = $this->db->query('SELECT id, file_name, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
                            FROM spw_uploaded_file
                            WHERE project_name = "'.$projName.'" AND milestone_name="'.$milesName.'"
                            ORDER BY upload_date');     

                    if($files->num_rows() > 0)
                    {
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $projName,
                            'parentId' => $parent,
                            'category' => 'project'
                        ));                    
                    }
                    else 
                    {
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $projName,
                            'parentId' => $parent,
                            'icon'  => '',
                            'category' => 'project'
                        )); 
                    }
                    $parent2 = $index;                                                        
                    foreach ($files->result_array() as $row3)
                    {                
                        $owner = $this->db->query('SELECT id, first_name, last_name
                                                   FROM spw_user
                                                   WHERE id = "'.$row3['uploaded_by_user'].'"');    
                        foreach ($owner->result_array() as $row4)
                        {      
                            $ownerID = $row4['id'];
                            $uploaded_by = $row4['first_name']." ".$row4['last_name'];
                        }
    //                    $file_name = basename($row3['path_to_file']);
                        $timestamp = $row3['upload_date'];
                        $due = substr($timestamp, 0, strrpos($timestamp, ' '));
                        $index++;
                        $tree->addToArrayAss(array(
                            'id'    => $index, 
                            'title'  => $row3['file_name'],
                            'parentId' => $parent2,
                            'category' => 'file',
                            'code'  => $row3['id'],
                            'date' => $due,
                            'owner' => $uploaded_by,
                            'ownerID' => $ownerID
                            )
                        );                             
                    }
                } 
            }
        }
    

         
//        foreach($projs->result_array() as $row)
//        {     
//            $index++;
//            $projName = $row['title'];
//            $tree->addToArrayAss(array(
//                'id'    => $index, 
//                'title'  => $projName,
//                'parentId' => 1,
//                'category' => 'project'
//                )
//            );
//
//            $parent = $index;
//            foreach($milestones->result_array() as $row2)
//            {     
//                $index++;
//                $milesName = $row2['milestone_name'];
//
//                $files = $this->db->query('SELECT id, file_name, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
//                        FROM spw_uploaded_file
//                        WHERE project_name = "'.$projName.'" AND milestone_name="'.$milesName.'"
//                        ORDER BY upload_date');     
//
//                if($files->num_rows() > 0)
//                {
//                    $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $milesName,
//                        'parentId' => $parent,
//                        'category' => 'milestone',
//                        'date' => $row2['due_date']
//                    ));                    
//                }
//                else 
//                {
//                    $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $milesName,
//                        'parentId' => $parent,
//                        'icon'  => '',
//                        'category' => 'milestone',
//                        'date' => $row2['due_date']
//                    )); 
//                }
//                $parent2 = $index;                                                        
//                foreach ($files->result_array() as $row3)
//                {                
//                    $owner = $this->db->query('SELECT id, first_name, last_name
//                                               FROM spw_user
//                                               WHERE id = "'.$row3['uploaded_by_user'].'"');    
//                    foreach ($owner->result_array() as $row4)
//                    {      
//                        $ownerID = $row4['id'];
//                        $uploaded_by = $row4['first_name']." ".$row4['last_name'];
//                    }
////                    echo 'file_name: ' . $file_name;
////                    $file_name = basename($row3['path_to_file']);
////                    echo 'file_name basename: ' . $file_name;
////                    echo readline();
//                    
//                    $timestamp = $row3['upload_date'];
//                    $due = substr($timestamp, 0, strrpos($timestamp, ' '));
//                    $index++;
//                    $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $row3['file_name'],
//                        'parentId' => $parent2,
//                        'category' => 'file',
//                        'code'  => $row3['id'],
//                        'date' => $due,
//                        'owner' => $uploaded_by,
//                        'ownerID' => $ownerID
//                        )
//                    );                             
//                }
//            }                
//        }            

        $tree->writeCSS();
        $tree->writeJavascript();
        $tree->drawTree(); 
        
        echo form_close();
            ?>
        </div>
            <?php 
    $this->load->view("template_footer");  