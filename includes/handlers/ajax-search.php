<?php
  include("../../config/config.php");
  include("../../includes/classes/User.php");


  $query = $_POST["query"];
  $loggedInUser = $_POST["loggedInUser"];
  $names = explode(" ", $query);
?>
