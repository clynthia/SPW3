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

    public function project_files() {
        
        //if the user is logged in, then grant access
        if( isUserLoggedIn($this))
            $this->load->view('project_files');
        else
           redirect('home','refresh');     
    }     
    
    public function milestone_files() {
        
        //if the user is logged in, then grant access
        if( isUserLoggedIn($this))
            $this->load->view('milestone_files');
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
//        $id = $this->input->post('index');
//        echo 'id: '.$id;
//        echo readline();
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
        if (!file_exists($dir2) and !is_dir($dir2)) 
        {
            mkdir($dir2, 0777);      
        } 
        //check that the <milestone_name> directory exists, if not then create it and grant all permissions
        $milestone_name = str_replace(' ', '_', $this->input->post('milestones'));
        $dir3 = $dir2.$milestone_name.'/';
        
        if (!file_exists($dir3) and !is_dir($dir3)) 
        {
            mkdir($dir3, 0777);      
        } 
        
        $user = getCurrentUserId($this);
//          
//        //check the directory uploads/user_id exists, if not then create it and grant all permissions
//        //every user will have a unique upload directory of their own
//        $dir2 = './uploads/'. $user .'/';
//        if (!file_exists($dir2) and !is_dir($dir2)) 
//        {
//            mkdir($dir2, 0777);   
//        } 

        $config['upload_path'] = $dir3;
        $config['allowed_types'] = '*';
        $config['file_name'] = $filename;
        $config['overwrite'] = true;

        $this->load->library('upload', $config);

        if ( !$this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());
            $msg = 'File upload was unsuccessful.';
            setErrorFlashMessage($this, $msg);
            redirect('files/project_files');
            return;
        }
        else
        {
            //this is the final location of the file
            $dir3 = $dir3.$file;
            $uploaded_file =  new SPW_Uploaded_File_Model();
            //store the uploaded file information in the database
            $uploaded_file->insert($filename, $dir3, $user, $this->input->post('projects'), $this->input->post('milestones'));

            $data = array('upload_data' => $this->upload->data());
            $msg = 'Your upload was successful!';
            setFlashMessage($this, $msg);
            redirect('files/project_files', 'refresh');
        }        
    }
