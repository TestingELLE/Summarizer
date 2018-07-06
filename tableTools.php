<?php
    session_start();
     if(!isset($_SESSION['uname'])){
      header("Location:logout.php");
      exit();
    }
    
    // behavior dependent on user type
    if($_SESSION['type']=="viewer"){
        echo "
        <script>
            alert('You do not have privilege to access the page. Please contact website manager.');
            window.location.href='Summarizer.php';
        </script>"; 
    }
    
    //if user is Admin, Maintainer or developer show the buttons
    //if user = viewer hide the buttons
    
    
    // set table variables based on user type
    if($_SESSION['type']=="Programmer") {$main_table ="test_main_table";}
    if($_SESSION['type']=="Admin" || $_SESSION['type']=="Maintainer") {$main_table ="main_table";}
    
    //common to all operations
    $headerName=["symbol", "industry", "sub_industry", "market_cap", "current_price", "pharma", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "variationD"]; 
    
      /*
             // get col names directly from main_table
                $colNames = "select COLUMN_NAME from information_schema.COLUMNS
                            WHERE
                            TABLE_NAME = $main_table AND
                            TABLE_SCHEMA = 'pupone_Summarizer';";
                mysqli_query($connect,$colNames);
               
                while($row = $colNames->fetch_assoc()){
                $headerName[] = $row['Field'];
} 
                 * */
    
    //operations
    //truncate and load new csv file -- NOT FINISHED
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
                $createTB="CREATE TABLE `$tableName` SELECT * FROM $main_table";
                mysqli_query($connect,$createTB);                                  // Moved up to actually create a backup table - Tom - 2018-06-19
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);
                
                
                //$sql2="TRUNCATE TABLE main_table";
                //mysqli_query($connect,$sql2);
               
               
                $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($header, $headerName));
                if(count(array_diff($header, $headerName))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                $colExists=implode(",",array_intersect($header,$headerName));
                // $_SESSION["colNotExists"]=$colExists;
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE $main_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM $main_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($row = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                }
                $_SESSION["table"]="$main_table";
                $removeEmptyRow="DELETE FROM $main_table WHERE symbol='' or symbol is null"; 
                mysqli_query($connect,$removeEmptyRow);
                $userAction="update $main_table";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                fclose($handle);               
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    }
     
    
    
    
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
                $createTB="CREATE TABLE `$tableName` SELECT * FROM $main_table";
                mysqli_query($connect,$createTB);   
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);
                //
                
         
                 
               $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($header, $headerName));
                if(count(array_diff($header, $headerName))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                $colExists=implode(",",array_intersect($header,$headerName));
                //
               
             
                /* This will truncate the update table before loading the information into main_table
                  by Tom Tran 2018-05-25 */
                $sqlTruncate4="TRUNCATE TABLE temp_update_table;";
                mysqli_query($connect,$sqlTruncate4);
                // replaced by Drop if Exist below. L 2018-07-03
                //$sqlDrop4="IF OBJECT_ID('temp_update_table', 'U') IS NOT NULL DROP TABLE temp_update_table; ";
                //mysqli_query($connect,$sqlDrop4);
                //$createTable="CREATE TABLE temp_update_table AS SELECT $colExists FROM $main_table WHERE 1=0";
                //mysqli_query($connect,$createTable);
                
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE temp_update_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                
                 /* This will delete any NULL rows after the file has been created and inserted into update_table.
                 which will prevent the issue of empty rows before inserting into main_table. 
                 by Tom Tran 2018-5-24 */
                
                $removeEmptyRow2="DELETE FROM temp_update_table WHERE symbol='' or symbol IS NULL"; 
                mysqli_query($connect,$removeEmptyRow2);
                
                
                $selectSymbolNotExists="SELECT symbol FROM temp_update_table where symbol NOT IN (SELECT DISTINCT symbol from $main_table);";
                $symbolNotExists=mysqli_query($connect,$selectSymbolNotExists);
                while($row = mysqli_fetch_assoc($symbolNotExists)){
                    $_SESSION["symbolNotExists"]=$row["symbol"]." ".$_SESSION["symbolNotExists"];
                };
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM temp_update_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($row = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                };
                /*
             
                  $set=array();
                for($z=0;$z<count($header);$z++){
                    $set[$z]="$main_table.".$header[$z]."="."temp_update_table.".$header[$z];
                };
                */
                
                //this puts Null for all blank values
                //$tablevar = "temp_update_table";
                $updateTable1 = "call processallcolumns ('temp_update_table');";
                //echo $updateTable1;
                mysqli_query($connect,$updateTable1) or die(mysqli_error($connect));
                
                
                // this updates all values with the new values if they are not NULL
                $updateTable2 = "call copyValuesFromA2B ('temp_update_table', '$main_table');";
                mysqli_query($connect,$updateTable2) or die(mysqli_error($connect));
                //echo $updateTable2;
                
                $_SESSION["table"]="$main_table";
                $userAction="update $main_table";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                fclose($handle);
                header("Location:loader.php");
            }
        }
    }
    
    
    //append new symbols from  csv file
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
                $createTB="CREATE TABLE `$tableName` SELECT * FROM $main_table";
                mysqli_query($connect,$createTB);      // Changed the order so that now it actually creates a backup.  - Tom 2018-06-02 
                $backTbquery="INSERT INTO backup_table (user,`filename`, `date`) VALUES('$user','$tableName','$date')";
                mysqli_query($connect,$backTbquery);
                
      
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
                
                /* not needed if table exists
                $createTable="CREATE TABLE temp_append_table AS SELECT $colExists FROM $main_table WHERE 1=0";
                mysqli_query($connect,$createTable);
                 */
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE temp_append_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES;";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                
                /* This will delete any NULL rows after the file has been created and inserted into temp_append_table.
                 which will prevent the issue of empty rows before inserting into temp_main_table.
                 by Tom Tran 2018-05-24 */
                
                $removeEmptyRow="DELETE FROM temp_append_table WHERE symbol='' or symbol IS NULL"; 
                mysqli_query($connect,$removeEmptyRow);
                
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM temp_append_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($result = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$result["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                $selectSymbolExists="SELECT symbol from temp_append_table where symbol in (SELECT DISTINCT symbol from $main_table);";
                $symbolExists=mysqli_query($connect,$selectSymbolExists);
                while($row = mysqli_fetch_assoc($symbolExists)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                // This will take the data that is found in the temp_append_table and INSERT it into the $main_table.
                $insertQuery="INSERT INTO $main_table SELECT * FROM temp_append_table;";  // Shortened this line for quicker insert. 2018-05-30
                mysqli_query($connect,$insertQuery);
                fclose($handle);
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    }
    
    //download current table
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
        $query = "SELECT * FROM $main_table";
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
        header('Content-Disposition: attachment; filename=$main_table_'.$date.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array("symbol", "industry", "sub_industry", "market_cap", "current_price", "pharma", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL","variationD"));
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
        }  
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
        fputcsv($output, array("symbol", "industry","sub_industry", "market_cap", "current_price", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside","2nd_price_target","2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL"));
       if (count($users) > 0) {
            foreach ($users as $row) {
                if(!empty($row)){
                    fputcsv($output, $row);
                }
            }
        }
    // NOT SURE WHAT THIS IS
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
                }
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
    }
     }

?>