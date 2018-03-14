<?php
    $newComment=$_POST['newComment'];
    $symbol=$_POST['symbol'];
    $user=$_POST['user'];
    echo $newComment;
    $con=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
    if (!$con)
    {
    die('Could not connect: ' . mysqli_error());
    }
    mysqli_select_db($con,"pupone_Summarizer");
    $sql="UPDATE main_table SET  skype_comments='$newComment' WHERE symbol='$symbol'";
    if(mysqli_query($con,$sql)){
    } else {
        echo "Error: ". mysqli_error($con);
    };

?>