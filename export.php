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
                $user=$_SESSION['loggedin'];
                date_default_timezone_set('America/Chicago');
                $date=date("Y-m-d h:i:sa",time());
                $tableName='BackUpTable-'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM main_table";
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);
                mysqli_query($connect,$createTB);
                $row = 1;
                $rowCount=0;
                $sql2="TRUNCATE TABLE main_table";
                mysqli_query($connect,$sql2);
                while($data=fgetcsv($handle)){
                    $rowCount++;
                    $data[0]=preg_replace('/\s+/', '', $data[0]);
                    if($row == 1){ $row++; continue; }
                    for($x=0;$x<count($data);$x++){
                        $data[$x]=mysqli_real_escape_string($connect,$data[$x]);
                     }
                     if($data[0]==""){
                        $_SESSION["break"]=$rowCount;
                        break;
                    }else{
                    $sql="INSERT INTO main_table (symbol, industry, market_cap, current_price, biotech, penny_stock, active, catalysts, last_earnings, next_earnings, bo_ah, intern, cash, burn, related_tickers, analysis_date, analysis_price, variation, 1st_price_target, 1st_upside,2nd_price_target,2nd_upside, downside_risk, rank, confidence, worse_case, target_weight, target_position, actual_position, actual_weight, weight_difference, strategy, discussion, notes, last_update)
                    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]','$data[30]','$data[31]','$data[32]','$data[33]','$data[34]')";
                    mysqli_query($connect,$sql);
                    }
                }
                fclose($handle);               
                $userAction="uploaded a CSV file";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    };

    if(isset($_POST["update"])){
        $connect=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");
                $user=$_SESSION['loggedin'];
                date_default_timezone_set('America/Chicago');
                $date=date("Y-m-d h:i:sa",time());
                $tableName='UpdateTable-'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM main_table";
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);
                mysqli_query($connect,$createTB);
                $row = 1;
                $rowCount=0; 
                $header=NULl;
                $symbolNotExist="";
                $duplicates="";
                $symbolArray=array();
                while($data=fgetcsv($handle)){
                    $rowCount=$rowCount+1; 
                    $data[0]=preg_replace('/\s+/', '', $data[0]);
                    if(!$header){
                        $header=$data;
                        continue;
                    }
                    else{
                        $rowValue=array();  
                        $rowValue=array_combine($header, $data);
                        $newRow=array();
                        foreach( $rowValue as $key=>$val) {
                            $newRow[]="$key='$val'";
                        }
                    }
                    $result=mysqli_query($connect,"SELECT * FROM main_table WHERE symbol='".$data[0]."'");
                    $row=mysqli_num_rows($result);
                    if(in_array($data[0],$symbolArray)){
                        $duplicates=$data[0]." ".$duplicates;
                        $_SESSION["duplicates"]= $duplicates;
                        continue;
                    }
                    array_push($symbolArray,$data[0]);
                    if($data[0]!==""&&$row==0){
                        $symbolNotExist=$data[0]." ".$symbolNotExist;
                        $_SESSION["symbolNotExists"]=$symbolNotExist;
                        continue;
                    }
                    for($x=0;$x<count($data);$x++){
                        $data[$x]=mysqli_real_escape_string($connect,$data[$x]);
                     }
                     if($data[0]==""){
                        $_SESSION["break"]=$rowCount;
                        break;
                     }else{
                        $value="";
                        $value.=implode(",",$newRow);
                        $sql="UPDATE main_table SET $value WHERE symbol='".$data[0]."'";
                        mysqli_query($connect,$sql) or die(mysqli_error($connect));
                    }
                   
                }
                fclose($handle);
                $userAction="updated a CSV file";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    }
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
                $user=$_SESSION['loggedin'];
                date_default_timezone_set('America/Chicago');
                $date=date("Y-m-d h:i:sa",time());
                $tableName='AppendTable-'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM main_table";
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);
                mysqli_query($connect,$createTB);
                $row = 1;
                $rowCount=0;
                while($data=fgetcsv($handle)){
                    $rowCount++;
                    $data[0]=preg_replace('/\s+/', '', $data[0]);
                    if($row == 1){ $row++; continue; } 
                    for($x=0;$x<count($data);$x++){
                        $data[$x]=mysqli_real_escape_string($connect,$data[$x]);
                     }
                     if($data[0]==""){
                        $_SESSION["break"]=$rowCount;
                        break;
                    }else{
                        $sql="INSERT INTO main_table (symbol, industry, market_cap, current_price, biotech, penny_stock, active, catalysts, last_earnings, next_earnings, bo_ah, intern, cash, burn, related_tickers, analysis_date, analysis_price, variation, 1st_price_target, 1st_upside,2nd_price_target,2nd_upside, downside_risk, rank, confidence, worse_case, target_weight, target_position, actual_position, actual_weight, weight_difference, strategy, discussion, notes, last_update)
                        VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]','$data[30]','$data[31]','$data[32]','$data[33]','$data[34]')";
                        mysqli_query($connect,$sql);
                    }
                }
                fclose($handle);
                
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
        fputcsv($output, array("symbol", "industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation", "1st_price_target", "1st_upside","2nd_price_target","2nd_upside", "downside_risk", "rank", "confidence", "worse_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update"));

        if (count($users) > 0) {
            foreach ($users as $row) {
                if(!empty($row)){
                    fputcsv($output, $row);
                }
            }
        }
        
        mysqli_close($con);
    };



?>




  