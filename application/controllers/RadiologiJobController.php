<?php 

require_once APPPATH.'libraries/jwt/JWT.php';
require_once APPPATH.'libraries/jwt/SignatureInvalidException.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;

class RadiologiJobController extends CI_Controller {
    
    private $secret = 'secret';

    function __construct() {
        parent::__construct();
        $this->load->model('RadiologiJobModel');
    }

    function checkToken() {
        $jwt = $this->input->get_request_header('token');
        try {
            $data = JWT::decode($jwt, $this->secret, array('HS256'));
            return true;
        } catch (\Exception $e) {
            return false;
        }  
    }

    function response($response, $status) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    function getAllJob() {
        $data = $this->RadiologiJobModel->getAll();
        if($data) {
            $status = 200;
            $response = array(
                'status'    =>  true,
                'data'      =>  $data
            );
            $this->response($response, $status);
        }
    }

    function getByIdJob($id=0) {
        $data = $this->RadiologiJobModel->getById($id);
        if($data) {
            $status = 200;
            $response = array(
                'status'    =>  true,
                'data'      =>  $data
            );
            $this->response($response, $status);
        } else {
            $status = 404;
            $response = array(
                'status'    =>  false,
                'message'      =>  "user not found"
            );
            $this->response($response, $status);
        }
    }

    function addJob() {
        if($this->checkToken() == true) {
            $input      = json_decode(file_get_contents('php://input'));
            $codeJob    = $input->codeJob;
            $code       = $input->code;
            $name       = $input->name;
            $nameOther  = $input->nameOther;
            $isDel      = $input->isDel;
            $field = array(
                'code_job'  =>  $codeJob,
                'code'      =>  $code,
                'name'      =>  $name,
                'name_other'=>  $nameOther,
                'is_del'    =>  $isDel
            );
            $insertedData = $this->RadiologiJobModel->add($field);
            if($insertedData) {
                $data = $this->RadiologiJobModel->getById($insertedData);
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
        } else {
            $status = 500;
            $response = array(
                'status'    =>  false,
                'message'   =>  'token expired or not applicable'
            );
            $this->response($response, $status);
        }
    }

    function editJOb($id=0) {
        if($this->checkToken() == true) {
            $input      = json_decode(file_get_contents('php://input'));
            $codeJob    = $input->codeJob;
            $code       = $input->code;
            $name       = $input->name;
            $nameOther  = $input->nameOther;
            $isDel      = $input->isDel;
            $field = array(
                'code_job'  =>  $codeJob,
                'code'      =>  $code,
                'name'      =>  $name,
                'name_other'=>  $nameOther,
                'is_del'    =>  $isDel
            );
            $editedData = $this->RadiologiJobModel->edit($id,$field);
            if($editedData) {
                $data = $this->RadiologiJobModel->getById($id);
                $status = 201;
                $response = array(
                    'status'    =>  true,
                    'message'   =>  'edited successfully',
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
        } else {
            $status = 500;
            $response = array(
                'status'    =>  false,
                'message'   =>  'token expired or not applicable'
            );
            $this->response($response, $status);
        }
    }

    function deleteJob($id=0) {
        if($this->checkToken() == true) {
            $deletedData = $this->RadiologiJobModel->delete($id);
            if($deletedData) {
                $status = 201;
                $response = array(
                    'status'    =>  true,
                    'message'   =>  'user id '.$id.' deleted successfully',
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
        } else {
            $status = 500;
            $response = array(
                'status'    =>  false,
                'message'   =>  'token expired or not applicable'
            );
            $this->response($response, $status);
        }
    }
}
