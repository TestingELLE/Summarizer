<?php
    session_start();
     if(!isset($_SESSION['uname'])){
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
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");
                $sql2="TRUNCATE TABLE test_main_table";
                mysqli_query($connect,$sql2);
                $header=array();
                $newHeader=array();
                $headerName=["symbol", "industry", "sub_industry", "market_cap", "current_price", "pharma", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "variationD"];
                $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($header, $headerName));
                
                if(count(array_diff($header, $headerName))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                
                $colExists=implode(",",array_intersect($header,$headerName));
                // $_SESSION["colNotExists"]=$colExists;
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE test_main_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES;";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM test_main_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($row = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                };
                $_SESSION["table"]="test_main_table";
                $removeEmptyRow="DELETE FROM test_main_table WHERE symbol='' or symbol IS NULL"; 
                
                mysqli_query($connect,$removeEmptyRow);
                fclose($handle);               
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    };
    //load csv file and update data 
    if(isset($_POST["update"])){
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");   
                
                // This will create an actual backup table.
                $user=$_SESSION['uname'];
                $date=date("Y-m-d h:i:sa",time());
                $date = preg_replace('/\s+/', '_', $date);
                $date = str_replace("-","_",$date);
                $date = str_replace(":","_",$date);
                $tableName='BackUpTable_'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM test_main_table";
                mysqli_query($connect,$createTB);   
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);
                
                $header=array();
                $newHeader=array();
                $headerName=["symbol", "industry", "sub_industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "id"];                
                $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($header, $headerName));
                if(count(array_diff($header, $headerName))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                $colExists=implode(",",array_intersect($header,$headerName));
                
                  /* This will truncate the test update table before loading the information into main_table
                  by Tom Tran 2018-05-26 */
                $sqlTruncate5="TRUNCATE TABLE test_update_table;";
                mysqli_query($connect,$sqlTruncate5);
                
                  /* This will delete any NULL rows after the file has been created and inserted into test_update_table.
                 which will prevent the issue of empty rows before inserting into main_table. 
                 by Tom Tran 2018-5-26 */
                
                $removeEmptyRow2="DELETE FROM test_update_table WHERE symbol='' or symbol IS NULL"; 
                mysqli_query($connect,$removeEmptyRow2);
               
                
                /*$createTable="CREATE TABLE test_update_table AS SELECT $colExists FROM test_main_table WHERE 1=0";
                mysqli_query($connect,$createTable);*/
                
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE test_update_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES;";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                
                $selectSymbolNotExists="SELECT symbol from test_update_table where symbol not in (select distinct symbol from test_main_table);";
                $symbolNotExists=mysqli_query($connect,$selectSymbolNotExists);
                
                while($row = mysqli_fetch_assoc($symbolNotExists)){
                    $_SESSION["symbolNotExists"]=$row["symbol"]." ".$_SESSION["symbolNotExists"];
                };
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM test_update_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                
                while($row = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                };
                $set=array();
                for($z=0;$z<count($header);$z++){
                    $set[$z]="test_main_table.".$header[$z]."="."test_update_table.".$header[$z];
                }
                $condition=implode(",",$set);
                $updateTable="UPDATE test_main_table, test_update_table
                SET $condition
                WHERE test_main_table.symbol = test_update_table.symbol;";
                mysqli_query($connect,$updateTable);       
                $_SESSION["table"]="test_main_table";
                fclose($handle);
                header("Location:loader.php");
            }
        }
    }
    //load csv file and append new symbols
    if(isset($_POST["append"])){
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");
                
                // This will create an actual backup table.
                $user=$_SESSION['uname'];
                $date=date("Y-m-d h:i:sa",time());
                $date = preg_replace('/\s+/', '_', $date);
                $date = str_replace("-","_",$date);
                $date = str_replace(":","_",$date);
                $tableName='BackUpTable_'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM test_main_table";
                mysqli_query($connect,$createTB);   
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);
                
                 /* This will truncate the append table test before loading the information into test_append_table
                     by Tom Tran 2018-05-24 */
                $sqlTruncate="TRUNCATE TABLE test_append_table;";
                mysqli_query($connect,$sqlTruncate);
                
                 /* This will truncate the test_main_table before loading the information into test_main_table
                    by Tom Tran 2018-05-24 */
                $sqlTruncate2="TRUNCATE TABLE test_main_table;";
                mysqli_query($connect,$sqlTruncate2);
                
                $header=array();
                $newHeader=array();
                $headerName=["symbol", "industry", "sub_industry", "market_cap", "current_price", "pharma", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "variationD"];                
                $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($headerName,$header));
                if(count(array_diff($headerName,$header))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                $colExists=implode(",",array_intersect($header,$headerName));
                
                $createTable="CREATE TABLE test_append_table AS SELECT $colExists FROM test_main_table WHERE 1=0";
                mysqli_query($connect,$createTable);
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE test_append_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES;";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                
            
                /* This will delete any NULL rows after the file has been created and inserted into test_append_table.
                 which will prevent the issue of empty rows before inserting into temp_main_table.
                 by Tom Tran 2018-05-24 */
                
                $removeEmptyRow="DELETE FROM test_append_table WHERE symbol='' or symbol IS NULL"; 
                mysqli_query($connect,$removeEmptyRow);
                
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM test_append_table GROUP BY symbol HAVING c > 1;";
                
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($result = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$result["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                $selectSymbolExists="SELECT symbol from test_append_table where symbol in (SELECT DISTINCT symbol from test_main_table);";
                $symbolExists=mysqli_query($connect,$selectSymbolExists);
                while($row = mysqli_fetch_assoc($symbolExists)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                // This will take the data that is found in the test_append_table and INSERT it into the test_main_table.
                $insertQuery="INSERT INTO test_main_table SELECT * FROM test_append_table;";  // Shortened this line for quicker insert. 2018-05-30
                mysqli_query($connect,$insertQuery);
                fclose($handle);
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    }
    //dowload current table
    if($_POST["download"]){
        $con=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$con)
        {
        die('Could not connect: ' . mysqli_error());
        }
        $query = "SELECT * FROM temp_main_table";
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
        header('Content-Disposition: attachment; filename=temp_main_table_'.$date.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array("symbol", "industry", "sub_industry", "market_cap", "current_price","pharma", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "variationD"));
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
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
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
        header('Content-Disposition: attachment; filename=BackUpTable_'.$date.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array("symbol", "industry", "sub_industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "id"));
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
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");
                $user=$_SESSION['uname'];
                date_default_timezone_set('America/Chicago');
                $date=date("Y-m-d h:i:sa",time());
                $date = preg_replace('/\s+/', '_', $date);
                $date = str_replace("-","_",$date);
                $date = str_replace(":","_",$date);
                $tableName='BackUpTable_'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM main_table";
                mysqli_query($connect,$createTB);                                  // Moved up to actually create a backup table - Tom - 2018-06-19
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);
                //$sql2="TRUNCATE TABLE main_table";
                //mysqli_query($connect,$sql2);
                $header=array();
                $newHeader=array();
                $headerName=["symbol", "industry", "sub_industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "id"];
                $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($header, $headerName));
                if(count(array_diff($header, $headerName))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                $colExists=implode(",",array_intersect($header,$headerName));
                // $_SESSION["colNotExists"]=$colExists;
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE main_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM main_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($row = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                };
                $_SESSION["table"]="main_table";
                $removeEmptyRow="DELETE FROM main_table WHERE symbol='' or symbol is null"; 
                mysqli_query($connect,$removeEmptyRow);
                $userAction="update main_table";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                fclose($handle);               
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    };
    //load a csv file and update current table
    if(isset($_POST["update"])){
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");
                $user=$_SESSION['uname'];
                date_default_timezone_set('America/Chicago');
                $date=date("Y-m-d h:i:sa",time());
                $date = preg_replace('/\s+/', '_', $date);
                $date = str_replace("-","_",$date);
                $date = str_replace(":","_",$date);
                $tableName='BackUpTable_'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM main_table";
                mysqli_query($connect,$createTB);             // Changed the order, now it will create an actual backup table. - Tom 2018-06-02
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);          
                $header=array();
                $newHeader=array();
                $headerName=["symbol", "industry", "sub_industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "id"];
                $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($header, $headerName));
                if(count(array_diff($header, $headerName))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                $colExists=implode(",",array_intersect($header,$headerName));
                /* This will truncate the update table before loading the information into main_table
                  by Tom Tran 2018-05-25 */
                $sqlTruncate4="TRUNCATE TABLE temp_update_table;";
                mysqli_query($connect,$sqlTruncate4);
                
                 /* This will delete any NULL rows after the file has been created and inserted into update_table.
                 which will prevent the issue of empty rows before inserting into main_table. 
                 by Tom Tran 2018-5-24 */
                
                $removeEmptyRow2="DELETE FROM temp_update_table WHERE symbol='' or symbol IS NULL"; 
                mysqli_query($connect,$removeEmptyRow2);
                
                $createTable="CREATE TABLE temp_update_table AS SELECT $colExists FROM main_table WHERE 1=0";
                mysqli_query($connect,$createTable);
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE temp_update_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                $selectSymbolNotExists="SELECT symbol FROM temp_update_table where symbol NOT IN (SELECT DISTINCT symbol from main_table);";
                $symbolNotExists=mysqli_query($connect,$selectSymbolNotExists);
                while($row = mysqli_fetch_assoc($symbolNotExists)){
                    $_SESSION["symbolNotExists"]=$row["symbol"]." ".$_SESSION["symbolNotExists"];
                };
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM temp_update_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($row = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                };
                $set=array();
                for($z=0;$z<count($header);$z++){
                    $set[$z]="main_table.".$header[$z]."="."temp_update_table.".$header[$z];
                }
                $condition=implode(",",$set);
                $updateTable="UPDATE main_table, temp_update_table
                SET $condition
                WHERE main_table.symbol = temp_update_table.symbol;";
                mysqli_query($connect,$updateTable);
                $_SESSION["table"]="main_table";
                $userAction="update main_table";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                fclose($handle);
                header("Location:loader.php");
            }
        }
    }
    //load a new csv file and append new symbols
    if(isset($_POST["append"])){
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            if($filename[1]=="csv"){
                $handle=fopen($_FILES["file"]["tmp_name"],"r");      
                $user=$_SESSION['uname'];
                date_default_timezone_set('America/Chicago');
                $date=date("Y-m-d h:i:sa",time());
                $date = preg_replace('/\s+/', '_', $date);
                $date = str_replace("-","_",$date);
                $date = str_replace(":","_",$date);
                $tableName='BackUpTable_'.$date.'';
                $createTB="CREATE TABLE `$tableName` SELECT * FROM main_table";
                mysqli_query($connect,$createTB);                   // Changed the order so that now it actually creates a backup.  - Tom 2018-06-02
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);     
                $header=array();
                $newHeader=array();
                $headerName=["symbol", "industry", "sub_industry", "market_cap", "current_price", "biotech", "penny_stock", "active", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "id"];
                $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($headerName,$header));
                if(count(array_diff($headerName,$header))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                $colExists=implode(",",array_intersect($header,$headerName));
                
                /* This will truncate the append table before loading the information into main_table
                  by Tom Tran 2018-05-24 */
                $sqlTruncate3="TRUNCATE TABLE temp_append_table;";
                mysqli_query($connect,$sqlTruncate3);
                
                
                $createTable="CREATE TABLE temp_append_table AS SELECT $colExists FROM main_table WHERE 1=0;";
                mysqli_query($connect,$createTable);          
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE temp_append_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                
                 /* This will delete any NULL rows after the file has been created and inserted into append_table.
                 which will prevent the issue of empty rows before inserting into main_table. 
                 by Tom Tran 2018-5-24 */
                
                $removeEmptyRow="DELETE FROM temp_append_table WHERE symbol='' or symbol IS NULL"; 
                mysqli_query($connect,$removeEmptyRow);
                
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM temp_append_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($result = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$result["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                $selectSymbolExists="SELECT symbol from temp_append_table where symbol in (select distinct symbol from main_table);";
                $symbolExists=mysqli_query($connect,$selectSymbolExists);
                while($row = mysqli_fetch_assoc($symbolExists)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                //$insertQuery="INSERT INTO main_table (symbol, industry, sub_industry, market_cap, current_price, biotech, penny_stock, status, catalysts, last_earnings, next_earnings, bo_ah, intern, cash, burn, related_tickers, analysis_date, analysis_price, variation1, 1st_price_target, 1st_upside, 2nd_price_target, 2nd_upside, downside_risk, rank, confidence, worst_case, target_weight, target_position, actual_position, actual_weight, weight_difference, strategy, discussion, notes, last_update,last_price, variationL, id)
                //SELECT * FROM temp_append_table;";           // WHERE append_table.symbol NOT IN (SELECT symbol from main_table)";
                
                $insertQuery = "INSERT INTO main_table SELECT * FROM temp_append_table;";  // Shortened this to work quicker - 2018-06-19 Tom
                mysqli_query($connect,$insertQuery);
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
        $con=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$con)
        {
        die('Could not connect: ' . mysqli_error());
        }
        $currentuser=$_SESSION['uname'];
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
        fputcsv($output, array("symbol", "industry","sub_industry", "market_cap", "current_price", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside","2nd_price_target","2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "id"));
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
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        $filename=$_GET["filename"];
        $currentuser=$_SESSION['uname'];
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
        header('Content-Disposition: attachment; filename=BackUpTable_'.$date.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array("symbol", "industry","sub_industry", "market_cap", "current_price", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside","2nd_price_target","2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "id"));
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