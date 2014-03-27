<?php $this->load->view("template_header"); ?>

<!-- added scripts for the date picker-->
<script src="<?php echo base_url() ?>js/jquery-1.9.1.js"></script>
<script src="<?php echo base_url() ?>js/jquery-ui.js"></script>
<script type="text/javascript" charset="utf-8">
            $(function()
            {
		$('#from-pick').datepicker();
                //$("#from-pick").datepicker("setDate", new Date);
            });
            
            $(function()
            {
		$('#to-pick').datepicker();
                //$("#to-pick").datepicker("setDate", new Date);
            });          
 </script> 
 <h2>Admin Dashboard</h2>
 <div>
 <?php echo anchor('files/project_files', 'Go to Files Repository', array( 'class' => 'btn btn-primary btn-small pull-right'))  ?>
</div>
<div>
 <?php echo anchor('admin/refresh_api', 'Refresh from API', array('style'=>'margin-right: 8px','class' => 'btn btn-primary btn-small pull-right'))  ?>
</div>
 <br><br>
 <?php
    echo form_open('admin/set_deadline', array(
        'class' => 'form-inline',
        'id' => 'set_deadline_form'
    ));
?>
    
 <div class="well">
    <h4>Set Deadline for Students to Choose a Project</h4>
    <br>
 
    <?php 

       echo form_label('Start Date:', 'from-pick');
       echo form_input(array(
                       'id' => 'from-pick',
                       'name' => 'from-pick',
                       'class' => 'input-small',
                       'placeholder' => 'mm/dd/yyyy',
                       'style' => "margin-left: 1px; margin-right: 5px",
                       'required' => '',
                       'title' => 'Start Deadline'
                   ));

       echo form_label('End Date:', 'to-pick');
       echo form_input(array(
                       'id' => 'to-pick',
                       'name' => 'to-pick',
                       'class' => 'input-small',
                       'placeholder' => 'mm/dd/yyyy',
                       'style' => "margin-left: 1px; margin-right: 7px",
                       'required' => '',
                       'title' => 'End Deadline'
                   ));
       
       echo form_submit(array(
           'id' => 'btn',
           'name' => 'deadline',
           'type' => 'Submit',
           'class' => 'btn btn-info',
           'value' => 'Set Deadline'
       ));

       //$currentTerm = $this->spw_term_model->getCurrentTermInfo();
       $term = SPW_Term_Model::getInstance();
       $currentTerm = $term -> getCurrentTermInfo();
       echo form_close()
    ?>
    <br>
    <br>
    <table class="table" >
        <thead>
            <tr>
                <th>Join/Leave Period Begins</th>
                <th>Join/Leave Deadline</th>
            </tr>
        </thead>
        <tr class="info">
            <?php
                //display the deadline information for the current term in a single-rowed column
                echo "<td>". date("m-d-Y", strtotime($currentTerm->start_date))."</td>";
                echo "<td>". date("m-d-Y", strtotime($currentTerm->end_date))."</td>";
            ?>
        </tr>
    </table>
 </div><!--end well -->

<?php
    echo form_open('admin/register_professor', array(
        'class' => '',
        'id' => 'registration_form'
    ));
?>
<div class="well">
    <div class="text-center">
        <h4>Create a New Professor User</h4>
    </div>
    
    <script type="text/javascript">
        function pwd_should_match()
        {
            var password_confirm = document.getElementById('password_2');
            if (password_confirm.value != document.getElementById('password_1').value)
            {
                password_confirm.setCustomValidity('Passwords do not match');
            }
            else if (password_confirm.value.length < 6 || document.getElementById('password_1').value.length < 6)
            {
                password_confirm.setCustomValidity('Passwords are too short (min 6 characters)');
            }
            else
            {
                password_confirm.setCustomValidity('');
            }
        }
    </script>
    <?php
    
        echo("<div>");
        echo form_input(array(
                    'id' => 'first_name',
                    'name' => 'first_name',
                    'type' => 'text',
                    'placeholder' => 'First Name',
                    'required' => '',
                    'title' => 'First Name'
                ));
        echo("</div>");
        echo("<div>");
        echo form_input(array(
                    'id' => 'last_name',
                    'name' => 'last_name',
                    'type' => 'text',
                    'placeholder' => 'Last Name',
                    'required' => '',
                    'title' => 'Last Name'
                ));
        echo("</div>");
        echo("<div>");
        echo form_input(array(
                        'id' => 'email_address',
                        'name' => 'email_address',
                        'type' => 'email',
                        'placeholder' => 'email@example.com',
                        'value' => set_value('email_address'),
                        'required' => '',
                        'title' => 'Email address'
                    ));
        echo("</div>");
        
        echo("<div>");
        echo form_password(array(
                        'id' => 'password_1',
                        'name' => 'password_1',
                        'placeholder' => 'Password',
                        'required' => '',
                        'title' => 'Password'
                    ));
        echo("</div>");

        echo("<div>");
        echo form_password(array(
                        'id' => 'password_2',
                        'name' => 'password_2',
                        'placeholder' => 'Confirm Password',
                        'required' => '',
                        'title' => 'Password Confirmation',
                        'oninput' => 'pwd_should_match()'
                    ));
        echo("</div>");
        
        echo("<div>");
        echo form_submit(array(
            'id' => 'btn',
            'name' => 'accounts',
            'type' => 'Submit',
            'class' => 'btn btn-info',
            'value' => 'Create Professor User'
        ));
        echo("</div>");
        
        echo form_close()
    ?>
</div>
 
<?php
    echo form_open('admin/activate_deactive_users', array(
        'class' => '',
        'id' => 'act_deact_form'
    ));
?>
 
<div class="well">
    <div class="text-center">
        <h4>Active and Deactivate Users</h4>
    </div>
    <table class = "table table-striped">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Select</th>
            </tr>
        </thead>
    <?php
        //select all the users that are in the system (except the head professor).
        $query = $this->db->query('SELECT id, first_name, last_name, email, role, status FROM spw_user
                                    WHERE role != "HEAD"
                                    ORDER BY role, last_name');

        foreach ($query->result_array() as $row)
         {
            if ($row['status'] == 'ACTIVE')
                 echo "<tr class=\"success\">";
            else 
                 echo "<tr class=\"error\">";
            echo "<td>".$row['first_name']."</td>";
            echo "<td>".$row['last_name']."</td>";
            echo "<td>".$row['email']."</td>";
            echo "<td>".$row['role']."</td>";
            echo "<td>".$row['status']."</td>";
            if ($row['role'] == 'STUDENT'){ echo "<td>Managed by API</td>";}
            else{echo "<td><input type=\"checkbox\" name=\"users[]\" value=\"" . $row['id']."\"></td>";}
            echo "</tr>";
        }
    ?>
    </table>

    <?php echo 'Choose an action to apply:'?>
    <br>
    <br>

    <div text-align: center>
        <label class="radio">
          <input type="radio" name="action" id="act" value="Activate" checked>
          Activate the selected user(s)
        </label>
        <label class="radio">
          <input type="radio" name="action" id="deact" value="Deactivate">
          Deactivate the selected user(s)
        </label>
    </div>
    
    <br>

    <?php 
        echo form_submit(array(
            'id' => 'btn-act-deact',
            'name' => 'activate',
            'type' => 'Submit',
            'class' => 'btn btn-info',
            'value' => 'Execute Changes'
        ));
        
        echo form_close()
    ?>
</div>
 
<?php $this->load->view("template_footer"); ?>
