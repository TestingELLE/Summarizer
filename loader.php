<?php
    ini_set('session.gc_maxlifetime',60*60*6);
    ini_set('session.gc_probability',1);
    ini_set('session.gc_divisor',1);
    session_start();
    //  echo $_SESSION['loggedin'];
    //  echo " ";
    //  echo $_SESSION["Last_Activity"];
    //  echo " ";
    //  echo time();
     if(!isset($_SESSION['loggedin'])){
       
      header("Location:logout.php");
      exit();
    }
    
    if($_SESSION['type']=="viewer"){
        
        echo "
        <script>
            alert('You do not have privilege to access the page. Please contact website manager.');
            window.location.href='Summarizer.php';
        </script>";   
    }
    if(isset($_SESSION["break"]) || isset($_SESSION["symbolNotExists"]) || isset($_SESSION["duplicates"])){
        echo '
        <script>
            alert(`New symbols: '.$_SESSION["symbolNotExists"].'; Null value in field "symbol" in row:'.$_SESSION["break"].'; Duplicated symbols:'.$_SESSION["duplicates"].';`);
        </script>
        ';
        $_SESSION["break"]=null;
        $_SESSION["symbolNotExists"]=null;
        $_SESSION["duplicates"]=null;
        echo '
        <script>
        window.location.href="loader.php";
        </script>
        ';  
    };
  

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Document</title>
    <style type="text/css">
		body {
			font-size: 15px;
			color: #343d44;
			font-family: "segoe-ui", "open-sans", tahoma, arial;
			padding: 0;
			margin: 0;
		}
		table {
			margin: auto;
			font-family: "Lucida Sans Unicode", "Lucida Grande", "Segoe Ui";
			font-size: 12px;
		}

		h1 {
			margin: 25px auto 0;
			text-align: center;
			text-transform: uppercase;
			font-size: 17px;
		}

		table td {
			transition: all .5s;
		}
		
		/* Table */
		.data-table {
			border-collapse: collapse;
			font-size: 14px;
			min-width: 537px;
		}

		.data-table th, 
		.data-table td {
			border: 1px solid #e1edff;
			padding: 7px 17px;
		}
		.data-table caption {
			margin: 7px;
		}

		/* Table Header */
		.data-table thead th {
			background-color: #508abb;
			color: #FFFFFF;
			border-color: #6ea1cc !important;
			text-transform: uppercase;
		}

		/* Table Body */
		.data-table tbody td {
			color: #353535;
		}
		.data-table tbody td:first-child,
		.data-table tbody td:nth-child(4),
		.data-table tbody td:last-child {
			text-align: right;
		}

		.data-table tbody tr:nth-child(odd) td {
			background-color: #f4fbff;
		}
		.data-table tbody tr:hover td {
			background-color: #ffffa2;
			border-color: #ffff0f;
		}

		/* Table Footer */
		.data-table tfoot th {
			background-color: #e5f5ff;
			text-align: right;
		}
		.data-table tfoot th:first-child {
			text-align: left;
		}
		.data-table tbody td:empty
		{
			background-color: #ffcccc;
		}
	</style>
</head>
<body>
    <form name="form" action="export.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" accept=".csv">
        <input type="submit" name="submit" value="Truncate and load new file">
        <input type="submit" name="update" value="Update">
        <input type="submit" name="append" value="Append">
    </form>
    <form method="POST" action="export.php">
        <input type="submit" name="download" value="Download table as CSV file">
    </form>
    <hr>
    <button><a href="Summarizer.php">Main Page</a></button>
<?php
if($_SESSION['type']=="Admin" || $_SESSION["type"]=="Programmer"){
    echo '
    <div id="mytable">
        <h1>Backup Table</h1>
        <table class="data-table">
            <caption class="title">Backup table Data</caption>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>User</th>
                    <th>Filename</th>
                    <th>DATE</th>
                    <th>Locked</th>
                    <th>Deleted</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
        ';
    }
        ?>
            <?php
            $connect=mysqli_connect("rendertech.com","pupone_Runhao","Runhao1212","pupone_Summarizer");
            if (!$connect)
            {
            die('Could not connect: ' . mysqli_error());
            }
            $query="SELECT * FROM backup_table";
            if($result = mysqli_query($connect,$query))
            {
            while ($row = mysqli_fetch_array($result))
            {   
                if($row['locked']==1){
                    $row['locked']="YES";
                }
                else{
                    $row['locked']="NO";
                }
                if($row['deleted']==1){
                    $row['deleted']="YES";
                }
                else{
                    $row['deleted']="NO";
                }
                if($_SESSION["type"]=="Admin"){
                echo '<tr>
                        <td>'.$row['id'].'</td>
                        <td>'.$row['user'].'</td>
                        <td id='.'deleteTB'.$row['id'].'>'.$row['filename'].'</td>
                        <td>'.$row['date'].'</td>
                        <td id='.$row['id'].'>'.$row['locked'].'</td>
                        <td id='.'delete'.$row['id'].'>'.$row['deleted'].'</td>
                        <td><input class="lockRow" data-id='.$row['id'].' type="button" name="edit" value="Lock"><input class="unlockRow" data-id='.$row['id'].' type="button" name="edit" value="Unlock"><input class="delete" data-id='.$row['id'].' type="button" name="edit" value="Delete"></td>
                    </tr>
                ';
                }else if($_SESSION["type"]=="Programmer"){
                    echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['user'].'</td>
                    <td id='.'deleteTB'.$row['id'].'>'.$row['filename'].'</td>
                    <td>'.$row['date'].'</td>
                    <td id='.$row['id'].'>'.$row['locked'].'</td>
                    <td id='.'delete'.$row['id'].'>'.$row['deleted'].'</td>
                    <td><input class="delete" data-id='.$row['id'].' type="button" name="edit" value="Delete"></td>
                </tr>
            ';
                }
            }
        }?>
            </tbody>
        </table>
    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> 
