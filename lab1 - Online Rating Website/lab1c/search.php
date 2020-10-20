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
            <p>&nbsp;&nbsp;Add new content</p>
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
            <h3><b> Searching Page :</b></h3>
            <hr>
            <form method="GET" action="#">
                <div class="form-group">
                  <label for="search">Search:</label>
                  <input type="text" class="form-control" placeholder="Text input" name="search">
                </div>
                
                <button type="submit" class="btn btn-default">GO</button>
            </form>

            
<?php
  function search($keyword, $db, $type){
    if ($type == 'actor')
      $query = "SELECT id,last,first,dob FROM Actor WHERE";
    else if ($type == 'movie')
      $query = "SELECT id,title,year FROM Movie WHERE";

    foreach ($keyword as $key) {
      if ($type == 'actor')
        $query .= " (last LIKE '%$key%' OR first LIKE '%$key%') AND";
      else if ($type == 'movie')
        $query .= " title LIKE '%$key%' AND";
    }
    if (substr($query, -3) == 'AND')
      $query = substr($query, 0, -3);

    if ($type == 'actor')
      $query .= "ORDER BY last, first";
    else if ($type == 'movie')
      $query .= "ORDER BY title";
    $rs = $db->query($query);
    while($row = mysqli_fetch_row($rs)) {
      # print("<td>"."<a href=\"BrowseActor.php?actor_id=".$row[0]."\">".$row[1].",".$row[2]."</a>"."</td>");
      # <a href=" Show_A.php?identifier=12514 ">
      if ($type == 'actor')
        echo "<tr><td><a href =\"Show_A.php?aid=$row[0]\"> $row[2] $row[1]</a></td><td><a>$row[3]</a></td></tr>";
      else if ($type == 'movie')
        echo "<tr><td><a href =\"Show_M.php?mid=$row[0]\">$row[1]</a></td><td><a>$row[2]</a></td></tr>";
      #print_r($row);
      #echo "<br>";
    }
  }


  if (isset($_GET["search"])){
    $db = new mysqli('localhost', 'cs143', '', 'CS143');
    $search = $_GET["search"];
    $keyword = explode(' ', $search);

    echo "<h4><b>matching Actors are:</b></h4><div class='table-responsive'> <table class='table table-bordered table-condensed table-hover'><thead> <tr><td>Name</td><td>Date of Birth</td></thead></tr><tbody>";

    search($keyword, $db, 'actor');
    echo "</table></div>";

    echo "<h4><b>matching Movie are:</b></h4><div class='table-responsive'> <table class='table table-bordered table-condensed table-hover'><thead> <tr><td>title</td><td>year</td></thead></tr><tbody>";

    search($keyword, $db, 'movie');
    echo "</table></div>";


    $db->close();
  }
?>
        </div>
      </div>
    </div>
  


</body>
</html>
