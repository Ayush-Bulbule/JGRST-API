<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');


//Authentication Routes
$routes->get('/auth', 'Authentication::index');
$routes->post('/login', 'Authentication::login');
$routes->post('/signup', 'Authentication::signup');
$routes->post('/verify-otp', 'Authentication::verify_otp');
$routes->post('/resend-otp', 'Authentication::resend_otp');
$routes->post('/sso-login', 'Authentication::sso_login');
$routes->post('/verify-sso', 'Authentication::verify_sso');
$routes->post('/forgot-password', 'Authentication::sso_login'); //send otp
$routes->post('/verify-forgot', 'Authentication::verify_forgot'); //verify otp and reset password
$routes->post('/reset-password', 'Authentication::update_password'); //reset password
//Home Routes All Get Data
$routes->get('/home', 'HomeController::index');
$routes->get('/get-subhashitas', 'HomeController::get_recent_subhashitas');
$routes->get('/get-gallery', 'HomeController::get_gallary');
$routes->get('/get-events', 'HomeController::get_events');
$routes->get('/get-notifications', 'HomeController::get_notifications');
$routes->get('/get-sevalist', 'HomeController::get_sevalist');

//Category - a. Get All Categories
$routes->get('/api/categories', 'HomeController::get_all_categories');
//Category - b. Get Lists in a Category
$routes->get('/api/categories/(:num)/lists', 'HomeController::get_list_in_category/$1');
// c. Create a New List in a Category
$routes->post('/api/categories/(:num)/lists', 'HomeController::create_list_in_category/$1');
// d. Update an Existing List
$routes->post('/api/categories/(:num)/lists/(:num)', 'HomeController::update_list_in_category/$1/$2');
//e. Delete a List
$routes->delete('/api/categories/(:num)/lists/(:num)', 'HomeController::delete_list_in_category/$1/$2');
//get booking by user id
$routes->get('/api/bookings/(:num)', 'HomeController::get_booking_by_user_id/$1');

//Feedback
$routes->post('/api/feedbacks', 'HomeController::create_feedback');


//Cart 
$routes->post('/api/cart/add', 'HomeController::add_to_cart');
$routes->post('/api/cart/update', 'HomeController::update_cart_item_quantity');
$routes->post('/api/cart/remove', 'HomeController::remove_from_cart');
$routes->get("/api/cart/(:num)", 'HomeController::get_cart_by_user_id/$1');
// Donation
$routes->get('/api/donations', 'HomeController::get_donations');
//donation
$routes->post('/api/add-donation', 'HomeController::add_donation');

// Add Seva Booking
$routes->post('/api/add-seva-booking', 'HomeController::add_seva_booking');

//update user
$routes->post('/api/update-user', 'HomeController::update_user');

// Blocked Dates 
$routes->get('/api/blocked-dates', 'HomeController::get_blocked_dates');


// Send SMS
$routes->post('/api/send-sms', 'HomeController::send_sms');

//Handle Instamojo web-hook
// https://api.aishwaryasoftware.xyz/api/add-payment-instamojo/'
$routes->post('/api/add-payment-instamojo', 'HomeController::add_payment_instamojo');

//User details
$routes->get('/api/user/(:num)', 'HomeController::get_user_details/$1');



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
