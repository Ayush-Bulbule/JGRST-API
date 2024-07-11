<?php

namespace App\Controllers;

use App\Libraries\TwilioService;
use CodeIgniter\Controller;
use App\Models\Users;
use App\Models\Verification;

class Authentication extends Controller
{

    use \CodeIgniter\API\ResponseTrait;
    public function index()
    {
        $response = ['message' => 'This is JGRST Auth API'];

        return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
    }

    public function login()
    {
        //Login User 
        $request = service('request');
        $mobile_no = $request->getPost('mobile_no');
        $password = $request->getPost('password');

        $usersModel = new Users();
        $user = $usersModel->where('mobile_no', $mobile_no)->first();

        //if phone no does not exist 
        if (empty($user)) {
            $json = json_encode([
                'status' => 'error',
                'message' => 'Invalid phone number.'
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(401);
        }

        //if phone no exists, verify password
        if ($password != $user['password']) {
            $json = json_encode([
                'status' => 'error',
                'message' => 'Invalid password.'
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(401);
        }

        //if password is correct, send user details
        $json = json_encode([
            'status' => 'success',
            'message' => 'Login successful.',
            'data' => [
                'user_id' => $user['user_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'mobile_no' => $user['mobile_no']
            ]
        ]);

        return $this->response->setContentType('application/json')->setJSON($json)->setStatusCode(200);
    }



    //Register -----------------------------------------------------------------------------------------------
    public function signup()
    {
        $request = service('request');
        $name = $request->getPost('name');
        $mobile_no = $request->getPost('mobile_no');
        $email = $request->getPost('email');
        $password = $request->getPost('password');
        $gotra = $request->getPost('gotra');
        $rashi = $request->getPost('rashi');
        $nakshatra = $request->getPost('nakshatra');
        $devotee_group = $request->getPost('devotee_group');

        $usersModel = new Users();
        $existingUser = $usersModel->where('mobile_no', $mobile_no)->first();

        // print_r($request->getPost());
        if (!empty($existingUser)) {
            $json = [
                'status' => 'error',
                'message' => 'Phone number already exists.'
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }

        //     // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'name' => $name,
            'mobile_no' => $mobile_no,
            'email' => $email,
            'password' => $password,
            'gotra' => $gotra,
            'rashi' => $rashi,
            'nakshatra' => $nakshatra,
            'devotee_group' => $devotee_group
        ];

        try {
            $usersModel->insert($data);


            //verify phone number
            $twilioService = new TwilioService();
            //generate 6 digit random number
            $otp = rand(100000, 999999);

            //sa
            $otp_message = 'Your JGRST verification code is ' . $otp . '.';
            $twilioService->sendSMS($mobile_no, $otp_message);

            //save otp in db
            $verifyModel = new Verification();

            $verifyModel->insert([
                'user_id' => $usersModel->getInsertID(),
                'otp' => $otp
            ]);

            $json = [
                'status' => 'success',
                'message' => 'OTP sent successfully.',
                'user_id' => $usersModel->getInsertID(),
                'otp' => $otp
            ];

            return $this->response->setJSON($json)->setStatusCode(200);
        } catch (\Exception $e) {
            // echo $e->getMessage();
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    //Public function verify otp
    public function verify_otp()
    {
        $request = service('request');
        // $mobile_no = $request->getPost('mobile_no');
        $user_id = $request->getPost('user_id');
        $otp = $request->getPost('otp');

        $verifyModel = new Verification();

        //is user or opt not recieved
        if (empty($user_id) || empty($otp)) {
            $json = [
                'status' => 'error',
                'message' => 'Invalid request.'
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }

        $existingOtp = $verifyModel->where('user_id', $user_id)->first();

        if ($existingOtp['otp'] == $otp) {
            $json = [
                'status' => 'success',
                'message' => 'OTP verified successfully.'
            ];

            //update user status
            $usersModel = new Users();
            try {
                $verifyModel->where('user_id', $user_id)->set(['verified' => 'yes', 'otp' => '000000'])->update();
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            //send user details
            $user = $usersModel->where('user_id', $user_id)->first();
            $data = [
                'user_id' => $user['user_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'mobile_no' => $user['mobile_no']
            ];
            $json['data'] = $data;

            return $this->response->setJSON($json)->setStatusCode(200);
        } else {
            $json = [
                'status' => 'error',
                'message' => 'Invalid OTP.'
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function sso_login()
    {
        try {
            //
            $request = service('request');
            $mobile_no = $request->getPost('mobile_no');

            $otp = rand(100000, 999999);
            //check for user if existes then just update his db else in create new user and save his otp in otp table agter getting insert id
            $usersModel = new Users();
            $verifyModel = new Verification();
            $user = $usersModel->where('mobile_no', $mobile_no)->first();

            //if phone no does not exist
            if (empty($user)) {
                //svae user to db


                $data = [
                    'name' => 'JGRST SSO User',
                    'mobile_no' => $mobile_no,
                ];
                $usersModel->insert($data);
                $user_id = $usersModel->getInsertID();

                //save otp in db
                $verifyModel->insert([
                    'user_id' => $user_id,
                    'otp' => $otp
                ]);
            } else {
                $user_id = $user['user_id'];
                // update otp of $user
                $verifyModel->where('user_id', $user_id)->set(['otp' => $otp])->update();
            }

            //send otp
            $twilioService = new TwilioService();
            // generate 6 digit random number
            //send otp
            $otp_message = 'Your JGRST verification code is ' . $otp . '.';
            $twilioService->sendSMS($mobile_no, $otp_message);


            $json = json_encode([
                'status' => 'success',
                'message' => 'OTP sent successfully.'
            ]);
            return $this->response->setContentType('application/json')->setJSON($json)->setStatusCode(200);
        } catch (\Exception $e) {
            echo $e->getMessage();
            //formulate error json
            $json = json_encode([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'actual' => $e->getMessage()
            ]);
            return $this->response->setContentType('application/json')->setJSON($json)->setStatusCode(400);
        }
    }

    public function sso_login_o()
    {
        $request = service('request');
        $mobile_no = $request->getPost('mobile_no');

        $usersModel = new Users();
        $user = $usersModel->where('mobile_no', $mobile_no)->first();

        //if phone no does not exist 
        if (empty($user)) {
            $json = json_encode([
                'status' => 'error',
                'message' => 'Invalid phone number.'
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(401);
        }

        //now generate otp and send
        $twilioService = new TwilioService();
        //generate 6 digit random number
        $otp = rand(100000, 999999);


        //save to db or update
        $verifyModel = new Verification();
        $existingOtp = $verifyModel->where('user_id', $user['user_id'])->first();
        //update this by new
        if (!empty($existingOtp)) {
            $verifyModel->where('user_id', $user['user_id'])->set(['otp' => $otp])->update();
        } else {
            $verifyModel->insert([
                'user_id' => $user['user_id'],
                'otp' => $otp
            ]);
        }

        //send otp
        $otp_message = 'Your JGRST verification code is ' . $otp . '.';
        $twilioService->sendSMS($mobile_no, $otp_message);


        $json = json_encode([
            'status' => 'success',
            'message' => 'OTP sent successfully.'
        ]);

        return $this->response->setContentType('application/json')->setJSON($json)->setStatusCode(200);
    }

    public function verify_sso()
    {
        $request = service('request');
        $mobile_no = $request->getPost('mobile_no');
        $otp = $request->getPost('otp');

        $usersModel = new Users();
        $user = $usersModel->where('mobile_no', $mobile_no)->first();


        //if phone no does not exist 
        if (empty($user)) {
            $json = json_encode([
                'status' => 'error',
                'message' => 'Invalid phone number.'
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(401);
        }

        //ceheck otp
        $verifyModel = new Verification();
        $existingOtp = $verifyModel->where('user_id', $user['user_id'])->first();

        // echo $existingOtp['otp'];
        // echo $otp;

        if ($existingOtp['otp'] == $otp) {
            $json = json_encode([
                'user_id' => $user['user_id'],
                'status' => 'success',
                'message' => 'OTP verified successfully.'
            ]);
            //now update otp to 000000
            $verifyModel->where('user_id', $user['user_id'])->set(['otp' => '000000'])->update();

            return $this->response->setContentType('application/json')->setJSON($json)->setStatusCode(200);
        } else {
            $json = json_encode([
                'status' => 'error',
                'message' => 'Invalid OTP.'
            ]);
            return $this->response->setContentType('application/json')->setJSON($json)->setStatusCode(401);
        }
    }


    public function verify_forgot()
    {
        $request = service('request');
        $mobile_no = $request->getPost('mobile_no');
        $otp = $request->getPost('otp');

        $usersModel = new Users();
        $user = $usersModel->where('mobile_no', $mobile_no)->first();


        //if phone no does not exist 
        if (empty($user)) {
            $json = json_encode([
                'status' => 'error',
                'message' => 'Invalid phone number.'
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(401);
        }

        //ceheck otp
        $verifyModel = new Verification();
        $existingOtp = $verifyModel->where('user_id', $user['user_id'])->first();

        // echo $existingOtp['otp'];
        // echo $otp;

        if ($existingOtp['otp'] == $otp) {
            $json = json_encode([
                'status' => 'success',
                'message' => 'OTP verified successfully.'
            ]);
            //now update otp to 000000
            $verifyModel->where('user_id', $user['user_id'])->set(['otp' => '000000'])->update();

            return $this->response->setContentType('application/json')->setJSON($json)->setStatusCode(200);
        } else {
            $json = json_encode([
                'status' => 'error',
                'message' => 'Invalid OTP.'
            ]);
            return $this->response->setContentType('application/json')->setJSON($json)->setStatusCode(401);
        }
    }

    public function update_password()
    {
        $request = service('request');
        $mobile_no = $request->getPost('mobile_no');
        $password = $request->getPost('password');

        $usersModel = new Users();
        $user = $usersModel->where('mobile_no', $mobile_no)->first();

        //if phone no does not exist
        if (empty($user)) {
            $json = json_encode([
                'status' => 'error',
                'message' => 'Invalid phone number.'
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(401);
        }
        //update no
        $usersModel->where('mobile_no', $mobile_no)->set(['password' => $password])->update();

        $json = json_encode([
            'status' => 'success',
            'message' => 'Password updated successfully.'
        ]);
        return $this->response->setContentType('application/json')->setJSON($json)->setStatusCode(200);
    }


    // The rest of the methods (get_subhashitas, get_gallery, get_events, get_sevaLists, get_notifications) will follow a similar pattern. Adjust them accordingly.
}
