<?php
class SPW_Milestones_Model extends CI_Model
{
    public $id;
    public $milestone_name;
    public $path_to_folder;
    public $due_date;
        
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
        $this->load->helper('file');
    }    
    
    public function insert_milestones($id, $name, $path, $due_date)
    {
        $data = array(  'milestone_id'   => $id,
                        'milestone_name' => $name,
                        'path_to_folder' => $path,           
                        'due_date'       => $due_date
                      );
        $this->db->insert('spw_milestones', $data); 
        $this->db->insert_id();
    }
         
    public function get_all_milestones()
    {
        $query = $this->db              
                ->order_by('due_date')
                ->get('spw_milestones');        
        if($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        else { return null; }
    }
    
    public function get_row($id)
    {
        $query = $this->db
            ->where('milestone_id', $id)
            ->get('spw_milestones');   
        return $query;
    }
    
    public function update_row($id, $name, $path, $due_date)
    {
        $data = array(  'milestone_id'   => $id,
                        'milestone_name' => $name,
                        'path_to_folder' => $path,           
                        'due_date'       => $due_date
                      );
        $this->db->where('milestone_id', $id);
        $this->db->update('spw_milestones', $data);
    }

    public function get_folder_path($milestone_id)
    {  
        $query = $this->db
               ->where('milestone_id', $milestone_id)
               ->select('path_to_folder')
               ->get('spw_milestones');

        if($query->num_rows() > 0)
        {
            return $query->row()->path_to_folder;
        }
        else
        {
            return null; 
        }
    }
    
    public function delete_milestone($milestone_id)
    {  
        //************do soft delete
        $this->db->delete('spw_milestones', array('milestone_id' => $milestone_id));
    }
}
?>


