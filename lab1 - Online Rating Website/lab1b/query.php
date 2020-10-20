



<html>
  <head>
    <title>Query Interface </title>
  </head>

<body>
  <h1>Query Interface </h1>


  <p>
    <form action = "query.php" form method = "GET">
      Write down your SQL commends in the following box:<br>
      <textarea name="query" cols="60" rows="8">
      </textarea>
      <br />
      <input type ="submit" value ="Enter">
    </form>
  </p>
  

<table border=1 cellspacing = 1 cellpadding=2>
<?php

$db = new mysqli('localhost', 'cs143', '', 'CS143');

if($db->connect_errno > 0){
  die('Unable to connect to database [' . $db->connect_error . ']');
}

  if(isset($_GET["query"])){
    $query = $_GET["query"];
    $rs = $db -> query($query);

  $fieldinfo = mysqli_fetch_fields($rs);
  $field_num = mysqli_num_fields($rs);
  echo "<tr align=center>";
  foreach ($fieldinfo as $val){
    echo '<td><b>'.$val->name.'</b></td>';
  }
  
  echo "</tr>"; 
  while($row = mysqli_fetch_row($rs)){
    
    echo "<tr align=center>"; 

   
    $count = 0;
    while($count < $field_num){
      if($row[$count])
        echo  "<td> $row[$count]</td>";
      else
        echo "<td>N/A </td>";
      
      $count++;
    }
    echo "</tr>"; 
  }
   echo "<tr>"; 

  
  
  

 
  
  
  $rs->free();

}
$db->close();



?>
</table>
</body>
</html>






