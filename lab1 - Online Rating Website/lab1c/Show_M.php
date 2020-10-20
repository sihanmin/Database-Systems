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
          <h3><b> Movie Information Page :</b></h3>
          <?php
              if($_GET){
                $mid = $_GET["mid"];
                $db = new mysqli('localhost', 'cs143', '', 'CS143');
                $res = $db->query("SELECT title,year,rating,company FROM Movie WHERE id = $mid;");
              echo "<h4 style=\"margin-top:5%\"><b>Movie's info:</b></h4> <div class='table-responsive'> <table class='table table-bordered table-condensed table-hover'><thead> <tr><td>title</td><td>director</td><td>year</td><td>MPRR rating</td><td>producer</td><td>genre</td></thead></tr><tbody>";
              $row = mysqli_fetch_row($res);

              $res1 = $db->query("SELECT did FROM MovieDirector WHERE mid = $mid;");

              $row1 = mysqli_fetch_row($res1);
              
              $res1 = $db->query("SELECT first, last FROM Director WHERE id = $row1[0];");
              $row1 = mysqli_fetch_row($res1);
              $res1 = $db->query("SELECT genre FROM MovieGenre WHERE mid = $mid;");
              while($row2 = mysqli_fetch_row($res1))
                $genre .=" $row2[0]";
              echo "<tr><td>$row[0]</td><td>$row1[0] $row1[1]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$genre</td></tr>";
              echo "</table></div>";

              echo "<h4 style=\"margin-top:2%\"><b>Actors in this Movie:</b></h4> <div class='table-responsive'> <table class='table table-bordered table-condensed table-hover'><thead> <tr><td>Name</td><td>Role</td></thead></tr><tbody>";
              $res = $db->query("SELECT aid, role FROM MovieActor WHERE mid = $mid;");
              while($row = mysqli_fetch_row($res)){
                $res1 = $db->query("SELECT first,last FROM Actor WHERE id = $row[0];");
                $row1 = mysqli_fetch_row($res1);
                echo "<tr><td><a href =\"Show_A.php?aid=$row[0]\">$row1[0] $row1[1]</a></td><td>$row[1]</td></tr>";
              }
              echo "</table></div>";

              echo "<h4 style=\"margin-top:2%\"><b>User review for the Movie:</b></h4>";
              $res = $db->query("SELECT AVG(rating), COUNT(*) FROM Review WHERE mid = $mid;");
              $row = mysqli_fetch_row($res);
              if($row[1] == 0)
                echo "<p>No one has left an review, you can add review below:</p>";
              else
                echo "<p>$row[1] user left their comments the average rating is $row[0]</p>";

                echo "<p><a href=\"Add_review.php\">Add Movie review by clicking this link</a></p>";

              if($row[1] != 0){
                echo "<h4 style=\"margin-top:2%\"><b>Details of User review:</b></h4>";
                $res = $db->query("SELECT * FROM Review WHERE mid = $mid;");
                while($row = mysqli_fetch_row($res)){
                  echo "<p>$row[0] at $row[1] gives the rating: $row[3], comments: $row[4]</p> ";

                }
              
              }





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