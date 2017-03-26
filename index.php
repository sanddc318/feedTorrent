<?php include("includes/header.php"); ?>

  <div class="user-details column">
    <a href="#"><img src="<?php echo $user["profile_pic"] ?>"></a>
    <div class="user-details-leftcol-right">
      <span class="name">
        <a href="#"><?php echo $user["first_name"] . " " . $user["last_name"] . "<br>"; ?></a>
      </span>
      <?php
        echo "<em>#" . $user["username"] . "</em> <br><br>";
        echo "Posts: " . $user["num_posts"] . "<br>";
        echo "Likes: " . $user["num_likes"] . "<br>";
        echo "Friends: 0" . "<br>";
      ?>
    </div>
  </div>

  </div> <!-- /.wrapper (header.php) -->

</body>
</html>
