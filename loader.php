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
    $connect=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
    if (!$connect)
    {
    die('Could not connect: ' . mysqli_error());
    }
    if(isset($_POST["submit"])){
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");
                $row = 1;
                $sql2="TRUNCATE TABLE main_table";
                mysqli_query($connect,$sql2);
                while($data=fgetcsv($handle)){
                    if($row == 1){ $row++; continue; } 
                    $sql="INSERT INTO main_table (symbol, industry, mkt_cap, price, biotech, penny_stock, active, catalysts, last_earnings, next_earnings, bo_ah, intern, cash, burn, related_tickets, analysis_date, analysis_price, low_target, price_target, upside, down_risk, rank, confidence, worse_case, target_weight, target_position, actual_position, actual_weight, diff, stragety, questions, notes, skype_comments, last_updates)
                    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]','$data[30]','$data[31]','$data[32]','$data[33]')";
                    mysqli_query($connect,$sql);
                    $run=mysqli_query($connect,$sql);
                }
                fclose($handle);
                
                mysqli_close($connect);
                
            }
        }
    };

    if(isset($_POST["append"])){
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");
                $row = 1;
                mysqli_query($connect,$sql2);
                while($data=fgetcsv($handle)){
                    if($row == 1){ $row++; continue; } 
                    $sql="INSERT INTO main_table (symbol, industry, mkt_cap, price, biotech, penny_stock, active, catalysts, last_earnings, next_earnings, bo_ah, intern, cash, burn, related_tickets, analysis_date, analysis_price, low_target, price_target, upside, down_risk, rank, confidence, worse_case, target_weight, target_position, actual_position, actual_weight, diff, stragety, questions, notes, skype_comments, last_updates)
                    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]','$data[30]','$data[31]','$data[32]','$data[33]')";
                    mysqli_query($connect,$sql);
                    $run=mysqli_query($connect,$sql);
                }
                fclose($handle);
                
                mysqli_close($connect);
                
            }
        }
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
    <form name="form" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" accept=".csv">
        <input type="submit" name="submit" value="Truncate and load new file">
        <input type="submit" name="append" value="Append">
        
    </form>
    <hr>
    <button><a href="Summarizer.php">Main Page</a></button>
    <div></div>
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> 
 
</body>
</html>