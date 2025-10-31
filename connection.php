
<?php
  $servername = "192.168.1.103";
  $username = "giuliano";
  $password = "prepuzio";
  $db = "dbBarche";

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $db, 3306);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  else
  {
    $sql = "";
  }
?>