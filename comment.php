<?php
    session_start();
    if(!isset($_SESSION['loggedin'])){
       
    header("Location:logout.php");
    exit();
    }
  
    
    $symbol=$_POST['symbol'];
    $user=$_POST['user'];
    echo $newComment;
    $con=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
    if (!$con)
    {
    die('Could not connect: ' . mysqli_error());
    }
    $newComment=mysqli_real_escape_string($con,$_POST['newComment']);
    mysqli_select_db($con,"pupone_Summarizer");
    $sql="UPDATE main_table SET  skype_comments='$newComment' WHERE symbol='$symbol'";
    if(mysqli_query($con,$sql)){
    } else {
        echo "Error: ". mysqli_error($con);
    };
    $currentuser=$_SESSION['loggedin'];
    $userAction='added comments';
    $log="INSERT INTO activity (user, `action`,`page`) VALUES ('$currentuser','$userAction','$symbol')";
    mysqli_query($con,$log);
    mysqli_close($con);

?>