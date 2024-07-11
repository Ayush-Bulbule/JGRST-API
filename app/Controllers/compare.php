<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Admin;
use App\Models\Users;


class AuthController extends BaseController
{
    public function index()
    {
        //
        echo view('auth/login');
    }
    public function login()
    {
        // handle post login here username and password
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');



        // check username and password
        $adminModel = new Admin();
        $result = $adminModel->where('username', $username)->first();
        // print_r($result);
        // print_r($username);
        if ($result) {
            if ($result['password'] == $password) {
                // set session
                $data = [
                    'username' => $username,
                    'password' => $password,
                    'isLoggedIn' => TRUE
                ];

                session()->set($data);

                session()->setFlashdata('success', 'Login Successfull!');
                session()->markAsTempdata('message', 4);
                return redirect()->to('/dashboard');
            } else {
                session()->setFlashdata('error', 'Invalid Username or Password!');
                session()->markAsTempdata('message', 4);
                return redirect()->to('/login');
            }
        } else {
            session()->setFlashdata('error', 'Invalid Username or Password!');
            session()->markAsTempdata('message', 4);
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        //clear session and redirect to login
        session()->destroy();
        return redirect()->to('/login');
    }


    //User Auth - API 
    public function login_api()
    {
        // handle post login here username and password
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        //check in users table
        $model = new Users();
        $result = $model->where('email', $email)->first();
        if ($result) {
            // check username and password
            if ($result['password'] == $password) {
                // set session
                $data = [
                    'email' => $email,
                    'password' => $password,
                ];
                session()->set($data);
                session()->setFlashdata('success', 'Login Successfull!');
                session()->markAsTempdata('message', 4);

                //return json response api ith ok status
                // Create a new response object
                $data = [
                    'id' => $result['user_id']
                ];

                //Response
                return $this->response->setJSON($data)->setStatusCode(200);
            } else {
                $data = ['error' => "Incorrect Credentials!"];
                return $this->response->setJSON($data)->setStatusCode(401);
            }
        } else {
            $data = ['error' => "Invalid Username or Password!"];
            return $this->response->setJSON($data)->setStatusCode(400);
        }
    }


    //user api taki user_id and return user
    public function users_api()
    {
        // handle post login here username and password
        $user_id = $this->request->getPost('id');

        //check in users table
        $model = new Users();
        $result = $model->where('user_id', $user_id)->first();
        if ($result) {
            // set session
            $data = [
                'user_id' => $result['user_id'],
                'name' => $result['name'],
                'email' => $result['email'],
                'mobile_no' => $result['mobile_no']
            ];

            //Response
            return $this->response->setJSON($data)->setStatusCode(200);
        } else {
            $data = ['error' => "Invalid Username or Password!"];
            return $this->response->setJSON($data)->setStatusCode(404);
        }
    }


    public function handleMiscRequests($requestType)
    {
        // Handle miscellaneous requests here based on the $requestType
        // You can access the $requestType parameter to determine the type of request.

        // Example response
        $response = ['error' => 'You are unauthorized to make this request.'];

        return $this->response->setJSON($response)->setStatusCode(401);
    }
}
