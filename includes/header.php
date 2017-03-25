<?php
  require("config/config.php");

  if ( isset($_SESSION["username"]) ) {
    $loggedInUser = $_SESSION["username"];
  } else {
    header( "Location: register.php" );
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to Swirlfeed</title>
</head>
<body>
