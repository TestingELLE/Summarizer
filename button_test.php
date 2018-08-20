
<?php
    session_start();
  
    if(isset($_POST['submit1'])){
        echo "something1";
    }
    if(isset($_POST['submit2'])){
        echo "something2";
    }if(isset($_POST['submit3'])){
        echo "something3";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<footer>
    <form action="button_test.php" method="post" id="this" style="width: 100%; height: 100%; background-color: darkblue;">
        <button type="submit" name="submit1" id="submit1" class="button" value="Submit">this is something</button>
        <button type="submit" name="submit2" id="submit2" class="button" value="this">this is something2</button>
        <input type="submit" name="submit3" id="submit3" class="button" value="true"/>
        <input type='text'>
    </form>
    <input type='text'>
</footer>
</body>
</html>