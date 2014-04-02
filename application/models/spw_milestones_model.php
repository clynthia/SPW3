<?php
class SPW_Milestones_Model extends CI_Model
{
    public $id;
    public $milestone_name;
    public $due_date;
        
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
        $this->load->helper('file');
    }    
    
    public function insert_milestones($id, $name, $due_date)
    {
        $data = array(  'milestone_id'   => $id,
                        'milestone_name' => $name,        
                        'due_date'       => $due_date,
                        'deleted'        => 'false'
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
    
    public function update_row($id, $name, $due_date, $deleted)
    {
        $oldName = "";
        
        //retrieving old milestone name before change
        $this->db->select('milestone_name');
        $this->db->from('spw_milestones');
        $this->db->where('milestone_id', $id); 
        $query = $this->db->get();                               
                                
        foreach($query->result_array() as $row)
        {
            $oldName = $row['milestone_name'];           
        }
        
        $this->db->select('id, path_to_file');
        $this->db->from('spw_uploaded_file');
        $this->db->where('milestone_name', $oldName); 
        $query2 = $this->db->get();
        
        foreach($query2->result_array() as $row)
        {
            $path = $row['path_to_file'];
            $name2 = str_replace(' ', '_', $name);
            $oldName = str_replace(' ', '_', $oldName);
            $new = array($name2);
            $old = array($oldName);
  //******************************************************************************************************************************************        
            $newPath = str_replace($old, $new, $path);     
            rename($path, $newPath);                 
        }
        
        $data = array(  'milestone_id'   => $id,
                        'milestone_name' => $name,      
                        'due_date'       => $due_date,
                        'deleted'        => $deleted
                      );
        $this->db->where('milestone_id', $id);
        $this->db->update('spw_milestones', $data);
    }    

    public function delete_milestone($milestone_id)
    {  
        $data = array(  'milestone_id' => $milestone_id,
                        'deleted'      => 'true'
                      );
        $this->db->where('milestone_id', $milestone_id);
        $this->db->update('spw_milestones', $data);
    }
}
?>


