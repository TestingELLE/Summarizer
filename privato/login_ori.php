<?php

session_start();    // Starting Session
$error = '';        // Variable To Store Error Message

if (isset($_POST['submit'])) {

    // if user enters empty username or empty password, an error message is sent
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error = "Username or Password is invalid";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Establishing Connection by passing server_name, user_id and password
        $connection = mysql_connect("echidna.arvixe.com:3306", "pupone_DVuser", "DVuserpassword") or die(mysql_error());
        ;

        // To protect MySQL injection for Security purpose
        $username = stripslashes($username);
        $password = stripslashes($password);
        $username = mysql_real_escape_string($username);
        $password = mysql_real_escape_string($password);

        // Selecting Database
        $db = mysql_select_db("pupone_dovislex", $connection);

        // SQL query to fetch information of registerd users
        $query = mysql_query("select * from Members where password='$password' AND userName='$username'", $connection);
        $rows = mysql_num_rows($query);

        //if the user is found, redirects page to page1.php
        if ($rows == 1) {
            $_SESSION['login_user'] = $username; // Initializing Session
            header("location: page1.php");
        } else {
            $error = "Username or Password is invalid";
        }
        mysql_close($connection); // Closing Connection
    }
}