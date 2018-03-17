<?php

// Establishing Connection by passing server_name, user_id and password
$connection = mysql_connect("62.149.150.171", "Sql924951", "a438135r74");

// Selecting Database
$db = mysql_select_db("Sql924951_1", $connection);

// Starting Session
session_start();

// Storing Session
$user_check = $_SESSION['login_user'];

// SQL Query To Fetch Complete Information Of User
$ses_sql = mysql_query("select userName from Members where userName='$user_check'", $connection);
$row = mysql_fetch_assoc($ses_sql);
$login_session = $row['userName'];

if (!isset($login_session)) {
    mysql_close($connection); // Closing Connection
    header('Location: index.php'); // Redirecting To Home Page
}