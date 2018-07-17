<?php
session_start();

  // set table variables based on user type
// TO DO: if code is now split across several pages, $main_table should be a session variable set once in tabletools
    if($_SESSION['type']=="Programmer") {$main_table ="test_main_table";}
    if($_SESSION['type']=="Admin" || $_SESSION['type']=="Maintainer") {$main_table ="main_table";}
    //echo "$main_table  ";
    
    
    echo 'append.php page';
    echo $_POST["append"];
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
                
                // TO DO this backup should happen only just before the operation takes place
                // IN FACT - given that it is an append, it may not be necessary at all
                // will remove it for now.
                // This will create an actual backup table.
                /*
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
                */
                
                
                
      //load the csv
      //
     
                // http://hawkee.com/snippet/8320/ CSV to mySQL - Create Table and Insert Data
                // Get the first row to create the column headings
                $frow = fgetcsv($fp);
                foreach($frow as $column) {
                    if($columns) $columns .= ', ';
                    $columns .= "`$column` varchar(60)";
                    }
                    
                    
                    ///* This will drop the append  table by L 2018-07-08 */
                $sqlDrop5="DROP TABLE IF EXISTS temp_update_table;";
                mysqli_query($connect,$sqlDrop5);
                
                ///* create the append  table */
                $create = "create table if not exists temp_append_table ($columns);";
                //echo $create."\n";
                mysqli_query($connect,$create) or die(mysqli_error($connect));
                 
                 // check that the column names are right
                    // all columns of append table should exist in $main_table
                    // and ideally vice-versa
                 //$tablevar = "temp_update_table";
                $appendTable = "call checkIfColumnsofA_areinB ('temp_append_table', '$main_table');";
                //echo $updateTable1;
                mysqli_query($connect,$appendTable) or die(mysqli_error($connect));
                
                
                //load the table
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE temp_append_table FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n\r' IGNORE 1 LINES;";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                
                /* This will delete any NULL rows after the file has been created and inserted into temp_append_table. which will prevent the issue of empty rows before inserting into temp_main_table.
                 by Tom Tran 2018-05-24 */
                
                $removeEmptyRow="DELETE FROM temp_append_table WHERE symbol='' or symbol IS NULL"; 
                mysqli_query($connect,$removeEmptyRow);
                
                
                // delete duplicate symbols in table
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM temp_append_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
                while($result = mysqli_fetch_assoc($duplicatedSymbol)){
                    $_SESSION["duplicates"]=$result["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                
                // check that the symbol does not already exist in $main_table
                $selectSymbolExists="SELECT symbol from temp_append_table where symbol in (SELECT DISTINCT symbol from $main_table);";
                $symbolExists=mysqli_query($connect,$selectSymbolExists);
                while($row = mysqli_fetch_assoc($symbolExists)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                // This will take the data that is found in the temp_append_table and INSERT it into the $main_table.
                $insertQuery="INSERT INTO $main_table SELECT * FROM temp_append_table;";  // Shortened this line for quicker insert. 2018-05-30
                echo "$insertQuery";
                mysqli_query($connect,$insertQuery);
                fclose($handle);
                mysqli_close($connect);
                header("Location:loader.php");
            }
        }
    }
    
?>