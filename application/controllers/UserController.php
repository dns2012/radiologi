<?php 

require_once APPPATH.'libraries/jwt/JWT.php';

use \Firebase\JWT\JWT;

class UserController extends CI_Controller {
    
    private $secret = 'secret';

    function __construct() {
        parent::__construct();
        $this->load->model('UserModel');
    }
    
    function response($response, $status) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    function register() {
        $input      = json_decode(file_get_contents('php://input'));
        $username   = $input->username;
        $password   = password_hash($input->password, PASSWORD_DEFAULT);
        $field      = array(
            'username'  =>  $username,
            'password'  =>  $password
        );
        $insertedData = $this->UserModel->add($field);
        if($insertedData) {
            $data = $this->UserModel->getById($insertedData);
            $status = 201;
            $response = array(
                'status'    =>  true,
                'message'   =>  'inserted successfully',
                'data'      =>  $data
            );
            $this->response($response, $status);
        } else {
            $status = 500;
            $response = array(
                'status'    =>  false,
                'message'   =>  'some error occured'
            );
            $this->response($response, $status);
        }   
    }

    function login() {
        $input      = json_decode(file_get_contents('php://input'));
        $username   = $input->username;
        $password   = $input->password;
        $user       = $this->UserModel->getByUsername($username);
        if(!empty($user)) {
            if (password_verify($password, $user['password'])) {
                $date = new DateTime();
                $payload['id']          =   $user['id'];
                $payload['username']    =   $user['username'];
                $payload['iat']         =   $date->getTimestamp();
                $payload['exp']         =   $date->getTimestamp() + 60*60*2;
                $token  = JWT::encode($payload, $this->secret);
                $status = 200;
                $response = array(
                    'status'    =>  true,
                    'data'      =>  $user,
                    'token'     =>  $token
                );
                $this->response($response, $status);
            } else {
                $status = 500;
                $response = array(
                    'status'    =>  false,
                    'message'   =>  'password false'
                );
                $this->response($response, $status);
            }
        } else {
            $status = 404;
            $response = array(
                'status'    =>  false,
                'message'   =>  'username not registered'
            );
            $this->response($response, $status);
        }
    }
}
