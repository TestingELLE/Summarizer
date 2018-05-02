<?php
include('login.php');

// if already logged in, user is redirected to page1.php page instead
if (isset($_SESSION['login_user'])) {
    header("location: page1.php");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Admin Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="http://code.jquery.com/jquery-2.1.3.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../it/navigation.css">

        <style>
            input {
                padding: 3px 3px 3px 3px;
                background-color: white;
                width: 100%;
            }

            #login {
                width: 40%;
                padding: 10px;
                margin: 0 auto;
            }
        </style>

        <!--All page specific code goes above this line which loads the common head-->
        <script src="../js/load-head.js"></script>
    </head>

    <body>
        <div id="supra-header"></div>
        <div id="page">
            <div id="header"> 
                <?php include('header.html'); ?> 
            </div>
            <div id="main-container" class="clear"> 
                <div id="tableDiv" class="notranslate" style="height:330px;">

                    <div id="main">
                        <br>
                        <br>
                        <div id="login"> 
                            <fieldset>
                                <h2 align="center">ADMIN LOGIN</h2>
                                <form method="post" action=""> 
                                    <br>
                                    USERNAME <br>
                                    <input id="name" name="username" placeholder="username" 
                                           type="text">
                                    <br>
                                    <br>
                                    PASSWORD <br>
                                    <input id="password" name="password" placeholder="**********" 
                                           type="password">
                                    <br> 
                                    <br>
                                    <br>
                                    <input name="submit" type="submit" value=" Login " 
                                           style="float:right; width:50%; background-color: #B09B4C;">
                                    <span style="font-size: x-small; color:red;"><?php echo $error; ?></span>
                                </form> 
                            </fieldset> 
                        </div> 
                        <br>
                        <br>
                        <br>
                    </div>


                    <nav id="navigation"> 
                        <?php include('navigation.html'); ?> 
                    </nav>
                    <footer id="colophon" class="clearfix notranslate"> 
                        <?php include('../commonHTML/footer.html'); ?> 
                    </footer>
                </div>
            </div>
    </body>
</html>