<?php

namespace App\Controllers;

use App\Libraries\TwilioService;
use App\Controllers\BaseController;
use App\Models\CalenderBlock;
use App\Models\Cart;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\Gallery;
use App\Models\Notification;
use App\Models\SevaBooking;
use App\Models\SevaCategory;
use App\Models\SevaList;
use App\Models\Subhashita;
use App\Models\Users;

class HomeController extends BaseController
{
    use \CodeIgniter\API\ResponseTrait;
    public function index()
    {
        try {
            $response = ['message' => 'This is JGRST Home API'];
            return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }


    //CRUD - User

    public function get_recent_subhashitas()
    {
        try {
            $request = service('request');
            $subhashitasModel = new Subhashita();
            $subhashitas = $subhashitasModel->orderBy('subhashita_id', 'DESC')->findAll(10);
            $json = json_encode([
                'status' => 'success',
                'message' => 'Subhashitas fetched successfully.',
                'data' => $subhashitas
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(200);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function get_gallary()
    {
        try {
            $request = service('request');
            $galleryModel = new Gallery();
            $gallery = $galleryModel->orderBy('gallery_id', 'DESC')->findAll();
            $gallery = array_map(function ($item) {
                $item['image_url'] = 'https://adminjgrst.aishwaryasoftware.xyz/uploads/' . $item['image_url'];
                return $item;
            }, $gallery);
            $json = json_encode([
                'status' => 'success',
                'message' => 'Gallery fetched successfully.',
                'data' => $gallery
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(200);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function get_events()
    {
        try {
            $request = service('request');
            $eventModel = new Event();
            $events = $eventModel->orderBy('event_id', 'DESC')->findAll();

            $events = array_map(function ($item) {
                $item['image_url'] = 'https://adminjgrst.aishwaryasoftware.xyz/uploads/' . $item['image_url'];
                return $item;
            }, $events);
            $json = json_encode([
                'status' => 'success',
                'message' => 'Events fetched successfully.',
                'data' => $events
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(200);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function get_sevalist()
    {
        try {
            $request = service('request');
            $sevalistModel = new SevaList();
            $sevalist = $sevalistModel->orderBy('seva_id', 'DESC')->findAll();
            $json = json_encode([
                'status' => 'success',
                'message' => 'Sevalist fetched successfully.',
                'data' => $sevalist
            ]);
            $sevalist = array_map(function ($item) {
                $item['image_url'] = 'https://adminjgrst.aishwaryasoftware.xyz/uploads/' . $item['image_url'];
                return $item;
            }, $sevalist);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(200);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function get_notifications()
    {
        try {
            $request = service('request');
            $notificationModel = new Notification();
            $notifications = $notificationModel->orderBy('notification_id', 'DESC')->findAll();
            $json = json_encode([
                'status' => 'success',
                'message' => 'Notifications fetched successfully.',
                'data' => $notifications
            ]);
            $this->response->setContentType('application/json');
            return $this->response->setJSON($json)->setStatusCode(200);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function get_booking_by_user_id($user_id)
    {
        try {
            $bookingModel = new SevaBooking();
            $booking = $bookingModel->where('user_id', $user_id)->findAll();

            if (count($booking) == 0) {
                $json = [
                    'status' => 'error',
                    'message' => 'No bookings found.',
                    'error' => 'No bookings found.'
                ];
                return $this->response->setJSON($json)->setStatusCode(400);
            }
            $response = [
                'user_id' => $user_id,
                'user_name' => $booking[0]['user_name'],
                'email' => $booking[0]['email_id'],
                'phone' => $booking[0]['mobile_no'],
                'bookings' => $booking
            ];
            return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function get_all_categories()
    {
        try {
            $categoryModel = new SevaCategory();
            $categories = $categoryModel->findAll();
            $data = [];
            foreach ($categories as $category) {
                $category['image_url'] = 'https://adminjgrst.aishwaryasoftware.xyz/uploads/' . $category['image_url'];
                array_push($data, $category);
            }
            return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($data);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function get_list_in_category($category_id)
    {
        try {
            $sevaListModel = new SevaList();
            $sevaList = $sevaListModel->where('seva_category_id', $category_id)->findAll();
            $sevaList = array_map(function ($item) {
                $item['image_url'] = 'https://adminjgrst.aishwaryasoftware.xyz/uploads/' . $item['image_url'];
                return $item;
            }, $sevaList);
            return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($sevaList);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function create_list_in_category($category_id)
    {
        try {
            $request = service('request');
            $sevaListModel = new SevaList();
            $data = [
                'seva_category_id' => $category_id,
                'name' => $request->getPost('name'),
                'amount' => $request->getPost('amount'),
                'time' => $request->getPost('time'),
                'description' => $request->getPost('description'),
                'no_of_persons_allowed' => $request->getPost('no_of_persons_allowed'),
                'instructions' => $request->getPost('instructions'),
            ];
            if ($data['name'] == null || $data['amount'] == null || $data['time'] == null || $data['description'] == null || $data['no_of_persons_allowed'] == null || $data['instructions'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($sevaListModel->insert($data)) {
                $response = ['message' => 'Seva List Created Successfully'];
                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Seva List Creation Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function update_list_in_category($category_id, $list_id)
    {
        try {
            $request = service('request');
            $sevaListModel = new SevaList();
            $data = [
                'seva_category_id' => $category_id,
                'name' => $request->getPost('name'),
                'amount' => $request->getPost('amount'),
                'time' => $request->getPost('time'),
                'description' => $request->getPost('description'),
                'no_of_persons_allowed' => $request->getPost('no_of_persons_allowed'),
                'instructions' => $request->getPost('instructions'),
            ];
            if ($data['name'] == null || $data['amount'] == null || $data['time'] == null || $data['description'] == null || $data['no_of_persons_allowed'] == null || $data['instructions'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($sevaListModel->update($list_id, $data)) {
                $response = ['message' => 'Seva List Updated Successfully'];
                $data['id'] = $list_id;
                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($data);
            } else {
                $response = ['message' => 'Seva List Updation Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    public function delete_list_in_category($category_id, $list_id)
    {
        try {
            $sevaListModel = new SevaList();
            $sevaList = $sevaListModel->where('seva_category_id', $category_id)->where('seva_id', $list_id)->delete();
            if ($sevaList) {
                $response = ['message' => 'Seva List Deleted Successfully'];
                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Seva List Deletion Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }



    //API to add Feedback
    public function create_feedback()
    {
        try {
            $request = service('request');
            $feedbackModel = new Feedback();
            $data = [
                'user_id' => $request->getPost('user_id'),
                'feedback_text' => $request->getPost('feedback_text'),
            ];

            // print_r($data);
            if ($data['user_id'] == null || $data['feedback_text'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($feedbackModel->insert($data)) {
                $response = ['message' => 'Feedback Added Successfully'];
                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Feedback Addition Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }

    // CRUD CART    
    public function get_cart_by_user_id($user_id)
    {
        try {
            $sevaListModel = new SevaList();
            $cartModel = new Cart();
            $cart = $cartModel->where('user_id', $user_id)->findAll();

            if (count($cart) == 0) {
                $json = [
                    'status' => 'error',
                    'message' => 'No items in cart.',
                    'error' => 'No items in cart.'
                ];
                return $this->response->setJSON($json)->setStatusCode(400);
            }

            //Map finde the seva details with cart
            $cart = array_map(function ($item) use ($sevaListModel) {
                $seva = $sevaListModel->where('seva_id', $item['seva_id'])->first();
                $item['seva_name'] = $seva['name'];
                $item['seva_amount'] = $seva['amount'];
                $item['seva_category_id'] = $seva['seva_category_id'];
                $item['seva_time'] = $seva['time'];
                $item['seva_description'] = $seva['description'];
                $item['seva_no_of_persons_allowed'] = $seva['no_of_persons_allowed'];
                $item['seva_instructions'] = $seva['instructions'];
                $item['seva_image_url'] = 'https://adminjgrst.aishwaryasoftware.xyz/uploads/' . $seva['image_url'];
                return $item;
            }, $cart);

            $response = [
                'user_id' => $user_id,
                'cart' => $cart
            ];

            return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }
    //API to add to cart user_id	seva_id	quantity	
    public function add_to_cart()
    {
        try {
            $request = service('request');
            $cartModel = new Cart();
            $data = [
                'user_id' => $request->getPost('user_id'),
                'seva_id' => $request->getPost('seva_id'),
                'quantity' => $request->getPost('quantity'),
            ];

            // print_r($data);
            if ($data['user_id'] == null || $data['seva_id'] == null || $data['quantity'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($cartModel->insert($data)) {
                $response = [
                    'message' => 'Item Added to Cart Successfully',
                    'status' => 'success'
                ];
                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Item Addition to Cart Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }

    //Update Cart Item Quantity of that product in cart post method
    public function update_cart_item_quantity()
    {
        try {
            $request = service('request');
            $cartModel = new Cart();
            $data = [
                'user_id' => $request->getPost('user_id'),
                'seva_id' => $request->getPost('seva_id'),
                'quantity' => $request->getPost('quantity'),
            ];

            // print_r($data);
            if ($data['user_id'] == null || $data['seva_id'] == null || $data['quantity'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($cartModel->update($data['seva_id'], $data)) {
                $response = [
                    'status' => 'success',
                    'message' => 'Item Quantity Updated Successfully'
                ];
                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Item Quantity Updation Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }

    //Remove Item from cart user_id seva_id
    public function remove_from_cart()
    {
        try {
            $request = service('request');
            $cartModel = new Cart();
            $data = [
                'user_id' => $request->getPost('user_id'),
                'seva_id' => $request->getPost('seva_id'),
            ];

            // print_r($data);
            if ($data['user_id'] == null || $data['seva_id'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($cartModel->where('user_id', $data['user_id'])->where('seva_id', $data['seva_id'])->delete()) {
                $response = [
                    'message' => 'Item Removed from Cart Successfully',
                    'status' => 'success'
                ];
                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Item Removal from Cart Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }


    // API to add Donation
    //"name","mobile_no","donation_type","amount","pan_no","payment_type","transaction_id"

    public function create_donation()
    {
        try {
            $request = service('request');
            $donationModel = new Donation();
            $data = [
                'name' => $request->getPost('name'),
                'mobile_no' => $request->getPost('mobile_no'),
                'donation_type' => $request->getPost('donation_type'),
                'amount' => $request->getPost('amount'),
                'pan_no' => $request->getPost('pan_no'),
                'payment_type' => $request->getPost('payment_type'),
                'transaction_id' => $request->getPost('transaction_id'),
            ];

            // print_r($data);
            if ($data['name'] == null || $data['mobile_no'] == null || $data['donation_type'] == null || $data['amount'] == null || $data['pan_no'] == null || $data['payment_type'] == null || $data['transaction_id'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($donationModel->insert($data)) {
                $response = ['message' => 'Donation Added Successfully'];
                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Donation Addition Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }

    //Add Donations with online default payment
    // "donation_id","name","mobile_no","remarks","amount","payment_type","transaction_id"
    public function add_donation()
    {
        try {
            $request = service('request');
            $donationModel = new Donation();
            $data = [
                'name' => $request->getPost('name'),
                'mobile_no' => $request->getPost('mobile_no'),
                'remarks' => $request->getPost('remarks'),
                'amount' => $request->getPost('amount'),
                'payment_type' => "online",
                'transaction_id' => $request->getPost('transaction_id'),
            ];

            // print_r($data);
            if ($data['name'] == null || $data['mobile_no'] == null || $data['remarks'] == null || $data['amount'] == null  || $data['transaction_id'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($donationModel->insert($data)) {
                $response = ['message' => 'Donation Added Successfully'];
                //send Messgae
                $twilioService = new TwilioService();

                $message = "Dear " . $data['name'] . ",\nThank you for your donation of Rs." . $data['amount'] . " towards JGRST. \nRegards,\nJGRST";
                $twilioService->sendSMS($data['mobile_no'], $message);

                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Donation Addition Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }


    //Send User Details on GET
    public function get_user_details($user_id)
    {
        try {
            $userModel = new Users();

            $users = $userModel->where('user_id', $user_id)->findAll();

            if (count($users) == 0) {
                $response = ['message' => 'User not found'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }

            //"name", "devotee_group", "email", "mobile_no", "password",  "gotra", "rashi", "nakshatra"
            $response = [
                'status' => 'success',
                'message' => 'User Details Fetched Successfully',
                'data' => $users[0]
            ];

            return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ];
            return $this->response->setJSON($json)->setStatusCode(400);
        }
    }

    //Handle insta mojo web hook
    // /final payload = {
    //     'purpose': widget.purpose + " - " + widget.buyer_name,
    //     'amount': widget.amount,
    //     'buyer_name': widget.buyer_name,
    //     'email': widget.email,
    //     'phone': widget.phone,
    //     'redirect_url': 'https://appredirect.26concepts.com/',
    //     'send_email': 'True',
    //     'webhook': 'https://api.aishwaryasoftware.xyz/api/add-donation/',
    //     'allow_repeated_payments': 'True',

    public function add_payment_instamojo()
    {
        try {
            $request = service('request');
            $donationModel = new Donation();
            $data = [
                'name' => $request->getPost('buyer_name'),
                'mobile_no' => $request->getPost('phone'),
                'purpose' => $request->getPost('purpose'),
                'amount' => $request->getPost('amount'),
                'payment_type' => "online",
            ];

            // print_r($data);
            if ($data['name'] == null || $data['mobile_no'] == null || $data['purpose'] == null || $data['amount'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($donationModel->insert($data)) {
                $response = ['message' => 'Donation Added Successfully'];
                //send Messgae
                $twilioService = new TwilioService();
                //send message - name purpose amount
                $message = "Dear " . $data['name'] . ",\nThis message is related to " . $data['purpose'] . "Thank you for your donation of Rs." . $data['amount'] . " towards JGRST. \nRegards,\nJGRST";
                // $message = "Dear " . $data['name'] . ",\nThank you for your donation of Rs." . $data['amount'] . " towards JGRST. \nRegards,\nJGRST";
                $twilioService->sendSMS($data['mobile_no'], $message);

                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Donation Addition Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }

    //Add Seva Booking API "date", "seva_category_id", "seva_id", "user_id", "user_name", "seva_type", "time", "amount", "gotra", "pan_no", "nakshatra", "rashi", "no_of_persons", "email_id", "mobile_no", "payment_type", "transaction_id"
    public function add_seva_booking()
    {
        try {
            $request = service('request');
            $sevaBookingModel = new SevaBooking();
            $data = [
                'date' => $request->getPost('date'),
                'seva_category_id' => $request->getPost('seva_category_id'),
                'seva_id' => $request->getPost('seva_id'),
                'user_id' => $request->getPost('user_id'),
                'user_name' => $request->getPost('user_name'),
                'seva_type' => $request->getPost('seva_type'),
                'time' => $request->getPost('time'),
                'amount' => $request->getPost('amount'),
                'gotra' => $request->getPost('gotra'),
                'pan_no' => $request->getPost('pan_no'),
                'nakshatra' => $request->getPost('nakshatra'),
                'rashi' => $request->getPost('rashi'),
                'no_of_persons' => $request->getPost('no_of_persons'),
                'email_id' => $request->getPost('email_id'),
                'mobile_no' => $request->getPost('mobile_no'),
                'payment_type' => $request->getPost('payment_type'),
                'transaction_id' => $request->getPost('transaction_id'),
            ];

            // print_r($data);
            if ($data['date'] == null || $data['seva_category_id'] == null || $data['seva_id'] == null || $data['user_id'] == null || $data['user_name'] == null || $data['seva_type'] == null || $data['time'] == null || $data['amount'] == null || $data['gotra'] == null || $data['pan_no'] == null || $data['nakshatra'] == null || $data['rashi'] == null || $data['no_of_persons'] == null || $data['email_id'] == null || $data['mobile_no'] == null || $data['payment_type'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }

            if ($sevaBookingModel->insert($data)) {
                $response = ['message' => 'Seva Booking Added Successfully'];
                //send Messgae
                $twilioService = new TwilioService();
                //send message - name purpose amount
                $message = "Dear " . $data['user_name'] . ",\nThank you for your Seva Booking of Rs." . $data['amount'] . " towards JGRST. \nRegards,\nJGRST";
                $twilioService->sendSMS($data['mobile_no'], $message);

                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'Seva Booking Addition Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }


    //API to send the message on specified mobile no
    public function send_sms()
    {
        try {
            $request = service('request');
            $twilioService = new TwilioService();
            $mobile_no = $request->getPost('mobile_no');
            $message = $request->getPost('message');
            // Validate Mobile No
            if ($mobile_no == null || $message == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            $twilioService->sendSMS($mobile_no, $message);
            $response = ['message' => 'Message Sent Successfully'];
            return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }





    //Update User API
    public function update_user()
    {
        try {
            $request = service('request');
            $userModel = new Users();
            $user_id = $request->getPost('user_id');
            $data = [
                'name' => $request->getPost('name'),
                'devotee_group' => $request->getPost('devotee_group'),
                'email' => $request->getPost('email'),
                'mobile_no' => $request->getPost('mobile_no'),
                'password' => $request->getPost('password'),
                'gotra' => $request->getPost('gotra'),
                'rashi' => $request->getPost('rashi'),
                'nakshatra' => $request->getPost('nakshatra'),
            ];

            //just update those fields which are sent


            // print_r($data);
            if ($data['name'] == null || $data['devotee_group'] == null || $data['email'] == null || $data['mobile_no'] == null || $data['password'] == null || $data['gotra'] == null || $data['rashi'] == null || $data['nakshatra'] == null) {
                $response = ['message' => 'Please fill all the fields'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
            if ($userModel->update($user_id, $data)) {
                $response = ['message' => 'User Updated Successfully'];
                return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
            } else {
                $response = ['message' => 'User Updation Failed'];
                return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
            }
        } catch (\Exception $e) {
            $response = ['message' => 'Something went wrong.'];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }


    public function get_blocked_dates()
    {
        try {
            //Send data
            $datesModel =  new CalenderBlock();

            $dates = $datesModel->findAll();

            $response = [
                'status' => 'success',
                'message' => 'Dates Fetched Successfully',
                'data' => $dates
            ];

            return $this->response->setContentType('application/json')->setStatusCode(200)->setJSON($response);
        } catch (\Exception $e) {
            $response = [
                'message' => 'Something went wrong.',
                'status' => 'error',
            ];
            return $this->response->setContentType('application/json')->setStatusCode(400)->setJSON($response);
        }
    }
}
