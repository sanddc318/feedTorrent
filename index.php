<?php
  include("includes/header.php");
  include("includes/classes/User.php");
  include("includes/classes/Post.php");

  if ( isset($_POST["post"]) ) {
    $post = new Post( $con, $loggedInUser );
    $post->submitPost( $_POST["post-text"], "none" );
  }
?>

  <div class="user-details column">
    <a href="<?php echo $loggedInUser; ?>">
      <img src="<?php echo $user["profile_pic"]; ?>">
    </a>

    <div class="user-details-leftcol-right">
      <span class="name">
        <a href="<?php echo $loggedInUser; ?>">
          <?php echo $user["first_name"] . " " . $user["last_name"] . "<br>"; ?>
        </a>
      </span>
      <?php
        echo "<em>#" . $user["username"] . "</em> <br><br>";
        echo "Posts: " . $user["num_posts"] . "<br>";
        echo "Likes: " . $user["num_likes"] . "<br>";
        echo "Friends: 0" . "<br>";
      ?>
    </div>
  </div>

  <div class="main-column column">
    <form class="post-form" action="index.php" method="POST">
      <textarea name="post-text" id="post-text" placeholder="Got something to say?"></textarea>
      <input type="submit" name="post" id="post_button" value="Post">
    </form>

    <?php
      $post = new Post( $con, $loggedInUser );
      $post->loadPostsFriends();
    ?>
  </div>

  </div> <!-- /.wrapper (header.php) -->

</body>
</html>
