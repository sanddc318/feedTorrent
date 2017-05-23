<?php
  ob_start();
  session_start();

  $timezone = date_default_timezone_set( "America/New_York" );

  $con = mysqli_connect( "us-cdbr-iron-east-03.cleardb.net", "b0044335d43a7d", "55e34de1", "heroku_26caad6e80323fa" );

  if ( mysqli_connect_errno() ) {
    echo "Failed to connect: " . mysqli_connect_errno() . "<br>";
  }
?>
