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
    
    //TO DO"  if user is Admin, Maintainer or developer show the buttons
    //if user = viewer hide the buttons -- this functionality should be in loader.php page
    
    // set table variables based on user type
    if($_SESSION['type']=="Programmer") {$main_table ="test_main_table";}
    if($_SESSION['type']=="Admin" || $_SESSION['type']=="Maintainer") {$main_table ="main_table";}
    //echo "$main_table  ";
    
    //common to all operations
   
    
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
    
    
    
    //load csv file and update data 
    if(isset($_POST["update"])){
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            
            //TO DO: check that is .csv file and skip with error message if not  
            if($filename[1]=="csv"){
                $fp=fopen($_FILES["file"]["tmp_name"],"r");   
                
           /*  replaced below 7/8/2018 L
            //chek that the columns are those expected. If not, abort.
                 $header=fgetcsv($handle);
                $colNotexists=implode(",",array_diff($header, $headerName));
                if(count(array_diff($header, $headerName))>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                $colExists=implode(",",array_intersect($header,$headerName));
              */  
              
                /* This will drop the update table
                  by L 2018-07-08 */
                $sqlDrop5="DROP TABLE IF EXISTS temp_update_table;";
                mysqli_query($connect,$sqlDrop5);

                //load the csv
                // http://hawkee.com/snippet/8320/ CSV to mySQL - Create Table and Insert Data
                // Get the first row to create the column headings
                $frow = fgetcsv($fp);
                foreach($frow as $column) {
                    if($columns) $columns .= ', ';
                    $columns .= "`$column` varchar(60)";
                    }
                    
                    $create = "create table if not exists temp_update_table ($columns);";
                    //echo $create."\n";
                    mysqli_query($connect,$create) or die(mysqli_error($connect));
                    
                    // Import the data into the newly created table.
                    $file = $_SERVER['PWD'].'/'.$file;
                    
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE temp_update_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                
                
                /*  NOT WORKING -- TO FIX - CUT OUT FOR NOW
                //replaces the above
                //check that the columns are those expected. If not, abort.
                 $checkColumns = "Call checkIfColumnsofA_areinB ('temp_update_table', '$main_table', @a);";
                // echo $checkColumns;
 
                mysqli_query($connect, $checkColumns);
                $checkColumnsCount = mysqli_query($connect, "select @a");
                //$checkColumnsCount = $res->fetch_assoc();
                // echo $checkColumnsCount;
                 if($checkColumnsCount>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
               mysqli_free_result($res);
               */
               
                 /* This will delete any NULL rows after the file has been created and inserted into update_table.
                 which will prevent the issue of empty rows before inserting into main_table. 
                 by Tom Tran 2018-5-24 */
                
                $removeEmptyRow2="DELETE FROM temp_update_table WHERE symbol='' or symbol IS NULL"; 
                mysqli_query($connect,$removeEmptyRow2);
                
                //check if there are duplicate symbols in the update file
                // TO DO. If there are, abort the whole thing and alert user.
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM temp_update_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($row = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                }
                
                /*
                // Prior to any actual update (total or partial), this will create an actual backup table.
                // TO do . If so, proceed, else abort with clear error message
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
                
                 
                
                 */
                // sets aside symbols that do not exist in $main_table
                // update the other symbols and alert user so he can append or take other corrective action
                $selectSymbolNotExists="SELECT symbol FROM temp_update_table where symbol NOT IN (SELECT DISTINCT symbol from $main_table);";
                $symbolNotExists=mysqli_query($connect,$selectSymbolNotExists);
                while($row = mysqli_fetch_assoc($symbolNotExists)){
                    $_SESSION["symbolNotExists"]=$row["symbol"]." ".$_SESSION["symbolNotExists"];
                }
                
                /*
             
                  $set=array();
                for($z=0;$z<count($header);$z++){
                    $set[$z]="$main_table.".$header[$z]."="."temp_update_table.".$header[$z];
                };
                */
                
                //prepare for actual update
                //this puts Null for all blank values
                $tablevar = "temp_update_table";
                $updateTable1 = "call blanks2nulls ('temp_update_table');";
                //echo $updateTable1;
                mysqli_query($connect,$updateTable1) or die(mysqli_error($connect));
                       
                // this updates all values with the new values if they are not NULL
                $updateTable2 = "call copyValuesFromA2B ('temp_update_table', '$main_table');";
                //echo $updateTable2;
                mysqli_query($connect,$updateTable2) or die(mysqli_error($connect));
               
                
                //$_SESSION["table"]="$main_table";
                $userAction="update $main_table";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                fclose($handle);
                header("Location:loader.php");
                
                
            }
        }
    }
    
   
   
?>