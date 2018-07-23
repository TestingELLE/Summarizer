<?php

 
    /*
    
    if (isset($_SESSION["break"]) || isset($_SESSION["symbolNotExists"]) || isset($_SESSION["duplicates"]) || isset($_SESSION["colNotExists"])) {
    echo '
        <script>
            alert(`You are updating ' . $_SESSION["table"] . ';\n\nThe following symbols do not exist in the database:\n   ' . $_SESSION["symbolNotExists"] . ';\nDuplicated symbols:' . $_SESSION["duplicates"] . ';\nColumns not exist: ' . $_SESSION["colNotExists"] . '`);
        </script>
        ';
    $_SESSION["break"] = null;
    $_SESSION["symbolNotExists"] = null;
    $_SESSION["duplicates"] = null;
    $_SESSION["colNotExists"] = null;
    echo '
        <script>
        window.location.href="loader.php";
        </script>
        ';
}
     * */
    


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
                

     /*
  // check that the column names are right
                    // all columns of append table should exist in $main_table
                    // and ideally vice-versa
           
                $checkColumns = "Call checkIfColumnsofA_areinB ('temp_append_table', '$main_table', @a);";
                echo $checkColumns;
                mysqli_query($connect, $checkColumns) or die(mysqli_error($connect));
                $res = mysqli_query($connect, "select @a");
                $checkColumnsCount = $res->fetch_assoc();
                echo $checkColumnsCount;
                 if($checkColumnsCount>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
               //mysqli_free_result($res);
               */


    
                //NOT WORKING -- TO FIX - CUT OUT FOR NOW
                //replaces the above
                //check that the columns are those expected. If not, abort.
                 $checkColumns = "Call checkIfColumnsofA_areinB ('temp_update_table', '$main_table', @out);";
                echo $checkColumns ."<br>" ;
                mysqli_query($connect, $checkColumns);
                
                $checkColumnsCount = mysqli_query($connect, "select @out");
               $res = $checkColumnsCount->fetch_assoc();
               echo $res['@out'];
               
               /*
                 if($checkColumnsCount>=1){
                    $_SESSION["colNotExists"]=$colNotexists;
                    header("location:loader.php");
                    die(mysqli_error($connect));
                }
                */
               
               
               