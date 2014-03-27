<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start();

class MilestonesController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('nav_top');
        $this->load->helper('flash_message');
        $this->load->helper('request');
        $this->load->helper('file');
        $this->load->helper('download');
        $this->load->helper('deadline_term');
        $this->load->helper('flash_message');
        $this->load->model('spw_user_model');
        $this->load->model('spw_project_model');
        $this->load->model('spw_milestones_model');
        $this->load->library('unit_test');        
    }
    
    public function milestones_view() 
    {        
        //if the user is logged in, then grant access
        if(isUserLoggedIn($this) && isHeadProfessor($this))         
            $this->load->view('milestones_view');            
        else
           redirect('home','refresh');     
    }    
    
    //Function used to handle changes made to the milestones in the repository
    //* do soft delete
    //* when duplicates, do not refresh page just display msg and mark red the duplicates
    public function requestUpdate()
    {     
        $count = 0;
        
        //************************DELETE MILESTONES***************************
        if ($this->input->post('action') === 'Delete')
        {
            if (is_array($this->input->post('delete_milestones'))) 
            {
                foreach ($this->input->post('delete_milestones') as $key => $value) 
                {
                    $file_path = $this->spw_milestones_model->get_folder_path($value);
                    $this->spw_milestones_model->delete_milestone($value);
                 
                    delete_files($file_path, true); // delete all files/folders
                    if($file_path != NULL)
                    {
                        rmdir($file_path);
                        $count++;
                    }                    
                } 
            }
            if ($count > 0)
            {
                $msg = 'Successfully deleted ' . $count . ' milestone(s).';
                setFlashMessage($this, $msg);
            }
            else 
            {
                $msg = 'No milesetones were selected for deletion.';
                setErrorFlashMessage($this, $msg);
            }                                           
        }        
        //************************SAVE MILESTONES***************************
        else 
        {
            $milestones = $this->input->post('milestone');
//            $milestones_model =  new SPW_Milestones_Model(); 
            
            //search for duplicate entries
            $i = 1;
            $j = 1;
            if (is_array($milestones))
            {
                foreach($milestones as $key => $value1)
                {                            
                    foreach($milestones as $key => $value2)
                    {
                        if ($i != $j)
                        {
                            if(($value1['name'] == $value2['name']) && 
                                    ($value1['name'] != null) && 
                                    ($value2['name'] != null))
                            {
                                //invalid duplicate names found
                                $count = -1;
                            }
                        }    
                        $j++;
                    }
                    $j = 1;
                    $i++;
                } 
                if ($count != -1)
                {
                    //input contains all valid milestone names, proceed to save them
                    foreach($milestones as $key => $value1)
                    {         
                        if (($value1['id'] == null) && ($value1['name'] != null))
                        {
                            //new milestone
                            $path = str_replace(' ', '_', $value1['name']);                
                            $dir = './milestones/';

                            if (!file_exists($dir) and !is_dir($dir)) 
                            {
                                mkdir($dir, 0777);      
                            }              
                            $fullPath = './milestones/'.$path.'/';

                            //create folder
                            if (!file_exists($fullPath) and !is_dir($fullPath)) 
                            {
                                mkdir($fullPath, 0777);      
                            } 

                            //insert into database
                            $this->spw_milestones_model->insert_milestones("", $value1['name'], $fullPath, $value1['due']);
                            $count++;              
                        }                
                        elseif($value1['id'] != null)
                        {
                            $query = $this->spw_milestones_model->get_row($value1['id']);

                            foreach($query->result() as $row)
                            {
                                $old_id   = $row->milestone_id;
                                $old_name = $row->milestone_name;
                                $old_date = $row->due_date;
                                $old_path = $row->path_to_folder;

                                if(($old_id == $value1['id']) && 
                                        ($old_name != $value1['name']) &&
                                        ($value1['name'] != null))
                                {                           
                                    //update path name
                                    $path = str_replace(' ', '_', $value1['name']);
                                    $fullPath = './milestones/' . $path . '/';
                                    rename($old_path, $fullPath);
                                    $this->spw_milestones_model->update_row($value1['id'], $value1['name'], $fullPath, $value1['due']);
                                    $count++; 
                                }
                                elseif(($old_id == $value1['id']) && ($old_date != $value1['due']))
                                {
                                    $this->spw_milestones_model->update_row($value1['id'], $value1['name'], $old_path, $value1['due']);
                                    $count++; 
                                }   
                            }
                        }
                    }
                }
            }
        }
        if ($count > 0)
        {
            $msg = 'Successfully updated ' . $count . ' milestone(s).';
            setFlashMessage($this, $msg);
            redirect('admin/milestones_view', 'refresh');  
        }
        elseif ($count == -1)
        {
            $msg = 'Duplicate names are not allowed.';
            setErrorFlashMessage($this, $msg);
            redirect('admin/milestones_view');
        }
        else
        {
            $msg = 'No milestone(s) were updated.';
            setErrorFlashMessage($this, $msg);
            redirect('admin/milestones_view');                
        }
    }
    
