
<?php 
    $this->load->view("template_header");
    $this->load->helper("user_image"); 
    $this->load->helper("current_user"); 
    $this->load->helper("url");
?>
        <div>
           <h3>Manage Files Repository</h3>
           <p>Here you can add, edit or delete the milestones used during this semester:</p>           
           <br>
             <input type="Submit" name="addNew" value="Add New" id="addNew" style="margin-left: 8px" class="btn btn-primary" onclick="addNewRow()">
           <br><br>
        </div>
        <?php
            echo form_open('milestonescontroller/requestupdate', array(
                'class' => '',
                'id' => 'save_delete_milestones_form'
            ));          
        ?>
        <div class ="well">              
            <?php     
            echo form_submit(array(
                'id'    => 'btn-act-deact',
                'name'  => 'action',
                'type'  => 'Submit',
                'class' => 'btn btn-primary',
                'value' => 'Delete',
                'style' => 'float:right; margin-right:25px'
            ));
            ?>
            <br><br>
            <table id="milestone_list" class="table table-bordered">
                <thead>
                    <tr>     
                        <th></th>
                        <th>Milestone Name</th>                        
                        <th>Due Date</th>                                                
                        <th>Delete</th>
                    </tr>
                </thead>
                
            <?php                       
            if(mysql_num_rows(mysql_query("SHOW TABLES LIKE 'spw_milestones'"))==1) {
                // convert into codeigniter!!!!!!!!!!!!!!!!!!
                $sql = 'SELECT milestone_id, milestone_name, due_date
                        FROM spw_milestones
                        ORDER BY due_date';
                $query = $this->db->query($sql);
                $count = 0;
                $num_rows = $query->num_rows;
                if ($num_rows > 0)
                {
                    foreach ($query->result_array() as $row)
                     {
                        echo "<tr bgcolor=\"#F4E8D4\">";      
                        echo "<td><input type=\"text\" name=\"milestone[".$count."][id]\" value=\"".$row['milestone_id']."\" style=\"display:none;\" readonly></td>";
                        echo "<td><input type=\"text\" name=\"milestone[".$count."][name]\" value=\"".$row['milestone_name']."\"></td>";
                        echo "<td><input type=\"date\" name=\"milestone[".$count."][due]\" value=\"".$row['due_date']."\"></td>";                
                        echo "<td><input type=\"checkbox\" name=\"delete_milestones[]\" value=\"" . $row['milestone_id']."\"></td>"; 
                        echo "</tr>"; 
                        $count++;
                     }          
                }
            }
            ?>                
            </table>    
           <?php 
                echo anchor('http://localhost/senior-projects/files/project_files', 'Cancel', array(
                                                                'style'   => 'margin-left: 260px; width: 120px;',
                                                                'class'   => 'btn btn-primary'
                                                                 ));
                echo form_submit(array(
                    'id'    => 'btn-save',
                    'name'  => 'action',
                    'type'  => 'submit',
                    'style' => 'margin-left: 50px; width: 150px;',
                    'class' => 'btn btn-primary',
                    'value' => 'Save Milestones'
                ));
                
            ?>
        </div>
<?php
echo form_close();
?>
<br><br>

<?php $this->load->view("template_footer"); ?>

<script>        
    function addNewRow()
    {        
        var table = document.getElementById("milestone_list");
        var rowNumber = table.rows.length;

        var row = table.insertRow(rowNumber++);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        
        var col1 = "<input type=\"text\" name=\"milestone["+rowNumber+"][id]\" style=\"display:none;\" readonly></td>";
        var col2 = "<input type=\"text\" name=\"milestone["+rowNumber+"][name]\" placeholder=\"Enter milestone name\">";
        var col3 = "<input type=\"date\" name=\"milestone["+rowNumber+"][due]\">";
        var col4 = "<input type=\"checkbox\" name=\"delete_milestones[]\">";        
            
        cell1.innerHTML = col1.replace();
        cell2.innerHTML = col2.replace();
        cell3.innerHTML = col3.replace();
        cell4.innerHTML = col4.replace();
    }    
</script>   
