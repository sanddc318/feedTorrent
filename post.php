<?php
  include("includes/header.php");


  if (isset($_GET["id"])) {
    $id = $_GET["id"];
  } else {
    $id = 0;
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
        echo "Friends: " . $num_friends . "<br>";
      ?>
    </div>
  </div>

  <div class="main-column column" id="main-column">
    <div class="posts-area">
      <?php
        $post = new Post($con, $loggedInUser);
        $post->getSinglePost($id);
      ?>
    </div>
  </div>
