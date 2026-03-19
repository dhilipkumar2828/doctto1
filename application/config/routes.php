<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['mypdf'] = "welcome/mypdf";
$route['api/subscription-plans/doctor'] = 'api/Subscription_api/plan';
$route['api/subscription-plans/doctor/(:any)'] = 'api/Subscription_api/plan/$1';
$route['api/subscription-plans/customer'] = 'api/Subscription_api/customer_plan';
$route['api/subscription-plans/customer/(:any)'] = 'api/Subscription_api/customer_plan/$1';
$route['api/subscription-plans/subscribe-to-doctor'] = 'api/Subscription_api/subscribe_to_doctor';
$route['api/subscription-plans/my-subscription'] = 'api/Subscription_api/my_subscription';
$route['api/subscription-plans/history'] = 'api/Subscription_api/subscription_history';
$route['api/subscription-plans/terms'] = 'api/Subscription_api/subscription_terms';


$route['web/store_wise_categories/(:num)'] = 'web/store_wise_categories';
$route['web/store_categories/(:num)'] = 'web/store_categories';
$route['web/viewallProducts/trending/(:num)'] = 'web/viewallProducts/trending';
$route['web/viewallProducts/topdeals/(:num)'] = 'web/viewallProducts/topdeals';
$route['web/viewallshops/(:num)'] = 'web/viewallshops';
$route['admin/inactive_products/(:num)'] = 'admin/inactive_products/index';
$route['admin/inactive_products/searchProducts/(:num)'] = 'admin/inactive_products/searchProducts';
$route['admin/products/(:num)'] = 'admin/products/index';
$route['admin/products/searchProducts/(:num)'] = 'admin/products/searchProducts';
$route['default_controller'] = 'home';
$route['privacy'] = 'home/privacy';
$route['terms'] = 'home/terms';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

