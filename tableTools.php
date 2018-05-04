<?php
    session_start();
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
    //check user type
  if($_SESSION['type']=="Programmer"){
      //truncate and load new csv file
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
                $sql2="TRUNCATE TABLE main_table_testL";
                mysqli_query($connect,$sql2);
                $header=array();
                $newHeader=array();
                $headerName=["symbol", "industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worse_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update"];
                $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($header, $headerName));
                if(count(array_diff($header, $headerName))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    break;
                }
                $colExists=implode(",",array_intersect($header,$headerName));
                // $_SESSION["colNotExists"]=$colExists;
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE main_table_testL FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM main_table_testL GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($row = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                };
                $removeEmptyRow="DELETE FROM main_table_testL WHERE symbol='' or symbol is null"; 
                mysqli_query($connect,$removeEmptyRow);
                fclose($handle);               
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    };
    //load csv file and update data 
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
                $header=array();
                $newHeader=array();
                $headerName=["symbol", "industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worse_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update"];
                $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($header, $headerName));
                if(count(array_diff($header, $headerName))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    break;
                }
                $colExists=implode(",",array_intersect($header,$headerName));
                $dropTABLE="DROP TABLE IF EXISTS update_table_testL";
                mysqli_query($connect,$dropTABLE);
                $createTable="CREATE TABLE update_table_testL AS SELECT $colExists FROM main_table_testL WHERE 1=0";
                mysqli_query($connect,$createTable);
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE update_table_testL FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                $selectSymbolNotExists="SELECT symbol from update_table_testL where symbol not in (select distinct symbol from main_table_testL);";
                $symbolNotExists=mysqli_query($connect,$selectSymbolNotExists);
                while($row = mysqli_fetch_assoc($symbolNotExists)){
                    $_SESSION["symbolNotExists"]=$row["symbol"]." ".$_SESSION["symbolNotExists"];
                };
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM update_table_testL GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($row = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                };
                $set=array();
                for($z=0;$z<count($header);$z++){
                    $set[$z]="main_table_testL.".$header[$z]."="."update_table_testL.".$header[$z];
                }
                $condition=implode(",",$set);
                $updateTable="UPDATE main_table_testL, update_table_testL
                SET $condition
                WHERE main_table_testL.symbol = update_table_testL.symbol;";
                mysqli_query($connect,$updateTable);
                $_SESSION["table"]="main_table_testL";
                fclose($handle);
                header("Location:loader.php");
            }
        }
    }
    //load csv file and append new symbols
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
                        $sql="INSERT INTO main_table_testL (symbol, industry, market_cap, current_price, biotech, penny_stock, active, catalysts, last_earnings, next_earnings, bo_ah, intern, cash, burn, related_tickers, analysis_date, analysis_price, variation, 1st_price_target, 1st_upside,2nd_price_target,2nd_upside, downside_risk, rank, confidence, worse_case, target_weight, target_position, actual_position, actual_weight, weight_difference, strategy, discussion, notes, last_update)
                        VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]','$data[30]','$data[31]','$data[32]','$data[33]','$data[34]')";
                        mysqli_query($connect,$sql);
                    }
                }
                fclose($handle);
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    }
    //dowload current table
    if($_POST["download"]){
        $con=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
        if (!$con)
        {
        die('Could not connect: ' . mysqli_error());
        }
        $query = "SELECT * FROM main_table_testL";
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
        header('Content-Disposition: attachment; filename=main_table_testL_'.$date.'.csv');
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


     //download data
     if(isset($_GET["filename"])){
        $connect=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        $filename=$_GET["filename"];
        $queryTable = "SELECT * FROM $filename WHERE symbol!='symbol'";
        if (!$queryResult = mysqli_query($connect, $queryTable)) {
            exit(mysqli_error($connect));
        };    
        $users = array();
        if (mysqli_num_rows($queryResult) > 0) {
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $users[] = $row;
            }
        }
        date_default_timezone_set('America/Chicago');
        $date=date("Y-m-d h:i:sa",time());
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=backup_table-'.$date.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array("symbol", "industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation", "1st_price_target", "1st_upside","2nd_price_target","2nd_upside", "downside_risk", "rank", "confidence", "worse_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update"));
        if (count($users) > 0) {
            foreach ($users as $row) {
                if(!empty($row)){
                    fputcsv($output, $row);
                }
            }
        }
    }
  }else if($_SESSION['type']=="Admin" || $_SESSION['type']=="Maintainer"){
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
                $date = preg_replace('/\s+/', '_', $date);
                $date = str_replace("-","_",$date);
                $date = str_replace(":","_",$date);
                $tableName='BackUpTable_'.$date.'';
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
    //load a csv file and update current table
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
                $date = preg_replace('/\s+/', '_', $date);
                $date = str_replace("-","_",$date);
                $date = str_replace(":","_",$date);
                $tableName='BackUpTable_'.$date.'';
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
                        $_SESSION["table"]="main_table";
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
    //load a new csv file and append new symbols
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
                $date = preg_replace('/\s+/', '_', $date);
                $date = str_replace("-","_",$date);
                $date = str_replace(":","_",$date);
                $tableName='BackUpTable_'.$date.'';
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
    //download the current table
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
        header('Content-Disposition: attachment; filename=main_table_'.$date.'.csv');
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


     //download data
     if(isset($_GET["filename"])){
        $connect=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        $filename=$_GET["filename"];
        $currentuser=$_SESSION['loggedin'];
        $userAction="downloaded backup table";
        $log="INSERT INTO activity (user, `action`) VALUES ('$currentuser','$userAction')";
        mysqli_query($connect,$log);
        $queryTable = "SELECT * FROM $filename WHERE symbol!='symbol'";
        if (!$queryResult = mysqli_query($connect, $queryTable)) {
            exit(mysqli_error($connect));
        };    
        $users = array();
        if (mysqli_num_rows($queryResult) > 0) {
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $users[] = $row;
            }
        }
        date_default_timezone_set('America/Chicago');
        $date=date("Y-m-d h:i:sa",time());
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=backup_table-'.$date.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array("symbol", "industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation", "1st_price_target", "1st_upside","2nd_price_target","2nd_upside", "downside_risk", "rank", "confidence", "worse_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update"));
        if (count($users) > 0) {
            foreach ($users as $row) {
                if(!empty($row)){
                    fputcsv($output, $row);
                }
            }
        }
    }
};



?>




  