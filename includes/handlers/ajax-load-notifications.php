<?php
  include("../../config/config.php");
  include("../classes/User.php");
  include("../classes/Notification.php");


  $limit = 6;
  $notification = new Notification($con, $_REQUEST["loggedInUser"]);
  echo $notification->getNotifications($_REQUEST, $limit);
?>
