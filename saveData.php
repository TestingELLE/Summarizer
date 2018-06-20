<?php
     
     session_start();

     if(!isset($_SESSION['uname'])){
       
        header("Location:logout.php");
        exit();
    }
    //set up database connection
    $con=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
    if (!$con)
    {
    die('Could not connect: ' . mysqli_error());
    }
    //get the data from symbol page and update the database 
    $price=$_POST["price"];
    $intern=$_POST["intern"];
    $LDate=$_POST["LDate"];  
    $mktCap=$_POST["mktCap"];  
    $NDate=$_POST["NDate"];
    $PTarget=$_POST["PTarget"];  
    $LTarget=$_POST["LTarget"]; 
    $industry=$_POST["industry"];
    $upside=$_POST["upside"];
    $secondPTarget=$_POST["secondPTarget"];
    $secondupside=$_POST["secondupside"];
    $down=$_POST["down"]; 
    $PStock=$_POST["PStock"]; 
    $biotech=$_POST["biotech"]; 
    $active=$_POST["active"]; 
    $LUpdate=$_POST["LUpdate"];
    $rank=$_POST["rank"]; 
    $AnalysisDate=$_POST["AnalysisDate"];
    $confidence=$_POST["confidence"];
    $Tweight=$_POST["Tweight"];  
    $Tposition=$_POST["Tposition"]; 
    $analysisPrice=$_POST["analysisPrice"];
    $cash=$_POST["cash"]; 
    $actualWeight=$_POST["actualWeight"];
    $actualPosition=$_POST["actualPosition"];
    $burn=$_POST["burn"]; 
    $diff=$_POST["diff"];   
    $question=$_POST["question"];
    $catalyst=$_POST["catalyst"];
    $strategy=$_POST["strategy"];
    $case=$_POST["case"];
    $ticket=$_POST["ticket"];
    $note=$_POST["note"];
    // $comment=$_POST["comment"];
    $boah=$_POST["boah"];
    $symbol=$_POST["symbol"];
    $yes=TRUE;
    // industry='$industry', mkt_cap='$mktCap', price=$price, biotech='$biotech' , penny_stock='$PStock', active='$active', catalysts='$catalyst', last_earnings='$LDate', next_earnings='$NDate', bo_ah='$boah', intern='$intern', cash='$cash', burn='$burn', related_tickets='$ticket', analysis_date='$AnalysisDate', analysis_price='$analysisPrice', low_target='$LTarget', price_target='$PTarget', upside='$upside', down_risk='$down', rank='$rank', confidence='$confidence', worse_case='$case', target_weight='$Tweight', target_position='$Tposition', actual_position='$actualPosition', actual_weight='$actualWeight', diff='$diff', stragety='$strategy', questions='$question', notes='$note', skype_comments='$comment', last_updates='$LUpdate'
    
    
    mysqli_select_db($con,"pupone_Summarizer");
    $currentuser=$_SESSION['uname'];
    $userAction='modified data';
    $log="INSERT INTO activity (user, `action`,`page`) VALUES ('$currentuser','$userAction','$symbol')";
    mysqli_query($con,$log);
    $sql="UPDATE main_table SET industry='$industry', market_cap='$mktCap', current_price='$price', biotech='$biotech' , penny_stock='$PStock', active='$active', catalysts='$catalyst', last_earnings='$LDate', next_earnings='$NDate', bo_ah='$boah', intern='$intern', cash='$cash', burn='$burn', related_tickers='$ticket', analysis_date='$AnalysisDate', analysis_price='$analysisPrice', variation1='$LTarget', 1st_price_target='$PTarget', 1st_upside='$upside',2nd_price_target='$secondPTarget',2nd_upside='$secondupside', downside_risk='$down', rank='$rank', confidence='$confidence', worst_case='$case', target_weight='$Tweight', target_position='$Tposition', actual_position='$actualPosition', actual_weight='$actualWeight', weight_difference='$diff', strategy='$strategy', discussion='$question', notes='$note', last_update='$LUpdate' WHERE symbol='$symbol'";
    if(mysqli_query($con,$sql)){
        echo "Successfully updated";
        
    } else {
        echo "Error: ". mysqli_error($con);
    }; 
    


    


?>