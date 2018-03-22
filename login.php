<?php
    
    session_start();
    
    $_SESSION['count'];
    isset($PHPSESSID)?session_id($PHPSESSID):$PHPSESSID = session_id(); 
    
    $_SESSION['count']++; 
    setcookie('PHPSESSID', $PHPSESSID, time()+21800);
   

    $year = time() + 31536000;

    if($_POST['remember'])  {
        setcookie('remember_me', $_POST['username'], $year);
        setcookie('remember_me2', $_POST['password'], $year);  
        }
        
    
    $con1=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
    if (!$con1)
    {
    die('Could not connect: ' . mysqli_error());
    }
    mysqli_select_db($con1,"pupone_Summarizer");
    if(isset($_POST["submit"])){
        $uname=$_POST["username"];
        $psw=$_POST["password"];
        if(!$_POST['remember']) {
            if(isset($_COOKIE['remember_me'])) {
                    $past = time() - 100;
                    setcookie('remember_me',"", $past);
                }
                if(isset($_COOKIE['remember_me2'])) {
                    $past = time() - 100;
                    setcookie('remember_me2',"", $past);
                }
            }
        // $sql="select * from account where username='".$uname."' and password='".$psw."'limit 1";
        $result=mysqli_query($con1,"SELECT * FROM account WHERE username='".$uname."' and password='".$psw."'limit 1");
       
        $row = mysqli_fetch_assoc($result);
        
        if(mysqli_num_rows($result)==1 && $row['type']=="Admin" || $row['type']=="Maintainer" ){
            $_SESSION['type']=$row['type'];
            $_SESSION['loggedin']=$uname;
            $_SESSION["Last_Activity"]=time(); 
            header("location: Summarizer.php");
            exit();
            mysqli_close($con1);
        };
        if(mysqli_num_rows($result)==1 && $row['type']=="viewer"){
            $_SESSION['type']=$row['type'];
            $_SESSION['loggedin']=$uname;
            $_SESSION["Last_Activity"]=time(); 
            header("location: Summarizer.php");
            exit();
            mysqli_close($con1);
        };
        if(mysqli_num_rows($result)==1 && $row['type']=="Programmer"){
            $_SESSION['type']=$row['type'];
            $_SESSION['loggedin']=$uname;
            $_SESSION["Last_Activity"]=time(); 
            header("location: Summarizer.php");
            exit();
            mysqli_close($con1);
        };
        
       
    }
    


?>
<html>
    <body>
        <form action="login.php" method="POST">
            User: </br>
            <input type="text" name="username" value="<?php 
            
                echo $_COOKIE['remember_me'];
              ?>"><br/>
            Password<br/>
            <input type="password" name="password" value="<?php
             
                echo $_COOKIE['remember_me2']; 
             
             ?>"><br/>
            <input type="submit" name="submit"value="Login">
            <br>
            <br>
            <input type="checkbox" name="remember" id="remember" value="1" <?php 
            if(isset($_COOKIE['remember_me'])&&isset($_COOKIE['remember_me2'])) {
		        echo 'checked="checked"';
	            }
	            else {
		            echo '';
	            }
	        ?>>
            <label for="remember">Remember me</label>
        </form>
     
    </body>
</html>