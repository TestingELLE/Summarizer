  
<!DOCTYPE html>
<html lang="en">
    <title>CheckColumnsTestPage</title>

    <head>
        <meta charset="UTF-8">
    <h1 style="text-align:center">Check Columns Test Page</h1>
</head>

<body>
    <footer>
   
        <form action="checkColumns.php" method="post" id="this">
            <button type ="submit" name ="checkColumns" id="checkColumns" class="button" value="Check Columns"> Check Columns</button>
            <button type ="submit" name="echo" id="echo" class="button" value="echo">This is a echo button</button>
        </form>
    </footer>
</body>

</html>


<?php
session_start();

if (isset($_POST['echo'])) {
    echo "Hello World";
}

if (isset($_POST["checkColumns"])) {

    $connect = mysqli_connect("rendertech.com", $_SESSION['uname_long'], $_SESSION['psw'], "pupone_Summarizer");
    if (!$connect) {
        die('Could not connect: ' . mysqli_error());
    }
    //replaces the above
    //check that the columns are those expected. If not, abort.
    
    /*
    try {
                 $checkColumns = "Call checkIfColumnsofA_areinB ('test2', 'test1', @out);";
                echo "<br>" . "Checking to see if columns names in test2 match the ones that are in test1" ."<br>" ;
                
                mysqli_query($connect, $checkColumns) or die(mysqli_error($connect));
                
                } catch (Exception $e) {
                    
                    echo  "Columns names are not as expected";
                    die("aborting procedure");
                }
    */
    $checkColumns = "Call checkIfColumnsofA_areinB ('test2', 'test1', @out);";
    echo $checkColumns ."<br>" ;
    mysqli_query($connect, $checkColumns);
   

    $checkColumnsCount = mysqli_query($connect, "SELECT @out");
   $res = $checkColumnsCount->fetch_assoc();
    echo "Columns Found: " . $res['@out'];
    
    // This works just as well.
    
    //$res = mysqli_query($connect, "SELECT @out")->fetch_assoc();
    //echo "Columns Found: " . $res['@out'];

    
    if ($res >= 1) {
      $_SESSION["colNotExists"] = $colNotexists;
     header("location:checkColumns.php");
     die(mysqli_error($connect));
     }
     
     
}
?>




