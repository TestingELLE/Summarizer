<?php
 session_start();

     if(!isset($_SESSION['uname'])){
       
        header("Location:logout.php");
        exit();
    }
    //set up database connection
    $con=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
    if (!$con)
    {
    die('Could not connect: ' . mysqli_error());
    }
    $current_price=$_POST["latestPrice"];
    //removed column from table as it is calculated $first_upside=$_POST["1st_upside"];
    //removed column from table as it is calculated $second_upside=$_POST["2nd_upside"];
    //removed column from table as it is calculated $varL=$_POST["variationL"];
    $symbol=$_POST["symbol"];
     mysqli_select_db($con,"pupone_Summarizer");
     $qry="UPDATE main_table SET current_price='$current_price' WHERE symbol='$symbol'";
    mysqli_query($con,$qry);
 if(mysqli_query($con,$qry)){
        echo "Successfully updated";
        
    } else {
        echo "Error: ". mysqli_error($con);
    }; 
    ?>