//    public function displayMilestones()
//    {
//        $milestones = $this->spw_milestones_model->get_all_milestones();
//        $this->load->view('project_files2', $milestones);
////        $this->spw_milestones_model->update_row($value1['id'], $value1['name'], $fullPath, $value1['due']);
//    }
    

//    function indexAction($name) { // The index page of your site for example
//        return $this->getView()->render('helloworld.html', array('name' => $name));
//    }


//    function showFeed(){
//
//$data['done'] = 'Done';
//$this->load->view('yourview', $data);
//
//}
}
//    
//    public function updateMilestones($milestones)
//    {
//        $count = 0;
//        
//        //search for duplicate entries
//        $i = 1;
//        $j = 1;
//        if (is_array($milestones))
//        {
//            foreach($milestones as $key => $value1)
//            {                            
//                foreach($milestones as $key => $value2)
//                {
//                    if ($i != $j)
//                    {
//                        if(($value1['name'] == $value2['name']) && ($value1['name'] != null) && ($value2['name'] != null))
//                        {
//                            //invalid duplicate names found
//                            $count = -1;
//                            return $count;
//                        }
//                    }    
//                    $j++;
//                }
//                $j = 1;
//                $i++;
//            }        
//                        
//            //input contains all valid milestone names, proceed to save them
//            foreach($milestones as $key => $value1)
//            {         
////                echo "milestones: ".$value1['id']." ".$value1['name']." ".$value1['due'];
//                
//                if (($value1['id'] == null) && ($value1['name'] != null))
//                {
//                    //new milestone
//                    $path = str_replace(' ', '_', $value1['name']);                
//                    $dir = './milestones/';
//
//                    if (!file_exists($dir) and !is_dir($dir)) 
//                    {
//                        mkdir($dir, 0777);      
//                    }              
//                    $fullPath = './milestones/'.$path.'/';
//
//                    //create folder
//                    if (!file_exists($fullPath) and !is_dir($fullPath)) 
//                    {
//                        mkdir($fullPath, 0777);      
//                    } 
//
//                    //insert into database
//                    $data = array(  'milestone_id'   => "",
//                                    'milestone_name' => $value1['name'],
//                                    'path_to_folder' => $fullPath,           
//                                    'due_date'       => $value1['due']
//                                  );
//                    $this->db->insert('spw_milestones', $data); 
//                    $value1['milestone_id'] = $this->db->insert_id();
////                    echo '......';
//                    $count++;
//                }                
//                elseif($value1['id'] != null)
//                {
////                    echo "value1 ID: ".$value1['id'];
//                    //existing milestone already in database
//                    $query = $this->db
//                        ->where('milestone_id', $value1['id'])
//                        ->get('spw_milestones');
//
//                    foreach($query->result() as $row)
//                    {
//                        $old_id   = $row->milestone_id;
//                        $old_name = $row->milestone_name;
//                        $old_date = $row->due_date;
//                        $old_path = $row->path_to_folder;
//                    
//                        echo 'old name: '.$old_name."\n";
//                        echo 'new name: '.$value1['name']."\n";
//                        if(($old_id == $value1['id']) && ($old_name != $value1['name']) &&($value1['name'] != null))
//                        {                           
//                            //update path name
//                            $path = str_replace(' ', '_', $value1['name']);
//                            $fullPath = './milestones/' . $path . '/';
//
//                            rename($old_path, $fullPath);
//
//                            $data = array(  'milestone_id'   => $value1['id'],
//                                            'milestone_name' => $value1['name'],
//                                            'path_to_folder' => $fullPath,           
//                                            'due_date'       => $value1['due']
//                                          );
//                            $this->db->where('milestone_id', $value1['id']);
//                            $this->db->update('spw_milestones', $data);
////                            echo 'changing both: name and date';
//                            $count++; 
//                        }
//                        elseif(($old_id == $value1['id']) && ($old_date != $value1['due']))
//                        {
//                            $data = array(  'milestone_id'   => $value1['id'],
//                                            'milestone_name' => $value1['name'],
//                                            'path_to_folder' => $old_path,           
//                                            'due_date'       => $value1['due']
//                                          );
//                            $this->db->where('milestone_id', $value1['id']);
//                            $this->db->update('spw_milestones', $data);
////                            echo 'changing date only';
//                            $count++; 
//                        }   
//                    }
//                }
//            }
//        }
//        $wahtwe = readline();
//        return $count;
//    }
//    
//    
//    
//        $old_milestones = $this->spw_milestones_model->get_all_milestones();
//        
//        if (is_array($new_milestones) && (is_array($old_milestones)))         //What happens when there is none saved?
//        {
//            foreach ($old_milestones as $key => $oldVal) 
//            {
//                $oldName = $this->spw_milestones_model->get_milestone_name($oldVal['milestone_id']);
//                $oldDue = $this->spw_milestones_model->get_due_date($oldVal['milestone_id']);
//                $oldId = $oldVal['milestone_id'];
//                
//                foreach($new_milestones as $key => $newVal)
//                {
//                    $newName = $newVal['name'];
//                    $newDue = $newVal['due'];
//                    $newId = $newVal['id'];
//                
//                    if ($newName != null && $newId == $oldId)
//                    {
//                        if ($newName != $oldName || $newDue != $oldDue)
//                        {
//                            $milestones_model->edit_milestone($oldId, $newName, $newDue);   //update date on existing milestone
//                            break;                        
//                        }
//                        else break;   //same record, do nothing
//                    }
//                    else if($newName != null && $newId != $oldId)
//                    {
//                        if ($newName != $oldName)
//                        {
//                            print "new name ".$newName." ";
//                            print "new ID ".$newId." ";
//                            print "old ID ".$oldId." ";
//                            print "old name ".$oldName." ";
//                            
//                            $milestones_model->add_new_milestone($newName, $newDue);    //insert new record                                                                                  
//                            $count++;
//                            break 2;
//                           
//                        }
//                        else break 2;    //duplicate names error
//                    }
//                    else if ($newName == null)
//                            break 2;    //name is null, do nothing
//                    
//                   
//                }
//                
//            }
//        }
//        if ($count > 0)
//        {
//            $msg = 'Milestone(s) succesfuly updated.';
//            setFlashMessage($this, $msg);
////        }
////        else 
////        {
////            $msg = 'No milestone was created.';
////            setErrorFlashMessage($this, $msg);
////        }   
//        redirect('admin/milestones_view', 'refresh');           
//    }
//    
//    public function requestDeleteMilestones()
//    {
//        $count = 0;
//        if ($this->input->post('action') === 'Delete')
//        {
//            if (is_array($this->input->post('delete_milestones'))) 
//            {
//                //retrieve all the ids from the array
//                foreach ($this->input->post('delete_milestones') as $key => $value) {
//                    $file_path = $this->spw_milestones_model->get_folder_path($value);
//                    $this->spw_milestones_model->delete($value);
//                    unlink($file_path);
//                    $count++;
//                } 
//            }
//
//            if ($count > 0)
//            {
//                $msg = 'Successfully deleted ' . $count . ' milestone(s).';
//                setFlashMessage($this, $msg);
//            }
//            else 
//            {
//                $msg = 'No milesetone(s) were selected for deletion.';
//                setErrorFlashMessage($this, $msg);
//            }
//        }
//        redirect('files/milestones_view');
//    }
//    
//    public function delete_milestones()
//    {
//        $count = 0;
//        if ($this->input->post('action') === 'Delete')
//        {
//            if (is_array($this->input->post('delete_milestones'))) 
//            {
//                //retrieve all the ids from the array
//                foreach ($this->input->post('delete_files') as $key => $value) {
//                    $milestone_path = $this->spw_uploaded_file_model->get_file_path($value);
//                    $this->spw_uploaded_file_model->delete($value);
//                    unlink($milestone_path);
//                    $count++;
//                } 
//            }
//
//            if ($count > 0)
//            {
//                $msg = 'Successfully deleted ' . $count . ' file(s)';
//                setFlashMessage($this, $msg);
//            }
//            else 
//            {
//                $msg = 'No files were selected for deletion';
//                setErrorFlashMessage($this, $msg);
//            }
//        }
//        else if ($this->input->post('action') === 'Download')
//        {
//            if (is_array($this->input->post('download_files')) && count($this->input->post('download_files')) == 1) 
//            {
//                //retrieve all the ids from the array, in this case only one iteration
//                foreach ($this->input->post('download_files') as $key => $value) 
//                {
//                    $file_path = $this->spw_uploaded_file_model->get_file_path($value);
//                    //download the file that was selected
//                    $this->download_single_file($file_path);
//                } 
//            }
//            
//            $msg = 'A file was not selected for download';
//            
//            if (count($this->input->post('download_files')) > 1)     
//                $msg = 'Please only select a single file for download';
//            setErrorFlashMessage($this, $msg);
//        }
//        redirect('files/project_files');
//    }    
//}
