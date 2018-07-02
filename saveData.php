<?php

session_start();

if (!isset($_SESSION['uname'])) {

    header("Location:logout.php");
    exit();
}
//set up database connection
$con = mysqli_connect("rendertech.com", $_SESSION['uname_long'], $_SESSION['psw'], "pupone_Summarizer");
if (!$con) {
    die('Could not connect: ' . mysqli_error());
}
//get the data from symbol page and update the database 
$industry = $_POST["industry"];
$mktCap = $_POST["mktCap"];
$price = $_POST["price"];
$biotech = $_POST["biotech"];
$PStock = $_POST["PStock"];
$status = $_POST["status"];
$catalyst = $_POST["catalyst"];
$LDate = $_POST["LDate"];
$next_earnings = $_POST["next_earnings"];
$boah = $_POST["boah"];
$intern = $_POST["intern"];
$cash = $_POST["cash"];
$burn = $_POST["burn"];
$ticket = $_POST["ticket"];
$AnalysisDate = $_POST["AnalysisDate"];
$analysisPrice = $_POST["analysisPrice"];
$LTarget = $_POST["LTarget"];
$PTarget = $_POST["PTarget"];
$upside = $_POST["upside"];
$secondPTarget = $_POST["secondPTarget"];
$secondupside = $_POST["secondupside"];
$down = $_POST["down"];
$rank = $_POST["rank"];
$confidence = $_POST["confidence"];
$case = $_POST["case"];
$Tweight = $_POST["Tweight"];
$Tposition = $_POST["Tposition"];
$actualPosition = $_POST["actualPosition"];
$actualWeight = $_POST["actualWeight"];
$diff = $_POST["diff"];
$strategy = $_POST["strategy"];
$question = $_POST["question"];
$note = $_POST["note"];
$LUpdate = $_POST["LUpdate"];
$last_price = $_POST["last_price"];

// $comment=$_POST["comment"];

$symbol = $_POST["symbol"];
$yes = TRUE;
// industry='$industry', mkt_cap='$mktCap', price=$price, biotech='$biotech' , penny_stock='$PStock', active='$active', catalysts='$catalyst', last_earnings='$LDate', next_earnings='$NDate', bo_ah='$boah', intern='$intern', cash='$cash', burn='$burn', related_tickets='$ticket', analysis_date='$AnalysisDate', analysis_price='$analysisPrice', low_target='$LTarget', price_target='$PTarget', upside='$upside', down_risk='$down', rank='$rank', confidence='$confidence', worse_case='$case', target_weight='$Tweight', target_position='$Tposition', actual_position='$actualPosition', actual_weight='$actualWeight', diff='$diff', stragety='$strategy', questions='$question', notes='$note', skype_comments='$comment', last_updates='$LUpdate'


mysqli_select_db($con, "pupone_Summarizer");
$currentuser = $_SESSION['uname'];
$userAction = 'modified data';
$log = "INSERT INTO activity (user, `action`,`page`) VALUES ('$currentuser','$userAction','$symbol')"; // move this line below $sql = "update....
mysqli_query($con, $log);
$sql = "UPDATE main_table SET industry='$industry', market_cap='$mktCap', current_price='$price', biotech='$biotech' , penny_stock='$PStock', status='$status', catalysts='$catalyst', last_earnings='$LDate', next_earnings='$next_earnings', bo_ah='$boah', intern='$intern', cash='$cash', burn='$burn', related_tickers='$ticket', analysis_date='$AnalysisDate', analysis_price='$analysisPrice', variation1='$LTarget', 1st_price_target='$PTarget', 1st_upside='$upside',2nd_price_target='$secondPTarget',2nd_upside='$secondupside', downside_risk='$down', rank='$rank', confidence='$confidence', worst_case='$case', target_weight='$Tweight', target_position='$Tposition', actual_position='$actualPosition', actual_weight='$actualWeight', weight_difference='$diff', strategy='$strategy', discussion='$question', notes='$note', last_update='$LUpdate', last_price='$last_price' WHERE symbol='$symbol';";

//$sql="UPDATE main_table SET last_price='$last_price' WHERE symbol='MDCO'";

if (mysqli_query($con, $sql)) {
    echo "Successfully updated";
} else {
    echo "Error: " . mysqli_error($con);
};
?>