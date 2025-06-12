<?php
ob_start();

$server = "localhost";
$username = "root";
$password = "";
$database = "chat_app";

$conn = new mysqli($server, $username, $password, $database);

if($conn->connect_error){
    die ("Connectio failed: " . $conn->connect_error);
}else{
    // echo("Connection Succesfull");
}
?>