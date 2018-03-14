<?php
    $price=$_POST["price"];
    $intern=$_POST["intern"];
    $LDate=$_POST["LDate"];  
    $mktCap=$_POST["mktCap"];  
    $NDate=$_POST["NDate"];
    $PTarget=$_POST["PTarget"];  
    $LTarget=$_POST["LTarget"]; 
    $industry=$_POST["industry"];
    $upside=$_POST["upside"];
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
    $comment=$_POST["comment"];
    $boah=$_POST["boah"];
    $symbol=$_POST["symbol"];
    $yes=TRUE;
    // industry='$industry', mkt_cap='$mktCap', price=$price, biotech='$biotech' , penny_stock='$PStock', active='$active', catalysts='$catalyst', last_earnings='$LDate', next_earnings='$NDate', bo_ah='$boah', intern='$intern', cash='$cash', burn='$burn', related_tickets='$ticket', analysis_date='$AnalysisDate', analysis_price='$analysisPrice', low_target='$LTarget', price_target='$PTarget', upside='$upside', down_risk='$down', rank='$rank', confidence='$confidence', worse_case='$case', target_weight='$Tweight', target_position='$Tposition', actual_position='$actualPosition', actual_weight='$actualWeight', diff='$diff', stragety='$strategy', questions='$question', notes='$note', skype_comments='$comment', last_updates='$LUpdate'
   $con=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
    if (!$con)
    {
    die('Could not connect: ' . mysqli_error());
    }
    mysqli_select_db($con,"pupone_Summarizer");
    $sql="UPDATE main_table SET industry='$industry', mkt_cap='$mktCap', price='$price', biotech='$biotech' , penny_stock='$PStock', active='$active', catalysts='$catalyst', last_earnings='$LDate', next_earnings='$NDate', bo_ah='$boah', intern='$intern', cash='$cash', burn='$burn', related_tickets='$ticket', analysis_date='$AnalysisDate', analysis_price='$analysisPrice', low_target='$LTarget', price_target='$PTarget', upside='$upside', down_risk='$down', rank='$rank', confidence='$confidence', worse_case='$case', target_weight='$Tweight', target_position='$Tposition', actual_position='$actualPosition', actual_weight='$actualWeight', diff='$diff', stragety='$strategy', questions='$question', notes='$note', skype_comments='$comment', last_updates='$LUpdate' WHERE symbol='$symbol'";
    // $sql="UPDATE test SET notes='$note' WHERE symbol='AABA'";
    if(mysqli_query($con,$sql)){
        echo "Successfully updated";
        mysqli_close($con);
    } else {
        echo "Error: ". mysqli_error($con);
    };

    


    


?>