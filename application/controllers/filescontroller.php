<?php    
include("file_tree_library.class.php"); 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start();

class FilesController extends CI_Controller {

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
        $this->load->model('spw_uploaded_file_model');
        $this->load->library('unit_test');        
    }

    public function project_files() 
    {        
        //if the user is logged in, then grant access
        if( isUserLoggedIn($this))
            $this->load->view('project_files');
        else
           redirect('home','refresh');     
    }     

    public function milestone_files() 
    {        
        //if the user is logged in, then grant access
        if( isUserLoggedIn($this))
        {
            $this->load->view('milestone_files');
        }
        else            
            redirect('home','refresh');     
    } 
    
    public function head_guide()
    {           
        $this->download_single_file("./UserGuide/Head.pdf") ;
    }
    public function mentors_guide()
    {           
        $this->download_single_file("./UserGuide/Mentor.pdf") ;
    }
    
     public function students_guide()
    {           
        $this->download_single_file("./UserGuide/Student.pdf") ;
    }
        
    public function download_single_file($file)
    {       
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
    
    //Function that handles the uploading of documents
    public function do_upload()
    {
        $filecheck = basename($_FILES['userfile']['name']);
        //replace all spaces with underscores
        $file = str_replace(' ', '_', $filecheck);
        //extract the extension
        $ext = strtolower(substr($filecheck, strrpos($file, '.') + 1));        
        
        //if the extension is empty, then the user has not made a file selection
        if ($ext == "")
        {
            $msg = 'Please choose a file to upload';
            setErrorFlashMessage($this, $msg);
            redirect('files/project_files');
        }
        //retrieve the name of the file
        $filename = substr($file, 0, strrpos($file, '.'));
        
        //check that the uploads directory exists, if not then create it and grant all permissions
        $dir = './uploads/';
        if (!file_exists($dir) and !is_dir($dir)) 
        {
            mkdir($dir, 0777);      
        } 
        //check that the <project_name> directory exists, if not then create it and grant all permissions        
        $project_name = str_replace(' ', '_', $this->input->post('projects'));
        $dir2 = $dir.$project_name.'/';
        $directory2 = str_replace(":", "", $dir2);

        if (!file_exists($directory2) and !is_dir($directory2)) 
        {
            mkdir($directory2, 0777);      
        } 
        //check that the <milestone_name> directory exists, if not then create it and grant all permissions
        $milestone_name = str_replace(' ', '_', $this->input->post('milestones'));
        $dir3 = $directory2.$milestone_name.'/';
        $directory3 = str_replace(":", "", $dir3);
        if (!file_exists($directory3) and !is_dir($directory3)) 
        {
            mkdir($directory3, 0777);      
        }   
        $user = getCurrentUserId($this);
        $config['upload_path'] = $directory3;
        $config['allowed_types'] = '*';
        $config['file_name'] = $filename;
        $config['overwrite'] = true;

        $this->load->library('upload', $config);

        if ( !$this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());
            $msg = 'File upload was unsuccessful. '.$this->upload->display_errors(); //' '.$dir3.' '.$filename;****************************************
            setErrorFlashMessage($this, $msg);
            if($this->input->post('order') == 'byProj')
            {
                redirect('files/project_files');
            } else {
                redirect('files/milestone_files');
            }                 
            return;
        }
        else
        {
//            echo 'file: '.$file;
//            echo 'dir3: '.$dir3;
//            echo readline();
            $uploaded_file =  new SPW_Uploaded_File_Model();
            $uploaded_file->insert($file, $dir3, $user, $this->input->post('projects'), $this->input->post('milestones'));

            $data = array('upload_data' => $this->upload->data());
            $msg = 'Your upload was successful!';
            setFlashMessage($this, $msg);
            if($this->input->post('order') == 'byProj')
            {
                redirect('files/project_files', 'refresh');
            } else {
                redirect('files/milestone_files', 'refresh');
            }            
        }        
    }
    
    public function download_delete_files()
    {
        $count = 0;
        if ($this->input->post('action') === 'Delete')
        {
            if (is_array($this->input->post('delete_files'))) 
            {
                //retrieve all the ids from the array
                foreach ($this->input->post('delete_files') as $key => $value) 
                {
                    $file_path = $this->spw_uploaded_file_model->get_file_path($value);
                    $this->spw_uploaded_file_model->delete($file_path);
                    unlink($file_path);
                    $count++;
                } 
            }
            if ($count > 0)
            {
                $msg = 'Successfully deleted ' . $count . ' file(s)';
                setFlashMessage($this, $msg);
            }
            else 
            {
                $msg = 'No files were selected for deletion';
                setErrorFlashMessage($this, $msg);
            }
        }
        else if ($this->input->post('download_files'))
        {
            foreach ($this->input->post('download_files') as $key => $value) 
            {
                $file_path = $this->spw_uploaded_file_model->get_file_path($value);
//                echo 'file: '.$file_path;
//                readline();
                $this->download_single_file($file_path);
            } 
        }
        redirect('files/project_files');
    }                    
    //this function returns a list of files of a specific category from all projects
    public function getFilesFrom($category)
    {
        return;
    }
    
    public function getMyProjects($id)
    {
        $list = $this->spw_uploaded_file_model->getMyProjects($id);
        return $list;
    }
    
    public function getProjects()
    {
        $list = $this->spw_uploaded_file_model->getProjectList();
        return $list;        
    }
    
    public function getMilestones()
    {
        $list = $this->spw_uploaded_file_model->getMilestoneList();
        return $list;
    }
}