<?php
    if(isset($_POST["id"])) {
    $id=$_POST["id"];
    $editSQL="UPDATE backup_table SET locked=1 WHERE id=$id";
    mysqli_query($connect,$editSQL);
    }
    if(isset($_POST["unlockId"])) {
        $unlockId=$_POST["unlockId"];
        $editSQL="UPDATE backup_table SET locked=0 WHERE id=$unlockId";
        mysqli_query($connect,$editSQL);
    }
    if($_SESSION["type"]=="Programmer"){
        if(isset($_POST["deleteId"])) {
                $unlockId=$_POST["deleteId"];
                $newID=$_POST["newID"];
            
                $checkStatus="SELECT * FROM backup_table WHERE id=$newID limit 1";
                if($result = mysqli_query($connect,$checkStatus))
                {
                    while ($row = mysqli_fetch_array($result))
                    { 
                        if($row["locked"]==1){

                        }
                        if($row["locked"]==0){
                            $updateSQL="UPDATE backup_table SET `deleted`=1 WHERE id=$newID";
                            $editSQL="DROP TABLE `$unlockId`";
                            mysqli_query($connect,$editSQL);
                            mysqli_query($connect,$updateSQL);
                        }
                    
            
                    }
                }
        };
    }
    if($_SESSION["type"]=="Admin"){
        if(isset($_POST["deleteId"])) {
            $unlockId=$_POST["deleteId"];
            $newID=$_POST["newID"];
        
            $checkStatus="SELECT * FROM backup_table WHERE id=$newID limit 1";
           
            $updateSQL="UPDATE backup_table SET `deleted`=1 WHERE id=$newID";
            $editSQL="DROP TABLE `$unlockId`";
            mysqli_query($connect,$editSQL);
            mysqli_query($connect,$updateSQL);
        }
                
        
    };
    
    
    echo '
    <script>
        $(document).on("click",".lockRow",function(){
            let id=$(this).attr("data-id")
            console.log(id)
        $.ajax({
            url:"loader.php",
            type:"POST",
            data:{"id":id}
        }).then(function(data){
                $(`#${id}`).text("YES")
        })
            
        })
        $(document).on("click",".unlockRow",function(){
            let unlockId=$(this).attr("data-id")
        $.ajax({
            url:"loader.php",
            type:"POST",
            data:{"unlockId":unlockId}
        }).then(function(data){
                $(`#${unlockId}`).text("NO")
        })
            
        })
    </script>
    ';
    if($_SESSION['type']=="Programmer"){
        echo '
        <script>
            $(document).on("click",".delete",function(){
                let id=$(this).attr("data-id");
                let deleteId="deleteTB"+$(this).attr("data-id")
                var fileName=$(`#${deleteId}`).text().trim()
                console.log(fileName)
            $.ajax({
                url:"loader.php",
                type:"POST",
                data:{"deleteId":fileName,
                      "newID":id}
            }).then(function(data){
                if($(`#delete${id}`).text()=="YES"){
                    alert("The table is alreadly deleted");
                }else{
                    if($(`#${id}`).text().trim()=="YES"){
                        alert("The table is locked");
                    }
                    else{
                        $(`#delete${id}`).text("YES");
                    }
                }
            })
                
            })
        </script>
        ';
    };
    if($_SESSION['type']=="Admin"){
        echo '
        <script>
            $(document).on("click",".delete",function(){
                let id=$(this).attr("data-id");
                let deleteId="deleteTB"+$(this).attr("data-id")
                var fileName=$(`#${deleteId}`).text().trim()
                console.log(fileName)
            $.ajax({
                url:"loader.php",
                type:"POST",
                data:{"deleteId":fileName,
                        "newID":id}
            }).then(function(data){
                if($(`#delete${id}`).text()=="YES"){
                    alert("The table is alreadly deleted");
                }else{
                     $(`#delete${id}`).text("YES");
                }
            })
                
            })
        </script>
        ';
    };

?>
    
 
</body>
</html>