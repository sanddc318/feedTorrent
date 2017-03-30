<?php
  require("../../config/config.php");

  if (isset($_GET['post-id']))
    $post_id = $_GET['post-id'];


  if (isset($_POST['result'])) {
    if ($_POST['result'] == 'true')
      $query = mysqli_query($con, "UPDATE posts
                                   SET deleted = 'yes'
                                   WHERE id = '$post_id'");
  }
?>
