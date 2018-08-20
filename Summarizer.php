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
<link rel="stylesheet" type="text/css" href="Summarizer.css">

</head>

  <body>
  
    <div style="width:80%;margin-left:auto;margin-right:auto">
      <nav class="navbar navbar-default navbar-fixed-top" style="display:inline" >
        <div class="container"> 
          <!--<p style="display:inline"> <?= ''.$user.'' ?></p>-->
          <ul style="display:inline-block;float:left;margin-left:-231px;list-style-type: none;margin-left:-50px">
              <li><?= ''.$user.'' ?></li>
              <li><?= ''.$type.''?></li>
          </ul>
          <button style="margin-left:50px;display:inline-block" ><a href="logout.php">Log Out</a></button>
          <button style="margin-top:5px;display:inline-block"><a href="loader.php">Loader</a></button>
          <h2 style="text-align:center; margin-top:-29px;margin-left:55px;margin-bottom:0px;width:991px">Summarizer</h2>
          
        <h6 style="margin-left:510px;margin-top:2px;margin-bottom:2px;font-size:xx-small">working prototype 1.1.8</h6>
        <h6 style="margin-left:500px;margin-top:2px;margin-bottom:8px;font-size:xx-small">Date Released: 2018-07-27</h6>

          <!--<p style="margin-top:-23px;"> <?= ''.$type.''?></p>-->
        </div>
          <form style="float:right;margin-right:20px;margin-top:-59px;padding:0px">
            <ul style="padding:0px;list-style-type: none">
            <li style="padding:0px"><input type="radio" value="active" checked="checked" name="active">
            <label for="active">Active</label>&nbsp;&nbsp;<input type="radio" value="X" name="X">
            <label for="X">X</label></li>
             <li style="padding:0px"><input type="radio" value="watch" name="watch">
            <label for="watch">Watch</label>&nbsp;&nbsp;<input type="radio" value="all" name="all">
            <label for="all">All</label></li>
            </ul>
        </form>
      </nav>
  
    <p style="text-align:center; margin-top:90px; margin-left:-37px"></p><!-- For some reason removing this element makes the search box disappear-->
    
    <table style="margin-right:10%;margin-left:-50px" id="SummarizerTable" class="table table-striped">
      <thead>
        <tr>
          <th>Symbol</th>
          <!--<th>Analysis Date</th>--->
          <th>Current Price ($)</th>
          <th>1st Price Target</th>
          <th>1st Upside</th>
          <th>2nd Price Target</th>
          <!--<th>Rank</th>-->
          <th>2nd Upside</th>
          <th>Last Analysis Price</th>
          <th>VariationL</th>
          <th>Target Weight</th>
          <th>Actual Weight</th>
          <th>Weight Difference</th>
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
          /*if($result = mysqli_query($con,"SELECT symbol, current_price, analysis_date, 1st_price_target, 1st_price_target / current_price - 1 AS 1st_upside, rank, target_weight,actual_weight,weight_difference,next_earnings FROM main_table"))*/
          if($result = mysqli_query($con,"SELECT symbol, current_price, 1st_price_target, 1st_price_target / current_price - 1 AS 1st_upside, 2nd_price_target, 2nd_price_target / current_price - 1 AS 2nd_upside, last_price, current_price - last_price AS variationL, actual_weight, target_weight, actual_weight - target_weight AS weight_difference, next_earnings FROM main_table where status = 'active';"))
          {
              /* pull data from database and insert into data table. */
              while($row = mysqli_fetch_array($result))
              {
                  /*$correct_format=preg_match_all('/(19|20)(\d{2})-(\d{1,2})-(\d{1,2})/', $row['analysis_date']);
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
                  $row['current_price']= number_format($row['current_price'],2,'.',',');*/
                  ?>  <tr>
                      <td ><a class="name" ><?= $row['symbol']?></a></td>
                          <td id="<?= $row['symbol']?>" style = "text-align: center"><?= $row['current_price']?></div></td>
                          <td id="pt<?= $row['symbol']?>" style = "text-align: center"><?= $row['1st_price_target']?></td>
                          <td id="upside<?= $row['symbol']?>" style = "text-align: center"><?= $row['1st_upside']?></td>
                          <td><div id="2ndPT<?= $row['symbol']?>" style ="text-align: center"> <?= $row['2nd_price_target']?></div></td>
                          <td id="2ndupside<?= $row['symbol']?>"><div style ="text-align: center"> <?= $row['2nd_upside']?></div></td>
                          <td><div id="LPrice<?=$row['symbol']?>" style = "text-align: center"><?= $row['last_price']?></div></td>
                          <td id="varL"><div id="varL<?=$row['symbol']?>" style ="text-align: center"> <?= $row['variationL']?></div></td>
                          <td><div style = "text-align: center"><?= $row['target_weight']?>%</div></td>
                          <td><div style = "text-align: center"><?= $row['actual_weight']?>%</div></td>
                          <td><div style = "text-align: center"><?= $row['weight_difference']?>%</div></td>
                          <td><div style = "text-align: center"><?= $row['next_earnings']?></div></td>
                        </tr>
                       <script>
                          /*$.get(`https://api.iextrading.com/1.0/stock/<?= $row['symbol'] ?>/price`, function (data){
                              $("#<?=$row['symbol']?>").text(" "+(Math.round(data*100)/100).toFixed(2));
                            var firstPriceTarget=$("#pt<?= $row['symbol'] ?>").text();
                            $("#upside<?=$row['symbol']?>").text(Math.round((firstPriceTarget/data-1)*100)+"%");
                             var secondPriceTarget=$("#2ndPT<?= $row['symbol']?>").text();
                             $("#2ndupside<?= $row['symbol']?>").text(Math.round((secondPriceTarget/data-1)*100)+"%");
                             var lastPrice=$("#LPrice<?=$row['symbol']?>").text();
                             $("#varL<?=$row['symbol']?>").text(Math.round((data/lastPrice-1)*100)+"%");
                            console.log(lastPrice);
                          });*/
                           $.ajax({// initial rendering of Symbol page with data from API
                    url: `https://api.iextrading.com/1.0/stock/<?= $row['symbol'] ?>/price`, //GET JSON object from url
                    type: 'GET',
                    success: function(data){
                            //$.post("Latest_price_into_Main.php",data);//sends to php page to update sql table with new price
                             $("#<?=$row['symbol']?>").text(" "+(Math.round(data*100)/100).toFixed(2));
                            var firstPriceTarget=$("#pt<?= $row['symbol'] ?>").text();
                            $("#upside<?=$row['symbol']?>").text(Math.round((firstPriceTarget/data-1)*100)+"%");
                             var secondPriceTarget=$("#2ndPT<?= $row['symbol']?>").text();
                             $("#2ndupside<?= $row['symbol']?>").text(Math.round((secondPriceTarget/data-1)*100)+"%");
                             var lastPrice=$("#LPrice<?=$row['symbol']?>").text();
                             if((lastPrice==="")||(lastPrice<=0))
                            {
                                 $("#varL<?=$row['symbol']?>").text("");
                            }
                            else{
                               
                                $("#varL<?=$row['symbol']?>").text(Math.round((data/lastPrice-1)*100)+"%"); 
                            }
            
                                },
                    error: function() {//if no JSON object is returned
                           //does nothing so that only data from sql table remains
                            }
                                });
                        setInterval(function(){ 
                             $.ajax({// initial rendering of Symbol page with data from API
                    url: `https://api.iextrading.com/1.0/stock/<?= $row['symbol'] ?>/price`, //GET JSON object from url
                    type: 'GET',
                    success: function(data){
                            //$.post("Latest_price_into_Main.php",data);//sends to php page to update sql table with new price
                             $("#<?=$row['symbol']?>").text(" "+(Math.round(data*100)/100).toFixed(2));
                            var firstPriceTarget=$("#pt<?= $row['symbol'] ?>").text();
                            $("#upside<?=$row['symbol']?>").text(Math.round((firstPriceTarget/data-1)*100)+"%");
                             var secondPriceTarget=$("#2ndPT<?= $row['symbol']?>").text();
                             $("#2ndupside<?= $row['symbol']?>").text(Math.round((secondPriceTarget/data-1)*100)+"%");
                             var lastPrice=$("#LPrice<?=$row['symbol']?>").text();
                             if((lastPrice==="")||(lastPrice<=0))
                            {
                                 $("#varL<?=$row['symbol']?>").text("");
                            }
                            else{
                               
                                $("#varL<?=$row['symbol']?>").text(Math.round((data/lastPrice-1)*100)+"%"); 
                            }
                            
                                },
                    error: function() {//if no JSON object is returned
                           //does nothing so that only data from sql table remains
                            }
                                }); 
                              }, 60000);
                         /*setInterval(function(){ 
                              $.get(`https://api.iextrading.com/1.0/stock/<?= $row['symbol']?> /price`, function (data){
                             $("#<?=$row['symbol']?>").text(" "+(Math.round(data*100)/100).toFixed(2));
                                    var firstPriceTarget=$("#pt<?= $row['symbol'] ?>").text();
                                $("#upside<?= $row['symbol'] ?>").text(Math.round((firstPriceTarget/data-1)*100)+"%");
                                 var secondPriceTarget=$("#2ndPT<?= $row['symbol']?>").text();
                             $("#2ndupside<?= $row['symbol']?>").text(Math.round((secondPriceTarget/data-1)*100)+"%");
                             var lastPrice=$("#LPrice<?=$row['symbol']?>").text();
                             $("#varL<?=$row['symbol']?>").text(Math.round((data/lastPrice-1)*100)+"%");
                                    }); 
                              }, 60000);*/
                        </script> 
                       <?php
                     
              }
              mysqli_free_result($result);
        }
          mysqli_close($con);
          
      
          ?>
    </tbody>
  </table>
</div>
<?php $_SESSION["selected_symbol"]=$_POST["symbol"] ?>
<footer style="text-align:center">

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
});
$(document).on("click", ".name", function() {
    var Symbol = $(this).text();  
    window.location.href = 'symbol.php?name='+Symbol; 
});

</script>
</body>
</html>
