<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/analytics', 'Home::analytics');
$routes->get('/ref', 'Home::ref');
$routes->get('/getLatestData', 'Home::getLatestData');
$routes->get('/getChartData', 'Home::getChartData');
$routes->get('/settings', 'Home::settings');
// APPPATH/Config/Routes.php
$routes->get('/fetchAndSend', 'AuthController::fetchAndSend');

$routes->get('/', 'Home::index');
$routes->get('/analytics', 'Home::analytics');
$routes->get('/home/air', 'Home::air');
$routes->get('/ref', 'Home::ref');
$routes->match(['get', 'post'], '/sensordata', 'Home::storeSensorData');
// Auth Routes
$routes->get('/Login', 'AuthController::showLoginForm', ['as' => 'login']);
$routes->post('/Login', 'AuthController::login');
$routes->get('/login', 'AuthController::showLoginForm', ['as' => 'login']);
$routes->get('/logout', 'AuthController::logout');
$routes->get('/getRecommendation', 'Home::getRecommendation');
// Register Route
$routes->get('recommendation/get', 'Home::getRecommendation');
$routes->get('/register', 'AuthController::showRegisterForm', ['as' => 'register']);
$routes->post('/try', 'AuthController::air');
// Admin & Dashboard Routes
$routes->get('/admin', 'AdminController::index');
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/admin/dashboard', 'AuthController::dashboard');
$routes->get('/admin/dashboard/chat', 'AuthController::chatui');
$routes->get('/admin/dashboard', 'AuthController::dashboard');
$routes->post('/air', 'AuthController::air');
$routes->get('/history', 'AuthController::history');
$routes->post('/notifikasi', 'AuthController::Notifikasiwagrub');
$routes->get('/kirimDataair', 'AuthController::kirimDataair');
$routes->get('/sendWaterQualityUpdate', 'AuthController::sendWaterQualityUpdate');
     // Fetch notifications from the response table
// $routes->post('/air/try', 'AuthController::air');

