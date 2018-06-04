<?php 
 
  session_start();

 if(!isset($_SESSION['uname'])){
   
  header("Location:logout.php");
  exit();
}

// if (isset($_SESSION["Last_Activity"]) && (time() - $_SESSION["Last_Activity"] >2880000)) {

//   header("Location:logout.php");

// }else{

//  $_SESSION["Last_Activity"] = time();
 
// }
 $user=$_SESSION['uname'];
 $type=$_SESSION['type'];
  
?>


<html lang="en">
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->

<!-- Latest compiled JavaScript -->
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css"> 
<link rel="stylesheet" type="text/css" href="summarizer_styleSheet.css">

</head>

  <body>
  
    <div id="outer">
      <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container"> 
            <p id="user_id"> <?= ''.$user.'' ?></p>
            <!-- <p style="display:inline"> <?php echo ''.$user.'' ?></p>  alternative 1 less compact-->
            <!--   <?php echo '<p style="display:inline">'.$user.'</p>' ?>  alternative 2 least compact-->
            
          <button id="logout_button" ><a href="logout.php">Log Out</a></button>
          <button id="loader_button"><a href="loader.php">Loader</a></button>
          <h2>Summarizer</h2>
         <!-- <p style="margin-top:-23px;"> <?php echo ''.$type.''?></p> -->
          <p id="user_type"> <?= ''.$type.''?></p> <!-- see above for alternatives -->
    
        </div>
      </nav>
  
    <p id="securities_notice">List of active securities</p>
    
    <table id="SummarizerTable" class="table table-striped table-condensed">
      <thead class="fixed-header">
        <tr>
          <th>Symbol</th>
          <th>Analysis Date</th>
          <th>Current Price ($)</th>
          <th>1st Price Target</th>
          <th>1st Upside</th>
          <th>Rank</th>
          <th>Target Weight</th>
          <th>Actual Weight</th>
          <th>Difference</th>
          <th>Next Earnings Date</th>
        </tr>
      </thead>
      <tbody> 
<?php
           $con=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
          if (!$con)
            {
            die('Could not connect: '.mysqli_error());
            } 

          mysqli_select_db($con,"pupone_Summarizer");
          if($result = mysqli_query($con,"SELECT symbol, current_price, analysis_date, 1st_price_target, 1st_upside, rank, target_weight,actual_weight,weight_difference,next_earnings FROM main_table"))
          {
              /* pull data from database and insert into data table. */
              while($row = mysqli_fetch_array($result))
              {
                 $correct_format=preg_match_all('/(19|20)(\d{2})-(\d{1,2})-(\d{1,2})/', $row['analysis_date']);
                  if($correct_format===0)
                  {
                    $row['analysis_date']=preg_replace('/(\d{1,2})[^a-zA-Z0-9](\d{1,2})[^a-zA-Z0-9]((19|20)?(\d{2}))/','20\3-\2-\1',$row['analysis_date']);
                  }
                  $correct_format1=preg_match_all('/(19|20)(\d{2})-(\d{1,2})-(\d{1,2})/', $row['next_earnings']);
                  if($correct_format1===0)
                  {
                    $row['next_earnings']=preg_replace('/(\d{1,2})[^a-zA-Z0-9](\d{1,2})[^a-zA-Z0-9]((19|20)?(\d{2}))/','20\3-\2-\1',$row['next_earnings']);
                  }
                  $row['current_price']=floatval($row['current_price']);
                  $row['current_price']= number_format($row['current_price'],2,'.',',');
                  echo'
                  <tr>
                          <td ><a  class="name" >'.$row['symbol'].'</a></td>
                          <td>'.$row['analysis_date'].'</td>
                          <td id="'.$row['symbol'].'">'.$row['current_price'].'</td>
                          <td id="pt'.$row['symbol'].'">'.$row['1st_price_target'].'</td>
                          <td id="upside'.$row['symbol'].'">'.$row['1st_upside'].'</td>
                          <td>'.$row['rank'].'</td>
                          <td>'.$row['target_weight'].'</td>
                          <td>'.$row['actual_weight'].'</td>
                          <td>'.$row['weight_difference'].'</td>
                          <td>'.$row['next_earnings'].'</td>
                        </tr>
                        <script>
                          $.get(`https://api.iextrading.com/1.0/stock/'.$row['symbol'].'/price`, function (data){
                            $("#'.$row['symbol'].'").text(" "+data);
                            var firstPriceTarget=$("#pt'.$row['symbol'].'").text()
                            $("#upside'.$row['symbol'].'").text(Math.round((firstPriceTarget/data-1)*100)+"%")
                          }) 
                         setInterval(function(){ 
                              $.get(`https://api.iextrading.com/1.0/stock/'.$row['symbol'].'/price`, function (data){
                                $("#'.$row['symbol'].'").text(" "+data);
                                    var firstPriceTarget=$("#pt'.$row['symbol'].'").text()
                                $("#upside'.$row['symbol'].'").text(Math.round((firstPriceTarget/data-1)*100)+"%")
                                    }); 
                              }, 60000);
                        </script> 
                       ';
              }
              mysqli_free_result($result);
        }
          mysqli_close($con);
          
      
         ?>
    </tbody>
  </table>
</div>
<?php $_SESSION["selected_symbol"]=$_POST["symbol"] ?>

<footer>
  <p>working prototype 1.1.2b</p>
  <p>Date Released: 2018-05-26</p>

  <nav>
  <a href="SummarizerLogin.html">Login</a>
  <a href="SummarizerStock.html">Stock</a>
  <a href="SummarizerTable.html">Table</a>
</nav>
</footer>

<script>
$(document).ready(function() {
$('#SummarizerTable').DataTable({
    paging: false
});
$(document).on("click", ".name", function() {
    var mySymbol = $(this).text();  
    window.location.href = 'symbol.php?name='+mySymbol; 
});
});
</script>
</body>
</html>


