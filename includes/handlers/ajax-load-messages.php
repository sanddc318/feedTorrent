<?php
  include("../../config/config.php");
  include("../classes/User.php");
  include("../classes/Message.php");


  $limit = 6;
  $message = new Message($con, $_REQUEST["loggedInUser"]);
  echo $message->getConvosDropdown($_REQUEST, $limit);
?>
