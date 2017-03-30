<?php
  require("config/config.php");
  include("includes/classes/User.php");
  include("includes/classes/Post.php");

  if ( isset($_SESSION["username"]) ) {
    $loggedInUser = $_SESSION["username"];
    $user_details_query = mysqli_query( $con, "SELECT * FROM users WHERE username = '$loggedInUser'" );
    $user = mysqli_fetch_array( $user_details_query );
    $num_friends = ( substr_count($user["friends_array"], ",") ) - 1; // Make into a function?
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
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/css/styles.css">
  <!-- JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/swirlfeed.js"></script>
</head>
<body>
  <!-- Navigation bar -->
  <div class="top-bar">
    <div class="logo">
      <a href="index.php">Swirlfeed!</a>
    </div>

    <nav>
      <a href="<?php echo $loggedInUser; ?>" class="user">
        <?php echo $user["first_name"]; ?>
      </a>
      <a href="index.php" class="nav-link"><i class="fa fa-home" aria-hidden="true"></i></a>
      <a href="#" class="nav-link"><i class="fa fa-envelope" aria-hidden="true"></i></a>
      <a href="#" class="nav-link"><i class="fa fa-cog" aria-hidden="true"></i></a>
      <a href="#" class="nav-link"><i class="fa fa-bell" aria-hidden="true"></i></a>
      <a href="requests.php" class="nav-link"><i class="fa fa-users" aria-hidden="true"></i></a>
      <a href="includes/handlers/logout.php" class="nav-link">
        <i class="fa fa-sign-out" aria-hidden="true"></i>
      </a>
    </nav>
  </div>

  <div class="wrapper">
