<?php 
define('DB_HOST'    , 'localhost'); 
define('DB_USERNAME', 'udara'); 
define('DB_PASSWORD', '@UdaraUnis2024'); 
define('DB_NAME'    , 'udara');

define('POST_DATA_URL', '/esp32/datasensor/sensordata.php');

//PROJECT_API_KEY is the exact duplicate of, PROJECT_API_KEY in NodeMCU sketch file
//Both values must be same
define('PROJECT_API_KEY', 'vierlee');


// Connect with the database 
$db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME); 
 
// Display error if failed to connect 
if ($db->connect_errno) { 
    echo "Connection to database is failed: ".$db->connect_error;
    exit();
}