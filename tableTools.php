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
            alert('You do not have the privileges to access the page. Please contact website manager.');
            window.location.href='Summarizer.php';
        </script>"; 
    }
    
    
    // set table variables based on user type
    if($_SESSION['type']=="Programmer") {$main_table ="test_main_table";}
    if($_SESSION['type']=="Admin" || $_SESSION['type']=="Maintainer") {$main_table ="main_table";}
    
    //echo "$main_table  ";

 
    
    //which operation is selected
    if( array_key_exists( 'flexible_update', $_POST ) ) {$actionSelection = "flexible_update";}
    //if( array_key_exists( 'full_update', $_POST ) ) {$actionSelection = "full_update";}
    //for now
    if( array_key_exists( 'full_update', $_POST ) ) {$actionSelection = "flexible_update";}
    if( array_key_exists( 'append', $_POST ) ) {$actionSelection = "append";}
    
    //temp_update_table or temp_append_table 
    switch ($actionSelection) {
        case "flexible_update":
            $tempTable = "temp_update_table";
            break;
         case "append":
            $tempTable = "temp_append_table";
    } // switch
    
    

    //common to all operations - load and check temp_$tempTable
    // connection
     $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        { die('Could not connect: ' . mysqli_error());}           
                
      //load the csv
      //
        if($_FILES["file"]["name"]){
            $filename=explode(".",$_FILES["file"]["name"]);
            //echo $filename[1];
        }
        
         // http://hawkee.com/snippet/8320/ CSV to mySQL - Create Table and Insert Data -- at bottom
          // Get the first row to create the column headings
        if($filename[1]=="csv"){
            $fp=fopen($_FILES["file"]["tmp_name"],"r");
            $frow = fgetcsv($fp);
            foreach($frow as $column) {
                 if($columns) $columns .= ', ';
                 $columns .= "`$column` varchar(60)";
             }
        }          
                //This will drop the $tempTable by L 2018-07-08 */
                $sqlDrop5="DROP TABLE IF EXISTS $tempTable;";
                mysqli_query($connect,$sqlDrop5);
                
                ///* create the $tempTabletable */
                //echo $columns;
                $create = "create table if not exists $tempTable ($columns);";
                //echo $create."\n";
                mysqli_query($connect,$create) or die(mysqli_error($connect));
                 
                // check that the column names are right
                // all columns of append table should exist in $main_table
                // and ideally vice-versa 
                
                //check that the columns are those expected. If not, abort.
                
                
                 $checkColumns = "Call checkIfColumnsofA_areinB ('$tempTable', '$main_table', @out);";
                echo "Checking to see if columns names in $tempTable match the ones that are in $main_table" ."<br>" ;
                
                mysqli_query($connect, $checkColumns) or die(mysqli_error($connect));
                
                $checkColumnsCount = mysqli_query($connect, "select @out");
                $res = $checkColumnsCount->fetch_assoc();
                echo "Columns Found: " . $res['@out'];
                
                 // This finds the number of duplicates found in $tempTable
                
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM $tempTable GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
               
                // while there are duplicates then echo how many found 
                
                while($result = mysqli_fetch_assoc($duplicatedSymbol)){
                    
                    echo "Number of Duplicates found in $tempTable :";
                    
                    $_SESSION["duplicates"]=$result["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                // This compares symbols in $tempTable to $main_table to see if there are any duplicates
               
                
                $selectSymbolExists="SELECT symbol from $tempTable as found where symbol in (SELECT DISTINCT symbol from $main_table);";
                $symbolExists=mysqli_query($connect,$selectSymbolExists);
                
                // while should echo the duplicates that were found when comparing $tempTable and $main_table;
                
                while($row = mysqli_fetch_assoc($symbolExists)){
                    
                    echo "The following duplicates were found when comparing $tempTable and $main_table: " . $row("found");
                    
                
                    $_SESSION["duplicates"]=$row["found"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                
   
                //load the table
                $loadQuery="LOAD DATA LOCAL INFILE '".$_FILES['file']['tmp_name']."' INTO TABLE $tempTable FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES;";
                mysqli_query($connect,$loadQuery) or die(mysqli_error($connect));
                
                
                /* This will delete any NULL rows after the file has been created and inserted into temp_append_table. which will prevent the issue of empty rows before inserting into temp_main_table.
                 by Tom Tran 2018-05-24 */
                
               $removeEmptyRow="DELETE FROM $tempTable WHERE symbol='' or symbol IS NULL"; 
                mysqli_query($connect,$removeEmptyRow);
                
             
           
      // actions specific to each operation          
    
    switch ($actionSelection) {
        case "flexible_update":
            
         //echo "flexible_update section ";    
          
                // sets aside symbols that do not exist in $main_table
                // update the other symbols and alert user so he can append or take other corrective action
                $selectSymbolNotExists="SELECT symbol FROM temp_update_table where symbol NOT IN (SELECT DISTINCT symbol from $main_table);";
                $symbolNotExists=mysqli_query($connect,$selectSymbolNotExists);
                
                while($row = mysqli_fetch_assoc($symbolNotExists)){
                    $_SESSION["symbolNotExists"]=$row["symbol"]." ".$_SESSION["symbolNotExists"];
                } //while
                
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
                $updateTable2 = "call copyValuesFromA2B ('temp_update_table', '$main_table',0);";
                //echo $updateTable2;
                mysqli_query($connect,$updateTable2) or die(mysqli_error($connect));
               
                
                //$_SESSION["table"]="$main_table";
                $userAction="update $main_table";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                fclose($fp);
                  
                 break;
    

        case "append":
    //append new symbols from  csv file
   
         //echo "append section ";
            
                // check that the symbol does not already exist in $main_table
                //$selectSymbolExists="SELECT symbol from temp_append_table where symbol in (SELECT DISTINCT symbol from $main_table);";
               // $symbolExists=mysqli_query($connect,$selectSymbolExists);
                
                 // while($row = mysqli_fetch_assoc($symbolExists)){
                   // $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                    //header("location:loader.php");
                    //die(mysqli_error($connect));
                //};
           
                
                /*
                while($row = mysqli_fetch_assoc($symbolExists)){
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                    // header("location:loader.php");
                    die(mysqli_error($connect));
                }
                
                 * 
                 */
            
                // This finds the number of duplicates found in $tempTable
                
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM $tempTable GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
               
                // while there are duplicates then echo how many found - This isn't working at the moment.... - TT
                
                while($result = mysqli_fetch_assoc($duplicatedSymbol)){
                    
                    echo "Number of Duplicates found in $tempTable :";
                    
                    $_SESSION["duplicates"]=$result["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                // This compares symbols in $tempTable to $main_table to see if there are any duplicates
               
                
                $selectSymbolExists="SELECT symbol from $tempTable where symbol in (SELECT DISTINCT symbol from $main_table);";
                $symbolExists=mysqli_query($connect,$selectSymbolExists);
                
                // while should echo the duplicates that were found when comparing $tempTable and $main_table;
                
                while($row = mysqli_fetch_assoc($symbolExists)){
                    
                    echo "The following duplicates were found when comparing $tempTable and $main_table:" ;
                    
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                // This will take the data that is found in the temp_append_table and INSERT it into the $main_table.
                
                $insertQuery="INSERT INTO $main_table SELECT * FROM temp_append_table;";  // Shortened this line for quicker insert. 2018-05-30
                //echo "$insertQuery";
                mysqli_query($connect,$insertQuery) or die(mysqli_query($connect));
                fclose($fp);
                mysqli_close($connect);
                header("Location:loader.php");
     
                break;
    
        } 


// switch
  
        
  /*      
    switch ($actionSelection) {
        case "flexible_update":
         //echo "flexible_update section ";    
          
                // sets aside symbols that do not exist in $main_table
                // update the other symbols and alert user so he can append or take other corrective action
                $selectSymbolNotExists="SELECT symbol FROM temp_update_table where symbol NOT IN (SELECT DISTINCT symbol from $main_table);";
                $symbolNotExists=mysqli_query($connect,$selectSymbolNotExists);
                
                while($row = mysqli_fetch_assoc($symbolNotExists)){
                    $_SESSION["symbolNotExists"]=$row["symbol"]." ".$_SESSION["symbolNotExists"];
                } //while
                
                /*
             
                  $set=array();
                for($z=0;$z<count($header);$z++){
                    $set[$z]="$main_table.".$header[$z]."="."temp_update_table.".$header[$z];
                };
                */
                
                //prepare for actual update
                //
                //this puts Null for all blank values
        
        
        /*
                $tablevar = "temp_update_table";
                $updateTable1 = "call blanks2nulls ('temp_update_table');";
                //echo $updateTable1;
                mysqli_query($connect,$updateTable1) or die(mysqli_error($connect));
                       
                // this updates all values with the new values if they are not NULL
                $updateTable2 = "call copyValuesFromA2B ('temp_update_table', '$main_table',0);";
                //echo $updateTable2;
                mysqli_query($connect,$updateTable2) or die(mysqli_error($connect));
               
                
                //$_SESSION["table"]="$main_table";
                $userAction="update $main_table";
                $log="INSERT INTO activity (user, `action`) VALUES ('$user','$userAction')";
                mysqli_query($connect,$log);
                fclose($fp);
                  
                 break;
    

/*        case "append":
    //append new symbols from  csv file
   
         //echo "append section ";
            
                //check that the symbol does not already exist in $main_table
                //$selectSymbolExists="SELECT symbol from temp_append_table where symbol in (SELECT DISTINCT symbol from $main_table);";
                //$symbolExists=mysqli_query($connect,$selectSymbolExists);
            
                // This finds the number of duplicates found in $tempTable
                
                $findDuplicatedSymbol="SELECT symbol, COUNT(*) c FROM $tempTable GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol=mysqli_query($connect,$findDuplicatedSymbol);
               
                // while there are duplicates then echo how many found 
                
                while($result = mysqli_fetch_assoc($duplicatedSymbol)){
                    
                    echo "Number of Duplicates found in $tempTable :";
                    
                    $_SESSION["duplicates"]=$result["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                // This compares symbols in $tempTable to $main_table to see if there are any duplicates
               
                
                $selectSymbolExists="SELECT symbol from $tempTable where symbol in (SELECT DISTINCT symbol from $main_table);";
                $symbolExists=mysqli_query($connect,$selectSymbolExists);
                
                // while should echo the duplicates that were found when comparing $tempTable and $main_table - This doesnt work yet.... TT
                
                while($row = mysqli_fetch_assoc($symbolExists)){
                    
                    echo "The following duplicates were found when comparing $tempTable and $main_table:" ;
                    
                    $_SESSION["duplicates"]=$row["symbol"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
    
                // This will take the data that is found in the temp_append_table and INSERT it into the $main_table.
                $insertQuery="INSERT INTO $main_table SELECT * FROM temp_append_table;";  // Shortened this line for quicker insert. 2018-05-30
                //echo "$insertQuery";
                mysqli_query($connect,$insertQuery);
                fclose($fp);
                mysqli_close($connect);
                header("Location:loader.php");
     
                break;
    
        } // switch
     
?>