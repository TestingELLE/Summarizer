<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 $headerName=["symbol", "industry", "sub_industry", "market_cap", "current_price", "pharma", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL", "variationD"]; 
 
 
 
 xt/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=$main_table_'.$date.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array("symbol", "industry", "sub_industry", "market_cap", "current_price", "pharma", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL","variationD"));
        if (count($users) > 0) {
            foreach ($users as $row) {
                if(!empty($row)){
                    fputcsv($output, $row);
                }
            }
        }
        
        mysqli_close($con);
    };


     //download data
     if(isset($_GET["filename"])){
        $connect=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$connect)
        {
        die('Could not connect: ' . mysqli_error());
        }
        $filename=$_GET["filename"];
        $currentuser=$_SESSION['uname'];
        $userAction="downloaded backup table";
        $log="INSERT INTO activity (user, `action`) VALUES ('$currentuser','$userAction')";
        mysqli_query($connect,$log);
        $queryTable = "SELECT * FROM $filename WHERE symbol!='symbol'";
        if (!$queryResult = mysqli_query($connect, $queryTable)) {
            exit(mysqli_error($connect));
        }  
        $users = array();
        if (mysqli_num_rows($queryResult) > 0) {
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $users[] = $row;
            }
        }
        date_default_timezone_set('America/Chicago');
        $date=date("Y-m-d h:i:sa",time());
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=BackUpTable_'.$date.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array("symbol", "industry","sub_industry", "market_cap", "current_price", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside","2nd_price_target","2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update","last_price", "variationL"));
       if (count($users) > 0) {
            foreach ($users as $row) {
                if(!empty($row)){
                    fputcsv($output, $row);
                }
            }
        }
       
        
        /* 
    //download current table
    if($_POST["download"]){
        $con=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
        if (!$con)
        {
        die('Could not connect: ' . mysqli_error());
        }
        
        $currentuser=$_SESSION['uname'];
        $userAction="downloaded current table";
        $log="INSERT INTO activity (user, `action`) VALUES ('$currentuser','$userAction')";
        mysqli_query($con,$log);
        $query = "SELECT * FROM $main_table";
        if (!$result = mysqli_query($con, $query)) {
            exit(mysqli_error($con));
        };
        
       
        $users = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }
        }
        date_default_timezone_set('America/Chicago');
        $time=time();
        $date=date("Y-m-d h:i:sa",time());
        header('Content-Type: te
     }*/