<?php
   
  session_start();
       
  if(!isset($_SESSION['uname'])){
      
   
    header("Location:logout.php");
    exit();
}
  if(isset($_GET["symbolSearch"])){
    $name=$_GET["symbolSearch"];
    header("Location:symbol.php?name=$name");
  };
 $user=$_SESSION['uname'];
 $type=$_SESSION['type'];
?>
<html lang="en">
    <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


      <script src="http://mbostock.github.com/d3/d3.v2.js"></script>
  		<style>
                    /*To be deleted?*/
  			/* tell the SVG path to be a thin blue line without any area fill */
                        
  			/*path {
  				stroke: steelblue;
  				stroke-width: 3;
  				fill: none;
  			}
  			.axis {
  			  shape-rendering: crispEdges;
  			}
  			.x.axis line {
  			  stroke: black;
  			}
  			.x.axis .minor {
  			  stroke-opacity: .5;
  			}
  			.x.axis path {

  			}
  			.y.axis line, .y.axis path {
  			  fill: none;
  			  stroke: #000;
  			}    */ 
  		</style>
<!-- jQuery library -->
<!-- <script src="SummarizerJS.js"></script> -->

<!-- Latest compiled JavaScript -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="symbol_styleSheet.css">
    </head>
    <body>
      <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
          <div class="navbar-header">
          </div>
          <ul class="nav navbar-nav">
              <p><?php echo $user ?></p>
     
              <p><?php echo $type ?></p>
              <button class="btn btn-default btn-md"><a href="logout.php">Log Out</a></button>
              <input id="back" class="btn btn-default btn-md"type="button" value="Main Page" onClick="Search()">
              <input class="btn btn-default btn-md" id="save"type="button" value="Save">
          </ul>
          <form class="navbar-form navbar-right"  action="symbol.php" method="GET" role="search" >
            <div class="input-group">
            <div class="input-group-btn">
                <input class="btn btn-default btn-sm" type="submit" name="submit" value="Go To">
              </div>
              <input   type="text" class="form-control" name="symbolSearch" placeholder="Search">
            </div>
          </form>
        </div>
      </nav>
      <div id="main-content">
      <br>
          <?php
          //check if user wanna redirect to other symbol page from the current symbol page
            if(isset($_GET["name"])){
              //get the symbol from the user input
              $name=$_GET["name"];
            };
            $con1=mysqli_connect("rendertech.com",$_SESSION['uname_long'],$_SESSION['psw'],"pupone_Summarizer");
            if (!$con1)
              {
              die('Could not connect: ' . mysqli_error());
              }
             
            mysqli_select_db($con1,"pupone_Summarizer");
            //select data from db for the searched symbol
            if($result2 = mysqli_query($con1,"SELECT * FROM main_table WHERE symbol='".$name."'"))
            {
              
              if(mysqli_num_rows($result2)==0){
                echo "<br><h3>Your input does not exist!</h3>";
              }else{
                /* pull data from database and insert into data table. */
                while($row1 = mysqli_fetch_array($result2))
                {
                  ?>
      <h4 style="margin-top:-20px"">15 minute delay</h4>
      <h2 style="text-align:center;display: inline;margin-left:24%" id="mysymbol"><a href="https://seekingalpha.com/symbol/'.$row1['symbol'].'/chart" onclick="javascript:void window.open(`https://seekingalpha.com/symbol/'.$row1['symbol'].'/chart`,`1520620719413`,`width=920,height=1200,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=200px,top=100px`);return false;"><span id="link" style="margin-left: -54px; margin-right: 20px;"  class="glyphicon glyphicon-picture" aria-hidden="true"></span></a><?= $row1['symbol'] ?></h2>
                   
                   <h4 style="display: inline;margin-left:22px"><a contenteditable="true" id="mktCap"><?= $row1['market_cap'] ?></a></h4>
                  
                   <h4 style="display: inline;margin-left:22px"><a contenteditable="true" id="industry"><?= $row1['industry'] ?></a></h4> 
                  
                   <ul style="float:right;margin-right:180px;margin-top:-5px">
                        <li style="list-style-type: none;"><h4 style="display: inline;margin-right:10%">Penny Stock: <a contenteditable="true" id="PStock"><?= $row1['penny_stock'] ?></a></h4></li> 
                         
                        <li style="list-style-type: none;"><h4 style="display: inline;margin-right:10%">Biotech: <a contenteditable="true" id="biotech"><?= $row1['biotech'] ?></a></h4></li>   
                         
                        <li style="list-style-type: none;"><h4 style="display: inline;margin-right:10%">Active: <a contenteditable="true" id="active"><?= $row1['active'] ?></a></h4></li>                     
                   </ul>
                   
                  <div style="clear:both"></div>
                  <div id="graph" class="aGraph" style="text-align:center;background-color:lightgrey;"></div>
        
                    <table class="table table-striped" style="text-align:left;">
                      <tbody>
                        <tr>
                            
                          <td><h4 style="display: inline;margin-right:10%">Last Earnings Date: <a id="LDate" contenteditable="true"><?= $row1['last_earnings'] ?></a></h4></td> 
                            
                          <td><h4 style="display: inline;margin-right:10%">Current Price ($): <a contenteditable="true" id="price"><?= $row1['current_price'] ?></a></h4></td>
                            
                           <td><h4 style="display: inline;margin-right:10%">Target Weight: <a contenteditable="true" id="Tweight"><?= $row1['target_weight'] ?></a></h4></td>
                      
                          <td><h4 style="display: inline;margin-right:10%">Cash: <a contenteditable="true" id="cash"><?= $row1['cash'] ?></a></h4></td>    
                        </tr>
                        <tr>
                        
                          <td><h4 style="display: inline;margin-right:10%">Next Earnings Date: <a contenteditable="true" id="NDate"><?= $row1['next_earnings'] ?></a><a style="margin-left:4px" contenteditable="true" id="boah"><?= " ".$row1['bo_ah'] ?></a></h4></td> 
                          
                   
                          <td><h4 style="display: inline;margin-right:10%">1st Price Target : <a contenteditable="true" id="PTarget"><?= $row1['1st_price_target'] ?></a></h4></td>
                          <td><h4 style="display: inline;margin-right:10%">Actual Weight: <a contenteditable="true" id="actualWeight"><?=$row1['actual_weight'] ?></a></h4></td>
                                 
                          <td><h4 style="display: inline;margin-right:10%">Burn: <a contenteditable="true" id="burn"><?=$row1['burn'] ?></a></h4></td>
                        </tr>
                        <tr>
                            
                          <td><h4 style="display: inline;margin-right:10%">Last Update: <a contenteditable="true" id="LUpdate"><?= $row1['last_update'] ?></a></h4></td> 
                          <td><h4 style="display: inline;margin-right:10%">1st Upside: <a contenteditable="true" id="upside"><?= $row1['1st_upside'] ?></a></h4></td>
                          <td><h4 style="display: inline;margin-right:10%">Weight Difference: <a contenteditable="true" id="diff"><?= $row1['weight_difference'] ?></a></h4></td> 
                          <td></td> 
                        </tr>
                        <tr>
                          <td><h4></h4></td>
                          <td><h4></h4></td>
                          <td><h4></h4></td>
                          <td><h4></h4></td>
                        </tr>
                        
                        <tr>
                          <td><h4 style="display: inline;margin-right:10%">Analysis Date: <a contenteditable="true" id="AnalysisDate"><?= $row1['analysis_date'] ?></a></h4></td>
                          <td><h4 style="display: inline;margin-right:10%">2nd Price Target : <a contenteditable="true" id="2ndPTarget"><?= $row1['2nd_price_target'] ?></a></h4></td>
                          <td></td>
                          <td><h4 style="display: inline;margin-right:10%">Rank: <a contenteditable="true" id="rank"><?= $row1['rank'] ?></a></h4></td>  
                        </tr>
           
                        <tr>
                          <td><h4 style="display: inline;margin-right:10%">Analysis Price: <a contenteditable="true" id="analysisPrice"><?= $row1['analysis_price'] ?></a></h4></td> 
                           <td><h4 style="display: inline;margin-right:10%">2nd Upside: <a contenteditable="true" id="2ndupside"><?= $row1['2nd_upside'] ?></a></h4></td> 
                         <td></td>
                          <td><h4 style="display: inline;margin-right:10%"> Confidence: <a contenteditable="true" id="confidence"><?= $row1['confidence'] ?></a></td>
                        </tr>
                        
                        <tr>
                          <td><h4 style="display: inline;margin-right:10%">Variation: <a contenteditable="true" id="LTarget"><?= $row1['variation'] ?></a></h4></td>
                          <td><h4 style="display: inline;margin-right:10%">Down Risk: <a contenteditable="true" id="down"><?= $row1['downside_risk'] ?></a></h4></td>
                          <td><h4 style="display: inline;margin-right:10%">Target Position: <a contenteditable="true" id="Tposition"><?= $row1['target_position'] ?></a></h4></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td><h4></h4></td>
                          <td><h4></h4></td>
                          <td><h4></h4></td>
                          <td><h4></h4></td>

                        </tr>
                        
                        <tr>
                          <td><h4 style="display: inline;margin-right:10%">Intern: <a id="intern" contenteditable="true"><?= $row1['intern'] ?></a></h4></td>
                          <td></td>
                          <td><h4 style="display: inline;margin-right:10%">Actual Position: <a contenteditable="true" id="actualPosition"><?= $row1['actual_position'] ?></a></h4></td> 
                          <td></td>
                        </tr>
                      </tbody>
                    
                    </table>
                    <button id="btn1"><span id="first1" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>Discussion</button>
                    <button id="btn2" style="margin-left:50px"><span id="second1" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Worst Case</button>
                    <button id="btn3" style="margin-left:50px"><span id="third1" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Catalysts</button>
                    <button id="btn4" style="margin-left:50px"><span id="forth1" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Related Tickers</button>
                    <button id="btn5" style="margin-left:50px"><span id="fifth1" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Strategy</button>
                    <h4 id="question"><span id="first" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Discussion:</h4>
                       
                    <textarea id="question1"contenteditable="true" style="width:95%;height:100px;display:none"><?= $row1['discussion'] ?>
                    </textarea>
                
                    <h4 id="case"><span id="second" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Worst Case:</h4>
                    <textarea id="case1"contenteditable="true" style="width:95%;height:100px;display:none"><?= $row1['worst_case'] ?> </textarea>
                    <h4 id="catalyst"><span id="third" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Catalysts:</h4>
                    <textarea id="catalyst1" contenteditable="true" style="width:95%;height:100px;display:none"><?= $row1['catalysts'] ?></textarea>
               

                    <h4 id="ticket"><span id="forth" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Related Tickers:</h4>
                    <textarea id="ticket1" contenteditable="true" style="width:95%;height:100px;display:none"><?= $row1['related_tickers'] ?>
                    </textarea>
                    
                    <h4 id="stra"><span id="fifth" class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Strategy:</h4>
                    <textarea id="stra1" contenteditable="true" style="width:95%;height:100px;display:none"><?= $row1['strategy'] ?>
                    </textarea>
                    
                        
                    <h4 id="note">Notes:</h4>
                    <div class="textarea" id="note1" contenteditable="true"><?= $row1['notes'] ?></div>
              
              <?php 
                
                }
                mysqli_free_result($result2);
          }
            mysqli_close($con1);
        }
      
          if($_SESSION["type"]=="viewer"){
            //if the user type is viewer, set all fields as readonly
            echo "
            <script>
              $('a').attr('contenteditable','false');
              $('textarea').attr('readonly','readonly');
              $('#note1').attr('contenteditable','false');
            </script>
            ";
          }
        ?>
      </div>
      <!-- script for hiding and displaying discussion, worst case .... -->
        <script src="sym.js"></script>
        <!-- script for saving symbol data on user click -->
        <script src="save.js"></script>
        <script>
          // API call to get the current stock price
            var symbol=$("#mysymbol").text()
            
            var firstPriceTarget=$("#PTarget").text()
            $.get(`https://api.iextrading.com/1.0/stock/${symbol}/price`, function (data1){
                    $("#price").text(" "+data1)
                    $("#upside").text(Math.round((firstPriceTarget/data1-1)*100)+"%")
                  })
                     
            $.get(`https://api.iextrading.com/1.0/stock/${symbol}/stats`, function (data){
              $("#mktCap").text((+data["marketcap"]/1000000).toFixed(2)+"M");
                }); 
            setInterval(function(){ 
              $.get(`https://api.iextrading.com/1.0/stock/${symbol}/price`, function (data){
                    $("#price").text(" "+data);
                    $("#upside").text(Math.round((firstPriceTarget/data-1)*100)+"%")
                    }) 
              }, 60000);
              // $("#clear").on("click",function(){
              //   $("#userComment").val("");
              // })
        </script>
    </body>
</html>



