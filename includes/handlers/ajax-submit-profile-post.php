<?php
  require("../../config/config.php");
  include("../classes/User.php");
  include("../classes/Post.php");

  if (isset($_POST['post-body'])) {
    $post = new Post($con, $_POST['user-from']);
    $post->submitPost($_POST['post-body'], $_POST['user-to']);
  }
?>