//    delete_download_feedback
    public function download_delete_files()
    {
        $count = 0;
        if ($this->input->post('action') === 'Delete')
        {
            if (is_array($this->input->post('delete_files'))) 
            {

                //retrieve all the ids from the array
                foreach ($this->input->post('delete_files') as $key => $value) {
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
        else if ($this->input->post('action') === 'Download')
        {
//            echo 'fadafadafdafaf';
//            echo readline();
            
//            is_array($this->input->post('download_files');
            
            
//            $count = count($this->input->post('download_files'));
//            echo 'count: '.$count;
//            echo readline();
//            if (is_array($this->input->post('download_files'))) // && $count == 1) 
//            {
//                            echo 'fadafadafdafaf';
//                            echo readline();
                //retrieve all the ids from the array, in this case only one iteration
//                foreach ($this->input->post('download_files') as $key => $value) 
//                {
//            echo 'value: '.$value;
                            $value = $this->input->post('download_files');
                            echo 'value: '.$value;
                            echo readline();
                    $file_path = $this->spw_uploaded_file_model->get_file_path($value);
                    //download the file that was selected
                    $this->download_single_file($file_path);
//                } 
//            }
//            elseif($count < 1)
//            {
                $msg = 'A file was not selected for download';
//            }
//            elseif ($count > 1)   
//            {
                $msg = 'Please only select a single file for download';
                setErrorFlashMessage($this, $msg);
//            }
            
        }
        redirect('files/project_files');
    }        
    
    
//    public function renderProjectTree()
//    {
//        $tree = new file_tree_library();
//        $projs = $this->db->query('SELECT id, title 
//                             FROM spw_project
//                             WHERE status = "APPROVED" OR status = "PENDING APPROVAL"
//                             ORDER BY title');   
//        $milestones = $this->db->query('SELECT milestone_id, mileston
//        ///.;......//e_name, path_to_folder, due_date
//                             FROM spw_milestones                                 
//                             ORDER BY due_date');                         
//        $index = 1;
//
//        $tree->addToArrayAss(array(
//            'id'    => $index, 
//            'title'  => 'Projects',
//            'ParentID' => 0,
//            'category' => 'byProjects'
//            )
//        );
//
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
////            $projNum = $row['id'];
//            $parent = $index;
//            foreach($milestones->result_array() as $row2)
//            {     
//                $index++;
//                $milesName = $row2['milestone_name'];
////                $id = $row2['milestone_id'];                      
//                $files = $this->db->query('SELECT id, file_name, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
//                        FROM spw_uploaded_file
//                        WHERE project_name = "'.$projName.'" AND milestone_name="'.$milesName.'"
//                        ORDER BY upload_date');             
//                if($files->num_rows() > 0)
//                {
//                    $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $milesName,
//                        'parentId' => $parent,
//                        'category' => 'milestone'
//                    ));                    
//                }
//                else 
//                {
//                    $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $milesName,
//                        'parentId' => $parent,
//                        'icon'  => '',
//                        'category' => 'milestone'
//                    )); 
//                }
//                $parent2 = $index;                                                        
//                foreach ($files->result_array() as $row3)
//                {                        
//                    $index++;
//                    $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $row3['file_name'],
//                        'parentId' => $parent2,
//                        'category' => 'file',
//                        'upload_date' => $row3['upload_date']
//                        )
//                    );                             
//                }
//            }                       
//        }            
//        $tree->writeCSS();
//        $tree->writeJavascript();
//        $tree->drawTree(); 
//
//
//        if($tree->isEmpty())
//        {
////            echo "wtf";
////            $this->load->view('project_files');
//            $error = array('error' => $this->upload->display_errors());
//            $msg = 'Laod of file tree was unsuccessful.';
//            setErrorFlashMessage($this, $msg);
//            redirect('files/project_files');
//            return;
//        }
//        else
//        {
//            $tree->writeCSS();
//            $tree->writeJavascript();
//            $tree->drawTree(); 
//            
//            $data = array('upload_data' => $this->upload->data());
//            $msg = 'Your file tree was successful!';
//            setFlashMessage($this, $msg);
//            redirect('files/project_files', 'refresh');
////            echo "wtfe3rqewfr";
////            redirect('home','refresh');          
//        }
//    }
//    
//    public function renderMilestoneTree()
//    {
//        $tree = new file_tree_library();
//        $milestones = $this->db->query('SELECT milestone_id, milestone_name, path_to_folder, due_date
//                             FROM spw_milestones                                 
//                             ORDER BY due_date');                         
//        $projs = $this->db->query('SELECT id, title 
//                             FROM spw_project
//                             WHERE status = "APPROVED" OR status = "PENDING APPROVAL"
//                             ORDER BY title');   
//        $index = 1;
//        $tree->addToArrayAss(array(
//            'id'    => $index, 
//            'title'  => 'Milestones',
//            'ParentID' => 0,
//            'category' => 'byMilestones'
//        ));
//        foreach($milestones->result_array() as $row)
//        {
//            $index++;
//            $milesName = $row['milestone_name'];
//            $parent = $index;
//            $tree->addToArrayAss(array(
//                'id'    => $index, 
//                'title'  => $milesName,
//                'parentId' => $parent,
//                'category' => 'milestone'
//            ));                                  
//            foreach($projs->result_array() as $row2)
//            {     
//                $index++;
//                $projName = $row2['title'];
//                $parent = $index;
//                $tree->addToArrayAss(array(
//                    'id'    => $index, 
//                    'title'  => $projName,
//                    'parentId' => 1,
//                    'category' => 'project'
//                ));
//                $files = $this->db->query('SELECT id, path_to_file, uploaded_by_user, project_name, upload_date, milestone_name
//                                            FROM spw_uploaded_file
//                                            WHERE project_name = "'.$projName.'" AND milestone_name="'.$milesName.'"
//                                            ORDER BY upload_date');     
//                if($files->num_rows() > 0)
//                {
//                    $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $milesName,
//                        'parentId' => $parent,
//                        'category' => 'milestone'
//                    ));                    
//                }
//                else 
//                {
//                    $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $milesName,
//                        'parentId' => $parent,
//                        'icon'  => '',
//                        'category' => 'milestone'
//                    )); 
//                }
//                $parent2 = $index;                                                        
//                foreach ($files->result_array() as $row3)
//                {                          
//                    $file_name = basename($row3['path_to_file']);
////                    echo 'filename: '.$file_name;
//                    $index++;
//                    $tree->addToArrayAss(array(
//                        'id'    => $index, 
//                        'title'  => $file_name,
//                        'parentId' => $parent2,
//                        'category' => 'file',
//                        'upload_date' => $row3['upload_date']
//                        )
//                    );                             
//                }
//            }
//        }            
//       
//        $tree->writeCSS();
//        $tree->writeJavascript();
//        $tree->drawTree(); 
//    }
    
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
