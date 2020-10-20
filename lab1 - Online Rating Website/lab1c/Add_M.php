<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CS143 Project 1c</title>
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
            <h3>Add new Movie</h3>
            <form method="GET" action="#">
                <div class="form-group">
                  <label for="title">Title</label>
                  <input type="text" class="form-control" placeholder="Text input" name="title">
                </div>
                <div class="form-group">
                  <label for="company">Company</label>
                  <input type="text" class="form-control" placeholder="Text input" name="company">
                </div>
                <div class="form-group">
                  <label for="year">Year</label>
                  <input type="text" class="form-control" placeholder="Text input" name="year">
                </div>
                <div class="form-group">
                    <label for="rating">MPAA Rating</label>
                    <select   class="form-control" name="rate">
                        <option value="G">G</option>
                        <option value="PG">PG</option>
                        <option value="PG-13">PG-13</option>
                        <option value="R">R</option>
                        <option value="NC-17">NC-17</option>
                        <option value="unknown">unknown</option>
                    </select>
                </div>
                <div class="form-group">
                    <label >Genre</label><br>
                    <input type="checkbox" name="genre[]" value="Action">Action</input>
                    <input type="checkbox" name="genre[]" value="Adult">Adult</input>
                    <input type="checkbox" name="genre[]" value="Adventure">Adventure</input>
                    <input type="checkbox" name="genre[]" value="Animation">Animation</input>
                    <input type="checkbox" name="genre[]" value="Comedy">Comedy</input>
                    <input type="checkbox" name="genre[]" value="Crime">Crime</input>
                    <input type="checkbox" name="genre[]" value="Documentary">Documentary</input>
                    <input type="checkbox" name="genre[]" value="Drama">Drama</input>
                    <input type="checkbox" name="genre[]" value="Family">Family</input>
                    <input type="checkbox" name="genre[]" value="Fantasy">Fantasy</input>
                    <input type="checkbox" name="genre[]" value="Horror">Horror</input>
                    <input type="checkbox" name="genre[]" value="Musical">Musical</input>
                    <input type="checkbox" name="genre[]" value="Mystery">Mystery</input>
                    <input type="checkbox" name="genre[]" value="Romance">Romance</input>
                    <input type="checkbox" name="genre[]" value="Sci-Fi">Sci-Fi</input>
                    <input type="checkbox" name="genre[]" value="Short">Short</input>
                    <input type="checkbox" name="genre[]" value="Thriller">Thriller</input>
                    <input type="checkbox" name="genre[]" value="War">War</input>
                    <input type="checkbox" name="genre[]" value="Western">Western</input>
                </div>
                <button type="submit" class="btn btn-default">ADD</button>
            </form>
<?php
  if (isset($_GET["title"],$_GET["company"],$_GET["year"]))
  {
    $title = $_GET["title"];
    $year = $_GET["year"];
    $company = $_GET["company"];
    $rating = $_GET["rate"];
    $genre = $_GET["genre"];

    echo "<br>";
    if($title == "" || strlen($title) > 100){
      echo "Please enter a valid movie title!<br>";
    }
    else if($year == "" || (int)$year < 1800){
      echo "Please enter a valid movie production year!<br>";
    }
    else if($company == "" ||strlen($company) > 50){
      echo "Please enter a valid movie company!<br>";
    }
    else{
      $db = new mysqli('localhost', 'cs143', '', 'CS143');
      if($db->connect_errno)
        die('Error connecting to the database');

      $rs = $db->query("SELECT id FROM MaxMovieID");
      $new_id = ($rs->fetch_assoc())['id'] + 1;


      $insert_movie = "INSERT INTO Movie VALUES($new_id,\"$title\",$year,\"$rating\",\"$company\");";

      if ($db->query($insert_movie)){
        echo("You've successfully added the movie \"$title\"!<br>");
        $db->query("UPDATE MaxMovieID SET id=$new_id");
      }
      else
        echo("There has being a problem adding the movie!<br>");

      for($i = 0; $i < count($genre); $i++){
        $insert_genre = "INSERT INTO MovieGenre VALUES($new_id,\"$genre[$i]\")";
        if($db->query($insert_genre))
          echo("You've successfully added genre $genre[$i] for the movie!<br>");
        else
          echo("There has being a problem adding the genre $genre[$i]!<br>");
      }

      $db->close();
    }

  }
?>
        </div>
      </div>
    </div>
  


</body>
</html>
