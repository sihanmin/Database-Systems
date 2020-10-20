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
            <h3>Add Reviews for Movie</h3>
            <form method="GET" action="Add_review.php" autocomplete="on">
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
                    echo "<option value=\"$row->id\"> $row->title ($row->year)</option>";
                  }
                  else
                  echo mysqli_error($db); 
                  $db->close();
                  ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="name">Enter the name </label>
                  <input type="text" name="review_name" class="form-control" maxlength="20" placeholder="Enter name here(max 20)" value="Anonymous">
                </div>
                <div class="form-group">
                  <label for="review">rating:</label><br>
                  <select class="form-control form-control-lg" name="rating">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                 </select>
                </div>
                <div class="form-group">
                  <label for="review">Enter the review below</label><br>
                  <textarea name="comments" class="form-control" rows="5" placeholder="Adding comments here (Maximum 500 words)" maxlength="500"></textarea>
                </div>
                <button type="submit" class="btn btn-default">Submit your review!</button>
              </form>
      <?php
        if(isset($_GET["m_id"],$_GET["review_name"],$_GET["rating"],$_GET["comments"])){
          $mid = $_GET["m_id"];
          $name = $_GET["review_name"];
          $rating = $_GET["rating"];
          $comments = $_GET["comments"];
          $db = new mysqli('localhost', 'cs143', '', 'CS143');
          if($db->connect_errno)
            die('Error connecting to the database');
          $timestamp = time();
          $time = date( 'Y-m-d H:i:s' , $timestamp);
          $sql = "INSERT INTO Review VALUES(\"$name\", \"$time\", $mid, $rating, \"$comments\");";
          $result = $db->query($sql);
          if($result){
            echo "Successfully added comment at time: $time";
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

