<?php
//runs session.php to make sure that user has access
include('session.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Admin Page</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="http://code.jquery.com/jquery-2.1.3.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../it/navigation.css">

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
                    <div>
                        <h1>Page 2</h1> 
                        <br>
                        <p>welcome <b><i><?php echo $login_session; ?></i></b></p>
                        <br>
                        <br>
                        restricted page: <a href="page1.php">Page 1</a>
                        <br>
                        <br>
                        <b><a href="logout.php">Log Out</a></b>
                    </div>
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