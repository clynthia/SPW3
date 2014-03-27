<?php 
    $this->load->view('template_header');
    $this->load->helper('user_image');
    $this->load->helper('current_user'); 
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
           <br>         
           <br>                              
        </div>
        <div class="well">
            <h4 style="padding-left:25px;">
                Choose the project and milestone that you wish to associate your upload with:
            </h4>
            <br>
              <?php echo form_open_multipart('filescontroller/do_upload');?>      
            <table style="width:60%;">
               <tr>
                    <td style="padding-left:30px;">
                       <b>
                            Projects:
                       </b>
                    </td>
                    <td style="padding-left:20px;">
                        <b>
                            Milestones:
                        </b>
                    </td>
               </tr>
               <tr>
                    <td style="padding-left:25px;">
                        <?php
                        $projects = $this->db->query('SELECT id, title FROM spw_project
                                             WHERE status = "APPROVED" OR status = "PENDING APPROVAL"
                                             ORDER BY title');
                        $projects_list = array();
                        $projects_list['All Projects'] = 'All Projects';
                        foreach ($projects->result_array() as $row)
                        {             
                             $projects_list[$row["title"]] = $row["title"];
                        }               
                        echo form_dropdown('projects', $projects_list);           
                        $milestones = $this->db->query('SELECT milestone_id, milestone_name 
                                                    FROM spw_milestones
                                                    ORDER BY due_date');
                        $milestone_array = array();
                        foreach ($milestones->result_array() as $row)
                        {                  
                            $milestone_array[$row["milestone_name"]] = $row["milestone_name"];
                        }
                        ?>              
                    </td>
                    <td style="padding-left:10px;">
                         <?php
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
<!--8888888888888888888-->
        <?php
            echo form_open('filescontroller/download_delete_files', array(
                'class' => '',
                'id' => 'download_delete_files_form'
            ));          
        ?>
<!--//***************-->
        <div class="well">
            <?php
            echo form_submit(array(
                        'id'    => 'byMilestone',
                        'name'  => 'byMilestone',
                        'type'  => 'Submit',
                        'class' => 'btn btn-primary pull-left',
                        'value' => 'View By Milestone',
                        'style' => 'margin-left:15px',
                        'onclick' =>'expandAll();return false'          ///**************************view BY Project
            ));    
            echo form_submit(array(
                        'id'    => 'btn-act-deact',
                        'name'  => 'action',
                        'type'  => 'Submit',
                        'class' => 'btn btn-primary pull-right',
                        'value' => 'Delete',
            ));                        
            echo form_submit(array(
                        'id'    => 'expand',
                        'name'  => 'expand',
                        'type'  => 'Submit',
                        'class' => 'btn btn-primary pull-right',
                        'value' => 'Expand All',
                        'style' => 'margin-right:20px',
                        'onclick' =>'expandAll();return false'
            ));
            echo form_submit(array(
                        'id'    => 'collapse',
                        'name'  => 'collapse',
                        'type'  => 'Submit',
                        'class' => 'btn btn-primary pull-right',
                        'value' => 'Collapse All',
                        'style' => 'margin-right:20px',
                        'onclick' => 'collapseAll();return false'
            ));
            ?>
            <br>
            <br>
            <?php
        $tree = new file_tree_library();
        $projs = $this->db->query('SELECT id, title 
                             FROM spw_project
                             WHERE status = "APPROVED" OR status = "PENDING APPROVAL"
                             ORDER BY title');   
        $milestones = $this->db->query('SELECT milestone_id, milestone_name, path_to_folder, due_date
                             FROM spw_milestones                                 
                             ORDER BY due_date');                         
        $index = 1;

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
//            $projNum = $row['id'];
            $parent = $index;
            foreach($milestones->result_array() as $row2)
            {     
                $index++;
                $milesName = $row2['milestone_name'];
//                $id = $row2['milestone_id'];                      
                $files = $this->db->query('SELECT id, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
                        FROM spw_uploaded_file
                        WHERE project_name = "'.$projName.'" AND milestone_name="'.$milesName.'"
                        ORDER BY upload_date');     
//                echo 'num rows: '.$files->num_rows();
                if($files->num_rows() > 0)
                {
                    $tree->addToArrayAss(array(
                        'id'    => $index, 
                        'title'  => $milesName,
                        'parentId' => $parent,
                        'category' => 'milestone'
                    ));                    
                }
                else 
                {
                    $tree->addToArrayAss(array(
                        'id'    => $index, 
                        'title'  => $milesName,
                        'parentId' => $parent,
                        'icon'  => '',
                        'category' => 'milestone'
                    )); 
                }
                $parent2 = $index;                                                        
                foreach ($files->result_array() as $row3)
                {                          
//                    echo 'path_to_file: '.$row3['path_to_file'];
                    
                    $file_name = basename($row3['path_to_file']);
//                    echo 'filename: '.$file_name;
                    $index++;
                    $tree->addToArrayAss(array(
                        'id'    => $index, 
                        'title'  => $file_name,
                        'parentId' => $parent2,
                        'category' => 'file',
                        'upload_date' => $row3['upload_date']
                        )
                    );                             
                }
            }                       
        }            
        $tree->writeCSS();
        $tree->writeJavascript();
        $tree->drawTree(); 
            ?>
        </div>
            <?php 
    $this->load->view("template_footer");