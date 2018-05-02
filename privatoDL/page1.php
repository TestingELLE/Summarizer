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
                restricted page: <a href="page2.php">Page 2</a>
                <br>
                <b><a href="logout.php">Log Out</a></b>
                <p>welcome <b><i><?php echo $login_session; ?></i></b></p>


                <div id="tableDiv" class="notranslate" style="height:330px;">

                    <header class="article-title"><h1>Numeri</h1></header>
                    <article>
                        <h3>Cellulari</h3>
                        <p>
                            P: 333 255-7367</br>
                            M: 339 26.12.49</br>
                            cellulare Gabriele	339 65.666.32</br>
                            cellulare Luca	366 416-8945
                        </p>



                        <h3>numeri belgi</h3>
                        <p>ufficio 32 3 502.13.42</p>


                        <h3>Simonetta</h3>
                        diretto ufficio	49 30 816.160.090
                        <p>Tel. diretto ufficio	49 30 757.74.521</br>
                            Tel. abitazione	49 30 325.62.01</br>
                            abitazione		49 30 816.13.111</br>
                            cellulare tedesco	49 177 277.30.85</br>
                            cellulare italiano	39 333.631.913</p>


                    </article>


                </div><!-- table-div-->


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