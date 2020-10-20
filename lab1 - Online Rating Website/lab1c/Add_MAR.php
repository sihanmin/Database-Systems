<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add_MAR</title>
  <link href="bootstrap.min.css" rel="stylesheet">
  <link href="lab1c.css" rel="stylesheet">
  <style>

   
</style>
</head>
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
            <h3>Add Actors for Movie</h3>
            <form method="GET" action="Add_MAR.php" autocomplete="on">
                <div class="form-group">
                  <label for="movie">Movie Title</label><br>
                  <select class="form-control form-control-lg" name="m_id">
                  <?php
                  $db = new mysqli('localhost', 'cs143', '', 'CS143');
                  if($db->connect_errno)
                    die('Error connecting to the database');
                  $sql = "SELECT id, title, year FROM Movie ORDER BY title";
                  $result = $db->query($sql);
                  if($result){
                  while($row = $result->fetch_object())
                    echo "<option value='$row->id'> $row->title ($row->year)</option>";
                  }
                  else
                  echo mysqli_error($db); 
                  $db->close();
                  ?>
                  </select>
                </div>
                
                
                <div class="form-group">
                  <label for="actor">Actor Name(First Last)</label><br>
                  <select class="form-control form-control-lg" name="a_id">
                  <?php
                  $db = new mysqli('localhost', 'cs143', '', 'CS143');
                  if($db->connect_errno)
                    die('Error connecting to the database');
                  $sql = "SELECT id, last, first, dob FROM Actor ORDER BY first,last";
                  $result = $db->query($sql);
                  if($result){
                  while($row = $result->fetch_object())
                    echo "<option value='$row->id'>$row->first $row->last ($row->dob)</option>";
                  }
                  else
                  echo mysqli_error($db); 
                  $db->close();
                  ?>
                 </select>
                </div>
                <div class="form-group">
                  <label for="role">Actor's role </label>
                  <input type="text" name="role" class="form-control" maxlength="50" placeholder="Enter role here(max 50)">
                </div>
                

                <button type="submit" class="btn btn-default">Submit</button>
            </form>
<?php
  if(isset($_GET["m_id"],$_GET["a_id"],$_GET["role"])){
    $mid = $_GET["m_id"];
    $a_id = $_GET["a_id"];
    $role = $_GET["role"];
    if(strlen($role) == 0)
      die('Error: Actors should have a role.');

    $db = new mysqli('localhost', 'cs143', '', 'CS143');
    if($db->connect_errno)
      die('Error connecting to the database');
    $sql = "INSERT INTO MovieActor VALUES($mid,$a_id,'$role')";
    $result = $db->query($sql);
    if($result){
      echo "Successfully added actor with id: $a_id to movie with m_id: $mid, the role is : $role";
      }
      else
        echo mysqli_error($db); 
     $db->close();
    }
?>
        </div>
      </div>
    </div>
  


</body>
</html>

