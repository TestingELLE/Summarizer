  
<!DOCTYPE html>
<html lang="en">
    <title>Find Duplicates</title>

    <head>
        <meta charset="UTF-8">
    <h1 style="text-align:center">Find Duplicates</h1>
</head>

<body>
    <footer>
   
        <form action="findDuplicates.php" method="post" id="this">
            <button type ="submit" name ="checkColumns" id="findDuplicates" class="button" value="Find Duplicates"> Find Duplicates</button>
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

if (isset($_POST["findDuplicates"])) {

    $connect = mysqli_connect("rendertech.com", $_SESSION['uname_long'], $_SESSION['psw'], "pupone_Summarizer");
    if (!$connect) {
        die('Could not connect: ' . mysqli_error());
    }
    // This finds the number of duplicates found in $tempTable
                
                $findDuplicatedSymbol = "SELECT symbol, COUNT(*) c FROM temp_append_table GROUP BY symbol HAVING c > 1;";
                $duplicatedSymbol = mysqli_query($connect,$findDuplicatedSymbol);
               
                // while there are duplicates then echo how many found 
                
                while($result = mysqli_fetch_assoc($duplicatedSymbol)){
                    
                    echo "Number of Duplicates found in temp_append_table: ";
                    
                    $_SESSION["duplicates"]=$result["c"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
                
                // This compares symbols in $tempTable to $main_table to see if there are any duplicates
               
                
                $selectSymbolExists= "SELECT symbol from temp_append_table as found where symbol in (SELECT DISTINCT symbol from test_main_table);";
                $symbolExists = mysqli_query($connect,$selectSymbolExists);
                
                // while should echo the duplicates that were found when comparing $tempTable and $main_table;
                
                while($row = mysqli_fetch_assoc($symbolExists)){
                    
                    echo "The following duplicates were found when comparing temp_append_table and test_main_table: " . $row("found");
                    
                
                    $_SESSION["duplicates"]=$row["found"]." ".$_SESSION["duplicates"];
                    header("location:loader.php");
                    die(mysqli_error($connect));
                };
     
     
}
?>




