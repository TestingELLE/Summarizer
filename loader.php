<?php
    ini_set('session.gc_maxlifetime',60*60*6);
    ini_set('session.gc_probability',1);
    ini_set('session.gc_divisor',1);
    session_start();
    //  echo $_SESSION['loggedin'];
    //  echo " ";
    //  echo $_SESSION["Last_Activity"];
    //  echo " ";
    //  echo time();
     if(!isset($_SESSION['loggedin'])){
       
      header("Location:logout.php");
      exit();
    }
    
    if($_SESSION['type']=="viewer"){
        
        echo "
        <script>
            alert('You do not have privilege to access the page. Please contact website manager.');
            window.location.href='Summarizer.php';
        </script>";
        
    }
  

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Document</title>
</head>
<body>
    <form name="form" action="export.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" accept=".csv">
        <input type="submit" name="submit" value="Truncate and load new file">
        <input type="submit" name="append" value="Append">
    </form>
    <form method="POST" action="export.php">
        <input type="submit" name="download" value="Download table as CSV file">
    </form>
    <hr>
    <button><a href="Summarizer.php">Main Page</a></button>
    <div></div>
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> 
 
</body>
</html>