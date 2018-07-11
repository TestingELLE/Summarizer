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
        
    
    if(isset($_POST["submit"])){
        $_SESSION['uname']=$_POST["username"];//we need uname for access to table
        $_SESSION['uname_long']="pupone_".$_SESSION['uname'];//we need uname_long for access to database
        $_SESSION['psw']=$_POST["password"];
        
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
        
        $con1=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$con1)
        {
            die('Could not connect: ' . mysqli_error());
        }
        mysqli_select_db($con1,"pupone_Summarizer");
  
        //$result=mysqli_query($con1,"SELECT * FROM accounts WHERE username='".$_SESSION['uname']."' and password='".$_SESSION['psw']."'limit 1");
        $result=mysqli_query($con1,"SELECT * FROM accounts WHERE username='".$_SESSION['uname']."'limit 1");
        $row = mysqli_fetch_assoc($result);
        //check the user type and save user name in session
        
        $_SESSION['type']=$row['type'];
        $_SESSION["Last_Activity"]=time(); 
        header("location: Summarizer.php");
        if(mysqli_num_rows($result)==1 && $row['type']=="Admin" || $row['type']=="Maintainer" ){
            
            exit();
            mysqli_close($con1);
        };
        if(mysqli_num_rows($result)==1 && $row['type']=="viewer"){
            exit();
            mysqli_close($con1);
        };
        if(mysqli_num_rows($result)==1 && $row['type']=="Programmer"){
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