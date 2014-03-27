<?php

class SPW_Uploaded_File_Model extends CI_Model
{
    public $id;
    public $path_to_file;
    public $uploaded_by_user;
    public $project;
    public $upload_date;
        
    public function __construct()
    {
        parent::__construct();
    }

    public function insert($filename, $path, $user_id, $proj_name, $milestone_name)
    {      
        $data = array(
//                        'id'        => $id,
                        'file_name' => $filename,
                        'path_to_file'  => $path,
                        'uploaded_by_user' => $user_id,
                        'project_name'  => $proj_name,
                        'milestone_name' => $milestone_name
                     );
        
        $query = $this->db
                      ->where('path_to_file', $path)
                      ->select('path_to_file')
                      ->get('spw_uploaded_file');
        
        //check that a record with the same path doesn't exists
        if($query->num_rows() > 0)
        {
//            echo 'W??';
            return;
            
        }
        else
        {
            $this->db->insert('spw_uploaded_file', $data); 
            $id =  $this->db->insert_id();         

            $sql = 'UPDATE spw_uploaded_file  SET upload_date = CURRENT_TIMESTAMP WHERE id  = ?';
            $this->db->query($sql, $id);		
            return;
        }   
    }
    
    public function get_files($project_name)
    {
        $query = $this->db
               ->where('project_name', $project_name)
               ->get('spw_uploaded_file');

        if($query->num_rows() > 0)
        {
            return $query;
        }    
        else
        {
            return null;
        }        
    }
    
//    public function get_file_path($file_name)
//    {  
//        $query = $this->db
//               ->where('file_name', $file_name)
//               ->select('path_to_file')
//               ->get('spw_uploaded_file');
//
//        if($query->num_rows() > 0)
//        {
//            return $query->row()->path_to_file;
//        }
//        else
//        {
//            return null; 
//        }
//    }
    
    public function get_file_path($file_id)
    {  
        $query = $this->db
               ->where('id', $file_id)
               ->select('path_to_file')
               ->get('spw_uploaded_file');

        if($query->num_rows() > 0)
        {
            return $query->row()->path_to_file;
        }
        else
        {
            return null; 
        }
    }
    
    public function delete($id)
    {  
//        echo 'file- path: '.$file_path;
//        echo readline();
        $this->db->delete('spw_uploaded_file', array('path_to_file' => $id));
    }
//    public function delete($file_path)
//    {  
//        echo 'file- path: '.$file_path;
//        echo readline();
//        $this->db->delete('spw_uploaded_file', array('path_to_file' => $file_path));
//    }
    
    public function getProjectList()
    {
        $projs = $this->db->query('SELECT id, title 
                     FROM spw_project
                     WHERE status = "APPROVED" OR status = "PENDING APPROVAL"
                     ORDER BY title'); 
        return $projs;
    }
    
    public function getMilestoneList()
    {
        $milestones = $this->db->query('SELECT milestone_id, milestone_name, path_to_folder, due_date
                             FROM spw_milestones                                 
                             ORDER BY due_date');  
        return $milestones;
    }
}	
?>