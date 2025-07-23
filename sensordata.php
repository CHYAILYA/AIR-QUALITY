<?php 
require 'config.php';

//----------------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape semua input
    $mq7 = escape_data($_POST["mq7"]);
    $mq135 = escape_data($_POST["mq135"]);
    $sharp = escape_data($_POST["sharp"]);
    $mq131 = escape_data($_POST["mq131"]);
    
    // Query SQL
    $sql = "INSERT INTO data_udara(mq7, mq135, sharp, mq131) 
            VALUES('".$mq7."','".$mq135."','".$mq131."','".$sharp."')";
    
    // Eksekusi query
    if($db->query($sql) === FALSE) {
        echo "Error: " . $sql . "<br>" . $db->error;
    }
    
    echo "OK. INSERT ID: " . $db->insert_id;
}
//----------------------------------------------------------------------------
else {
    echo "No HTTP POST request found";
}
//----------------------------------------------------------------------------

function escape_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}