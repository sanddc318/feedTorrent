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
  <!-- Googlefonts -->
  <link href="https://fonts.googleapis.com/css?family=Merienda+One" rel="stylesheet">
  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/css/styles.css">
  <!-- JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</head>
<body>
  <!-- Navigation bar -->
  <div class="top-bar">
    <div class="logo">
      <a href="index.php">Swirlfeed!</a>
    </div>
  </div>
