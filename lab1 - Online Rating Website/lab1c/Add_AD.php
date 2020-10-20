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
          <h2>Add new Actor/Director</h2>
          <p>This page is used to add Actor/Director info into Database</p>
            <form method = "GET" action="Add_AD.php">
              <div>
              <b>Select the identity:  </b><label class="radio-inline">
                <input type="radio" name="identity" checked="checked" value="Actor"/>Actor
              </label>
              <label class="radio-inline">
                <input type="radio" name="identity" value="Director"/>Director
              </label>
              </div>
              <div>
                <b>Select a gender:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><label class="radio-inline">
                    <input type="radio" name="sex" checked="checked" value="male">Male&nbsp;
                </label>
                <label class="radio-inline">
                    <input type="radio" name="sex" value="female">Female
                </label>
              </div><br>
                
                <div class="form-group">
                  <label for="first_name">First Name</label>
                  <input type="text" class="form-control" placeholder="Enter First name here" maxlength="20" name="fname"/>
                </div>
                
                <div class="form-group">
                  <label for="last_name">Last Name</label>
                  <input type="text" class="form-control" placeholder="Enter Last name here" maxlength="20" name="lname"/>
                </div>
                
                
                <div><b>Date of Birth:</b></div>
                <div class="form-group row" style="margin-top: -2%;">
                  <div class="col-md-2">
                    <label for="yob"></label>
                    <input type="text" class="form-control" maxlength="4" size="2"  placeholder="YYYY" name="doby">
                    </div>
                  <div class="col-md-1" style="margin-left: -2%;">
                    <label for="mob"></label>
                      <input type="text" class="form-control" maxlength="2" size="2" placeholder="MM" name="dobm">
                    </div>
                  <div class="col-md-1" style="margin-left: -2%;">
                    <label for="dob"></label>
                      <input type="text" class="form-control" maxlength="2" size="2" placeholder="DD" name="dobd">
                  </div>
                </div>
                <div><b>Date of Death:</b></div>
                <div class="form-group row" style="margin-top: -2%;">
                  <div class="col-md-2">
                    <label for="yod"></label>
                    <input type="text" class="form-control" maxlength="4" size="2"  placeholder="YYYY" name="dody">
                    </div>
                  <div class="col-md-1" style="margin-left: -2%;">
                    <label for="mod"></label>
                      <input type="text" class="form-control" maxlength="2" size="2" placeholder="MM" name="dodm">
                    </div>
                  <div class="col-md-1" style="margin-left: -2%;">
                    <label for="dod"></label>
                      <input type="text" class="form-control" maxlength="2" size="2" placeholder="DD" name="dodd">
                  </div>
                </div>
                <button type="submit" class="btn btn-default">ADD</button>
            </form>
            <?php
              function convertdate($year,$month,$day){
                return $year.$month.$day;
              }
              if (isset($_GET["fname"],$_GET["lname"],$_GET["doby"],$_GET["dobm"],$_GET["dobd"])){
                $first_name = $_GET["fname"];
                $last_name = $_GET["lname"];
                $birth_year = $_GET["doby"];
                $birth_month = $_GET["dobm"];
                $birth_day = $_GET["dobd"];
                $death_year = $_GET["dody"];
                $death_month = $_GET["dodm"];
                $death_day = $_GET["dodd"];
                $sex = $_GET["sex"];
                $identity = $_GET["identity"];

                echo "<br>";
                if($first_name == ""){
                  echo "Please enter a valid first name!<br>";
                }
                else if($last_name == ""){
                  echo "Please enter a valid last name!<br>";
                }
                else if(!checkdate($birth_month, $birth_day, $birth_year)){
                  echo "Invalid Date for birthday!<br>";
                }
                else{
                  $db = new mysqli('localhost', 'cs143', '', 'CS143');
                  if($db->connect_errno)
                    die('Error connecting to the database');
                  $rs = $db->query("SELECT id FROM MaxPersonID");
                  $new_id = ($rs->fetch_assoc())['id'] + 1;
                  $birthday = convertdate($birth_year,$birth_month,$birth_day);
                  $deathday = "NULL";
                  if($death_year!=""||$death_month!=""||$death_day!=""){
                      if(!checkdate($death_month, $death_day, $death_year))
                        die("invalid death date");
                      $deathday = convertdate($death_year,$death_month,$death_day);
                      if($deathday <= $birthday)
                        die("deathday cannot before birthday");
                    }
                  if($identity == "Director"){
                    $insert_query = "INSERT INTO Director VALUES($new_id,\"$last_name\",\"$first_name\",$birthday,$deathday);";
                  }
                  else
                    $insert_query = "INSERT INTO Actor VALUES($new_id,\"$last_name\",\"$first_name\",\"$sex\",$birthday,$deathday);";
                  if ($db->query($insert_query)){
                    echo("You've successfully added the actor/director $first_name $last_name!<br>");
                    $db->query("UPDATE MaxPersonID SET id=$new_id");
                  }
                  else
                      echo("There has being a problem adding the movie!<br>");

                   $db->close();
            }

        }
            ?>

             

        </div>
      </div>
    </div>

    
  

</body>
</html>

