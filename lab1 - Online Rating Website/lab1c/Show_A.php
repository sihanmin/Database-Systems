<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>CS143 Project 1c</title>

    <!-- Bootstrap -->
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="lab1c.css" rel="stylesheet">

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header navbar-defalt">
          <a class="navbar-brand" href="index.php" style="color: white">Movie Actor Query System</a>
        </div>
      </div>
    </nav>

    <div class="container">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li><a href="Add_AD.php">Add Actor/Director</a></li>
            <li><a href="Add_M.php">Add Movie Information</a></li>
            <li><a href="Add_MAR.php">Add Movie/Actor Relation</a></li>
            <li><a href="Add_MDR.php">Add Movie/Director Relation</a></li>
            <li><a href="Add_review.php">Add Movie review</a></li>
          </ul>
          
          <ul class="nav nav-sidebar">
            <p>&nbsp;&nbsp;Search Interface:</p>
            <li><a href="search.php">Search/Actor Movie</a></li>
          </ul>
        </div>
         <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h3><b> Actor Information Page :</b></h3>
          <?php
              if($_GET){
                $aid =$_GET["aid"];
              
              $db = new mysqli('localhost', 'cs143', '', 'CS143');
              $res = $db->query("SELECT id,sex,last,first,dob,dod FROM Actor WHERE id = $aid;");
              echo "<h4 style=\"margin-top:5%\"><b>Actor's info:</b></h4> <div class='table-responsive'> <table class='table table-bordered table-condensed table-hover'><thead> <tr><td>Name</td><td>Sex</td><td>Date of Birth</td><td>Date of Death</td></thead></tr><tbody>";
              $row = mysqli_fetch_row($res);

              if($row){
                $dod = $row[5];
                if($row[5] == NULL)
                  $dod ="Still Alive";

                echo "<tr><td> $row[3] $row[2]</td><td>$row[1]</td><td>$row[4]</td><td>$dod</td></tr>";

              }
              echo "</table></div>";

              echo "<h4><b>Actor's Movie:</b></h4> <div class='table-responsive'> <table class='table table-bordered table-condensed table-hover'><thead> <tr><td>Movie Name</td><td>Role</td></thead></tr><tbody>";

              $res = $db->query("SELECT mid, role FROM MovieActor WHERE aid = $aid;");
              while($row = mysqli_fetch_row($res)){
                $res1 = $db->query("SELECT title FROM Movie WHERE id = $row[0];");
                $row1 = mysqli_fetch_row($res1);
                echo "<tr><td><a href =\"Show_M.php?mid=$row[0]\">$row1[0]</a></td><td>$row[1]</td></tr>";
              }
              echo "</table></div>";
              

              $db->close();
            
            }
          ?>

        
        <label for="search_input">Search:</label>
          <form class="form-group" action="search.php" method ="GET" id="usrform">
              <input type="text" id="search_input" class="form-control" placeholder="Search..." name="search"><br>
              <input type="submit" value="GO" class="btn btn-default" style="margin-bottom:10px">
          </form>
          </div>
          
         </div>
      </div>
    </div>
  

</body>
</html>