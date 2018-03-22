<?php
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
  
    if(isset($_POST["submit"])){
        $connect=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");
                $date=date("Y-m-d h:i:sa",time());
                $tableName='BackUpTable-'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM main_table";
                mysqli_query($connect,$createTB);
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
                $user=$_SESSION['loggedin'];
                $userAction="uploaded a CSV file";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    };

    if(isset($_POST["append"])){
        $connect=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");
                $date=date("Y-m-d h:i:sa",time());
                $tableName='AppendTable-'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM main_table";
                mysqli_query($connect,$createTB);
                $row = 1;
                while($data=fgetcsv($handle)){
                    if($row == 1){ $row++; continue; } 
                    $sql="INSERT INTO main_table (symbol, industry, mkt_cap, price, biotech, penny_stock, active, catalysts, last_earnings, next_earnings, bo_ah, intern, cash, burn, related_tickets, analysis_date, analysis_price, low_target, price_target, upside, down_risk, rank, confidence, worse_case, target_weight, target_position, actual_position, actual_weight, diff, stragety, questions, notes, skype_comments, last_updates)
                    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]','$data[30]','$data[31]','$data[32]','$data[33]')";
                    mysqli_query($connect,$sql);
                    $run=mysqli_query($connect,$sql);
                }
                fclose($handle);
                $user=$_SESSION['loggedin'];
                $userAction="appended a CSV file";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    }
    if($_POST["download"]){
        
        $con=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
        if (!$con)
        {
        die('Could not connect: ' . mysqli_error());
        }
        $currentuser=$_SESSION['loggedin'];
        $userAction="downloaded current table";
        $log="INSERT INTO activity (user, `action`) VALUES ('$currentuser','$userAction')";
        mysqli_query($con,$log);
        $query = "SELECT * FROM main_table";
        if (!$result = mysqli_query($con, $query)) {
            exit(mysqli_error($con));
        };
        $users = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }
        }
        date_default_timezone_set('America/Chicago');
        $time=time();
        $date=date("Y-m-d h:i:sa",time());
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=main_table-'.$date.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array("symbol","industry","mkt_cap","price","biotech","penny_stock","active","catalysts","last_earnings","next_earnings","bo_ah","intern","cash","burn","related_tickets","analysis_date","analysis_price","low_target","price_target","upside","down_risk","rank","confidence","worse_case","target_weight","target_position","actual_position","actual_weight","diff","stragety","questions","notes","skype_comments","last_updates"));
 
        if (count($users) > 0) {
            foreach ($users as $row) {
                fputcsv($output, $row);
            }
        }
        
        mysqli_close($con);
    };



?>