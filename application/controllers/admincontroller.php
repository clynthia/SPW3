<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start();

class AdminController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('nav_top');
        $this->load->helper('flash_message');
        $this->load->model('spw_user_model');
        $this->load->model('spw_term_model');
        $this->load->library('email');
        $this->load->library('unit_test');
    }

    public function index() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email_address', 'Email Address', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $data = array();

        if ($this->form_validation->run() == true) {
            $data['credentials_error'] = "";
            $this->load->model('spw_user_model');
            $res = $this->spw_user_model->verify_user($this->input->post('email_address'), $this->input->post('password'));


            if ($res !== false) {
                $role = 'STUDENT';

                foreach ($res as $row) {
                    $role = $row->role;
                }

                if ($role == 'STUDENT') {
                    //verify againgst API

                    $s_url = $this->config->item('fiu_api_url') . $this->input->post('email_address');
                    $jason_return = file_get_contents($s_url);
                    $jason_return = json_decode($jason_return);

                    $panther_user_info = (object) array(
                                'valid' => $jason_return->valid,
                                'id' => $jason_return->id,
                                'email' => $jason_return->email,
                                'firstName' => $jason_return->firstName,
                                'lastName' => $jason_return->lastName,
                                'middle' => $jason_return->middle
                    );
                    if (!$panther_user_info->valid) {
                        $data['credentials_error'] = "Invalid Credentials";
                    } else {
                        //
                        foreach ($res as $row) {

                            $sess_array = array(
                                'id' => $row->id,
                                'email' => $row->email,
                                'using' => 'fiu_senior_project',
                                'role' => $row->role
                            );
                            $this->session->set_userdata('logged_in', $sess_array);
                        }
                        redirect('home', 'refresh');
                    }
                }
                else
                {
                        foreach ($res as $row) {

                            $sess_array = array(
                                'id' => $row->id,
                                'email' => $row->email,
                                'using' => 'fiu_senior_project',
                                'role' => $row->role
                            );
                            $this->session->set_userdata('logged_in', $sess_array);
                        }
                        redirect('home', 'refresh');
                }
               
            }
            else if ($this->spw_user_model->has_correct_credentials_and_is_inactive($this->input->post('email_address'), $this->input->post('password')))
            {
                $data['credentials_error'] = "Your account has been deactivated. Contact the admin for more information.";
            }
            else
            { $data['credentials_error'] = "Invalid Credentials"; }
            
        }
       else
       {
            $data['credentials_error'] = "";
       }
       $this->load->view('login_index', $data);
    }

    public function admin_dashboard() {
       
        if( isUserLoggedIn($this) &&  isHeadProfessor($this) )
            $this->load->view('admin_dashboard');
        else
           redirect('home','refresh');     
    }
    
    public function milestones_view() {
        
        //if the user is logged in, then grant access
        if( isUserLoggedIn($this))
            $this->load->view('milestones_view');
        else
           redirect('home','refresh');     
    }

    public function register_professor() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email_address', 'Email Address', 'required|valid_email');
        $this->form_validation->set_rules('password_1', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('password_2', 'Password', 'required|min_length[6]');
        $data = array();
        
        if ($this->form_validation->run() !== false) {
            $this->load->model('spw_user_model');

            $res = $this->spw_user_model->is_spw_registered($this->input->post('email_address'));
            if ($res == false) {
                $this->spw_user_model->create_new_professor_user($this->input->post('email_address'), $this->input->post('password_1'), 
                        $this->input->post('first_name'), $this->input->post('last_name'));
                 $message =' <html >  <head><title></title></head>
                            <body>  <he <h2>Welcome to the Senior Project Website !! </h2>
                                <p>We have created an account for you to access it.</p>
                                <p> Please log in with your email address and this temporary password:' .  $this->input->post('password_1') . ' </p>
                                    <p>Once you login, please update your profile and refer to the User Guide on the About page for help.</p>
                                <p><a href="http://srprog-spr13-01.aul.fiu.edu/senior-projects">SeniorProjectWebsite</a></p>
                            </body>
                            </html>';         
            
                send_email($this, $this->input->post('email_address'), 'Senior Project Website Account', $message );
                
                $msg = 'Successfully created a new professor user with the email: ' . $this->input->post('email_address');
                setFlashMessage($this, $msg);
               
            } else {
                $msg = 'Cannot create a professor with the email: ' . $this->input->post('email_address') . '
<br>User with this email already exists';
                setErrorFlashMessage($this, $msg);
                $data['already_registered'] = true;
            }
        }
        redirect('admin/admin_dashboard');
    }

    //need a fucntion that will retrieve all the users that are currently in the system
    public function activate_deactive_users() 
    {    
        $updates = 0;
        if ($this->input->post('action') === 'Deactivate') {
            if (is_array($this->input->post('users'))) {
                //retrieve all the ids from the array
                foreach ($this->input->post('users') as $key => $value) {
                    $this->spw_user_model->change_status_to_inactive($value);
                    $updates++;
                }
                
                $msg = 'Successfully deactivated ' . $updates . ' user(s)';
                setFlashMessage($this, $msg);
            }
        } else if ($this->input->post('action') === 'Activate') {
            if (is_array($this->input->post('users'))) {
                //retrieve all the ids from the array
                foreach ($this->input->post('users') as $key => $value) {
                    $this->spw_user_model->change_status_to_active($value);
                    $updates++;
                }
                
                $msg = 'Successfully activated ' . $updates . ' user(s)';
                setFlashMessage($this, $msg);
            }
        }

        redirect('admin/admin_dashboard');
    }
    
    public function set_deadline()
    {    
        //This will return the epoch date: 1970-01-01
        $epochDate = date("Y-m-d", strtotime( "//"));
    
        //convert the text fields into date objects
        $startDeadline = date("Y-m-d", strtotime($this->input->post('from-pick')));
        $endDeadline = date ("Y-m-d", strtotime($this->input->post('to-pick')));
        
        //check that both dates can actually be parsed into a date not equal to epoch
        if($startDeadline == $epochDate)
        {
            $msg = 'The input: ' . $this->input->post('from-pick'). ' cannot be parsed as a valid date.
                <br>Use the calendar to make your selections.';
            setErrorFlashMessage($this, $msg);
            
            redirect('admin/admin_dashboard');
            return;
        }
        else if ($endDeadline == $epochDate)
        {
            $msg = 'The input: ' . $this->input->post('to-pick'). ' cannot be parsed as a valid date.
                <br>Use the calendar to make your selections.';
            setErrorFlashMessage($this, $msg);
            
            redirect('admin/admin_dashboard');
            return;
        }
        //if the end date is less than or equal to the start date then this isn't a valid time window
        else if ($endDeadline <= $startDeadline)
        {
            $msg = 'The end date must be greater than the start date to appropiately define a realistic time window.';
            setErrorFlashMessage($this, $msg);
            
            redirect('admin/admin_dashboard');
            return;
        }
        //both dates are valid, so proceed to insert them into the deadline
        else
        {
            //retrieve the information for the ongoing semester
            //$currentTerm = $this->spw_term_model->getCurrentTermInfo();
            
             $term = SPW_Term_Model::getInstance();
             $term -> setDeadline($startDeadline, $endDeadline);
            //$this->spw_term_model->setDeadline($startDeadline, $endDeadline);
            
            $msg = 'Successfully updated the join/leave project time period';
            setFlashMessage($this, $msg);
            
            redirect('admin/admin_dashboard');
        }   
    }
    
     public function refresh_api()
     {
         $s_url = $this->config->item('fiu_api_refresh') ;
         $jason_return = file_get_contents( $s_url);
         if ($jason_return == 'OK')
         {
              setFlashMessage($this, "Succesfully update from API");
             
         }
         else 
         {
             setErrorFlashMessage($this, "There was an error on the API. Please verify the server.");
         }
         redirect('admin/admin_dashboard');
     }
   

}