<?php
session_start();

if (!isset($_SESSION['uname'])) {
    header("Location:logout.php");
    exit();
}
if ($_SESSION['type'] == "viewer") {
    echo "
        <script>
            alert('You do not have privilege to access the page. Please contact website manager.');
            window.location.href='Summarizer.php';
        </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
    <title>Flexible Download and Show Views Page</title>

    <head>
        <meta charset="UTF-8">
    <h1 style="text-align:center">Flexible Download and Show Views Page</h1>
</head>

<body>
    <footer>

        <form action="flexDownload.php" method="post" id="this">
            <button type ="submit" name ="full_download" id="full_download" class="button" value="Full Download">Full View Download</button>
            <button type ="submit" name="reg_download" id="reg_download" class="button" value="Regular Download">Regular View Download</button>
            <button type ="submit" name ="full_view" id="full_download_view" class="button" value="Full Download">Full View</button>
            <button type ="submit" name="reg_view" id="reg_download_view" class="button" value="Regular Download">Regular View</button>
        </form>
    </footer>
</body>

</html>


<?php
// Regular download View

if (isset($_POST["reg_view"])) {
    $connect = mysqli_connect("rendertech.com", $_SESSION['uname_long'], $_SESSION['psw'], "pupone_Summarizer");
    if (!$connect) {
        die('Could not connect: ' . mysqli_error());
    }

    echo "<br></br>" . "Displaying Regular Download View" . "<br></br>";

    $regViewQuery = "SELECT * FROM v_reg_download;";

    $regResult = mysqli_query($connect, $regViewQuery);

    echo "<table border = '1'>
            
        <tr>
        
        <th>Symbol</th>
        <th>Industry</th>
        <th>Sub-industry</th>
        <th>Current Price</th>
        <th>BioTech</th>
        <th>Analysis Price</th>
        <th>1st Price Target</th>
        <th>2nd Price Target</th>
        <th>Downside Risk</th>
        <th>Rank</th>
        <th>Confidence</th>
        <th>Target Weight</th>
        </tr>";

    while ($row = mysqli_fetch_array($regResult)) {

        echo "<tr>";

        echo "<td>" . $row['symbol'] . "</td>";
        echo "<td>" . $row['industry'] . "</td>";
        echo "<td>" . $row['sub_industry'] . "</td>";
        echo "<td>" . $row['current_price'] . "</td>";
        echo "<td>" . $row['biotech'] . "</td>";
        echo "<td>" . $row['analysis_price'] . "</td>";
        echo "<td>" . $row['1st_price_target'] . "</td>";
        echo "<td>" . $row['2nd_price_target'] . "</td>";
        echo "<td>" . $row['downside_risk'] . "</td>";
        echo "<td>" . $row['rank'] . "</td>";
        echo "<td>" . $row['confidence'] . "</td>";
        echo "<td>" . $row['target_weight'] . "</td>";

        echo "</tr>";
    }

    echo "</table>";
}


if (isset($_POST["full_view"])) {
    $connect = mysqli_connect("rendertech.com", $_SESSION['uname_long'], $_SESSION['psw'], "pupone_Summarizer");
    if (!$connect) {
        die('Could not connect: ' . mysqli_error());
    }

    echo "<br></br>" . "Displaying Full Download View" . "<br></br>";

    $fullViewQuery = "SELECT * FROM v_full_download;";

    $fullResult = mysqli_query($connect, $fullViewQuery);

    echo "<table border = '1'>
            
        <tr>
        
        <th>Symbol</th>
        <th>Industry</th>
        <th>Sub-industry</th>
        <th>Market Cap</th>
        <th>Current Price</th>
        <th>Pharma</th>
        <th>BioTech</th>
        <th>Penny Stock</th>
        <th>Status</th>
        <th>Catalysts</th>
        <th>Last Earnings</th>
        <th>Next Earnings</th>
        <th>BO_AH</th>
        <th>Intern</th>
        <th>Cash</th>
        <th>Burn</th>
        <th>Related Tickers</th>
        <th>Analysis Date</th>
        <th>Analysis Price</th>
        <th>Variation 1</th>
        <th>1st Price Target</th>
        <th>1st Upside</th>
        <th>2nd Price Target</th>
        <th>2nd Upside</th>
        <th>Downside Risk</th>
        <th>Rank</th>
        <th>Confidence</th>
        <th>Worst Case</th>
        <th>Target Weight</th>
        <th>Target Position</th>
        <th>Actual Position</th>
        <th>Actual Weight</th>
        <th>Weight Difference</th>
        <th>Strategy</th>
        <th>Discussion</th>
        <th>Notes</th>
        <th>Last Update</th>
        <th>Last Price</th>
        <th>Variation L</th>
        <th>Variation D</th>
        
        </tr>";

    while ($row = mysqli_fetch_array($fullResult)) {

        echo "<tr>";

        echo "<td>" . $row['symbol'] . "</td>";
        echo "<td>" . $row['industry'] . "</td>";
        echo "<td>" . $row['sub_industry'] . "</td>";
        echo "<td>" . $row['market_cap'] . "</td>";
        echo "<td>" . $row['current_price'] . "</td>";
        echo "<td>" . $row['pharma'] . "</td>";
        echo "<td>" . $row['biotech'] . "</td>";
        echo "<td>" . $row['penny_stock'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['catalysts'] . "</td>";
        echo "<td>" . $row['last_earnings'] . "</td>";
        echo "<td>" . $row['next_earnings'] . "</td>";
        echo "<td>" . $row['bo_ah'] . "</td>";
        echo "<td>" . $row['intern'] . "</td>";
        echo "<td>" . $row['cash'] . "</td>";
        echo "<td>" . $row['burn'] . "</td>";
        echo "<td>" . $row['related_tickers'] . "</td>";
        echo "<td>" . $row['analysis_date'] . "</td>";
        echo "<td>" . $row['analysis_price'] . "</td>";
        echo "<td>" . $row['variation1'] . "</td>";
        echo "<td>" . $row['1st_price_target'] . "</td>";
        echo "<td>" . $row['1st_upside'] . "</td>";
        echo "<td>" . $row['2nd_price_target'] . "</td>";
        echo "<td>" . $row['2nd_upside'] . "</td>";
        echo "<td>" . $row['downside_risk'] . "</td>";
        echo "<td>" . $row['rank'] . "</td>";
        echo "<td>" . $row['confidence'] . "</td>";
        echo "<td>" . $row['worst_case'] . "</td>";
        echo "<td>" . $row['target_weight'] . "</td>";
        echo "<td>" . $row['target_position'] . "</td>";
        echo "<td>" . $row['actual_position'] . "</td>";
        echo "<td>" . $row['actual_weight'] . "</td>";
        echo "<td>" . $row['weight_difference'] . "</td>";
        echo "<td>" . $row['strategy'] . "</td>";
        echo "<td>" . $row['discussion'] . "</td>";
        echo "<td>" . $row['notes'] . "</td>";
        echo "<td>" . $row['last_update'] . "</td>";
        echo "<td>" . $row['last_price'] . "</td>";
        echo "<td>" . $row['variationL'] . "</td>";
        echo "<td>" . $row['variationD'] . "</td>";

        echo "</tr>";
    }

    echo "</table>";
}

if (isset($_POST["full_download"])) {

    // Connect to the DB

    $connect = mysqli_connect("rendertech.com", $_SESSION['uname_long'], $_SESSION['psw'], "pupone_Summarizer");
    if (!$connect) {
        die('Could not connect: ' . mysqli_error());
    }

}
    // doesn't work...
    
    /*
    $output = ""; 
    $sql = mysqli_query($connect,"SELECT * FROM main_table");
    $columns_total = mysqli_num_fields($sql);

// Get The Field Name

    for ($i = 0; $i < $columns_total; $i++) {
        $heading = mysqli_field_name($sql, $i);
        $output .= '"' . $heading . '",';
    }
    $output .= "\n";

// Get Records from the table

    while ($row = mysqli_fetch_array($sql)) {
        for ($i = 0; $i < $columns_total; $i++) {
            $output .= '"' . $row["$i"] . '",';
        }
        $output .= "\n";
    }

// Download the file

    $filename = "myFile.csv";
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename=' . $filename);

    echo $output;
    exit;

*/



// Works but outputs everything no column headers

/*
  // prepping for csv

  header ('Content-Type: text/csv; charset=utf-8');
  header ('Content-Disposition: attachment; filename = full_download.csv');
  $output = fopen("php://output", "w");

  //fputcsv($output, array());                                                        This is needed if you want to have the column headings

  // sql query
  $query = ("SELECT * FROM v_full_download;");
  $result = mysqli_query($connect, $query);

  while ($row = mysqli_fetch_assoc($result)){

  fputcsv($output, $row);
  }

  fclose($output);
 */
?>



<?php

// old code

/*
  //full download view of main_table

  if (isset($_POST["full_download"])) {
  $connect = mysqli_connect("rendertech.com", $_SESSION['uname_long'], $_SESSION['psw'], "pupone_Summarizer");
  if (!$connect) {
  die('Could not connect: ' . mysqli_error());
  }

  // move this to the end if successful.

  $currentuser = $_SESSION['uname'];
  $userAction = "downloaded current table";
  $log = "INSERT INTO activity (user, `action`) VALUES ('$currentuser','$userAction')";
  mysqli_query($connect, $log);

  $queryTable = "SELECT * FROM v_full_download;";
  if (!$queryResult = mysqli_query($connect, $queryTable)) {
  exit(mysqli_error($connect));
  }

  // prep for csv file.

  $users = array();
  if (mysqli_num_rows($queryResult) > 0) {
  while ($row = mysqli_fetch_assoc($queryResult)) {
  $users[] = $row;
  }
  }
  date_default_timezone_set('America/Chicago');
  $date = date("Y-m-d h:i:sa", time());
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=Full_View_Download' . $date . '.csv');
  $output = fopen('php://output', 'w');
  fputcsv($output, array("symbol", "industry", "sub_industry", "market_cap", "current_price", "pharma", "biotech", "penny_stock", "status", "catalysts", "last_earnings", "next_earnings", "bo_ah", "intern", "cash", "burn", "related_tickers", "analysis_date", "analysis_price", "variation1", "1st_price_target", "1st_upside", "2nd_price_target", "2nd_upside", "downside_risk", "rank", "confidence", "worst_case", "target_weight", "target_position", "actual_position", "actual_weight", "weight_difference", "strategy", "discussion", "notes", "last_update", "last_price", "variationL", "variationD"));
  if (count($users) > 0) {
  foreach ($users as $row) {
  if (!empty($row)) {
  fputcsv($output, $row);
  }
  }
  }
  };


  // regular download of main_table

  if (isset($_POST["reg_download"])) {
  $connect = mysqli_connect("rendertech.com", $_SESSION['uname_long'], $_SESSION['psw'], "pupone_Summarizer");
  if (!$connect) {
  die('Could not connect: ' . mysqli_error());
  }

  $currentuser = $_SESSION['uname'];
  $userAction = "downloaded current table";
  $log = "INSERT INTO activity (user, `action`) VALUES ('$currentuser','$userAction')";
  mysqli_query($connect, $log);

  $queryTable = "SELECT * FROM v_reg_download;";
  if (!$queryResult = mysqli_query($connect, $queryTable)) {
  exit(mysqli_error($connect));
  }

  // prep for csv file.

  $users = array();
  if (mysqli_num_rows($queryResult) > 0) {
  while ($row = mysqli_fetch_assoc($queryResult)) {
  $users[] = $row;
  }
  }
  date_default_timezone_set('America/Chicago');
  $date = date("Y-m-d h:i:sa", time());
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=Regular_Download_Table_' . $date . '.csv');
  $output = fopen('php://output', 'w');
  fputcsv($output, array("symbol", "industry", "sub_industry", "current_price", "biotech", "analysis_price", "1st_price_target", "2nd_price_target", "downside_risk", "rank", "confidence", "target_weight"));
  if (count($users) > 0) {
  foreach ($users as $row) {
  if (!empty($row)) {
  fputcsv($output, $row);
  }
  }
  }


  };

 */
?>
