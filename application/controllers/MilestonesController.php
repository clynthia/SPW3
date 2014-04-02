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
//                    $file_path = $this->spw_milestones_model->get_folder_path($value);
                    $this->spw_milestones_model->delete_milestone($value);
                 
//                    delete_files($file_path, true); // delete all files/folders
//                    if($file_path != NULL)
//                    {
//                        rmdir($file_path);
                        $count++;
//                    }                    
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
        else {
//            $restore = $this->input->post('restore');
//            if(is_array($restore))
//            {
//                foreach($restore as $key => $value)
//                {
//                    $this->spw_milestones_model->update_row($value1['id'], $value1['name'],$value1['due']);
//                }
//            }
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
                            $this->spw_milestones_model->insert_milestones("", $value1['name'], $value1['due']);                                                       
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
//                                $old_path = $row->

                                if(($old_id == $value1['id']) && 
                                        ($old_name != $value1['name']) &&
                                        ($value1['name'] != null))
                                {                           
                                    //update path name
//                                    $path = str_replace(' ', '_', $value1['name']);
//                                    $fullPath = './milestones/' . $path . '/';
//                                    rename($old_path, $fullPath);
                                    $this->spw_milestones_model->update_row($value1['id'], $value1['name'],$value1['due'], 'false');
//                                    $this->spw_milestones_model->update_uploaded_file($value1['name']);
                                    $count++; 
                                }
                                elseif(($old_id == $value1['id']) && ($old_date != $value1['due']))
                                {
                                    $this->spw_milestones_model->update_row($value1['id'], $value1['name'],$value1['due'], 'false');
//                                    $this->spw_milestones_model->update_uploaded_file($value1['name']);
                                    $count++; 
                                } 
                                elseif(isset($value1['restore']))
                                {
                                    $this->spw_milestones_model->update_row($value1['id'], $value1['name'],$value1['due'], 'false');
//                                    $this->spw_uploaded_file_model->update_file($value1['name']);
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
}