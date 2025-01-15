<?php
session_start();

// Create a new MySQLi connection
$con=mysqli_connect('localhost','root','','expenses');

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>


