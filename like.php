<?php
  require("config/config.php");
  include("includes/classes/User.php");
  include("includes/classes/Post.php");

  if ( isset($_SESSION["username"]) ) {
    $loggedInUser = $_SESSION["username"];
    $user_details_query = mysqli_query( $con, "SELECT * FROM users WHERE username = '$loggedInUser'" );
    $user = mysqli_fetch_array( $user_details_query );
  } else {
    header( "Location: register.php" );
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title></title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <?php
    // Get id of post
    if ( isset($_GET["post_id"]) ) {
      $post_id = $_GET["post_id"];
    }

    $get_likes = mysqli_query( $con, "SELECT likes, added_by FROM posts
                                      WHERE id = '$post_id'" );
    $row = mysqli_fetch_array( $get_likes );
    $total_likes = $row["likes"];
    $user_liked = $row["added_by"];


    $user_details_query = mysqli_query( $con, "SELECT * FROM users
                                               WHERE username = '$user_liked'" );
    $row = mysqli_fetch_array( $user_details_query );
    $total_user_likes = $row["num_likes"];

    // Like button
    if ( isset($_POST["like-button"]) ) {
      $total_likes++;
      $query = mysqli_query( $con, "UPDATE posts
                                    SET likes = '$total_likes'
                                    WHERE id = '$post_id'" );
      $total_user_likes++;
      $user_likes = mysqli_query( $con, "UPDATE users
                                         SET num_likes = '$total_user_likes'
                                         WHERE username = '$user_liked'" );
      $insert_user = mysqli_query( $con, "INSERT INTO likes
                                           VALUES ('', '$loggedInUser', '$post_id')" );

      // Insert notification
    }

    // Unlike button
    if ( isset($_POST["unlike-button"]) ) {
      $total_likes--;
      $query = mysqli_query( $con, "UPDATE posts
                                    SET likes = '$total_likes'
                                    WHERE id = '$post_id'" );
      $total_user_likes--;
      $user_likes = mysqli_query( $con, "UPDATE users
                                         SET num_likes = '$total_user_likes'
                                         WHERE username = '$user_liked'" );
      $insert_user = mysqli_query( $con, "DELETE FROM likes
                                          WHERE username = '$loggedInUser'
                                          AND post_id = '$post_id'" );
    }



    // Check for previous likes
    $check_query = mysqli_query( $con, "SELECT * FROM likes
                                        WHERE username = '$loggedInUser'
                                        AND post_id = '$post_id'" );
    $num_rows = mysqli_num_rows( $check_query );

    if ( $num_rows > 0 ) {
      echo "<form action='like.php?post_id=" . $post_id . "'method='POST'>
              <input type='submit' class='comment-like' name='unlike-button' value='Unlike'>
              <div class='like_value'>
                " . $total_likes . " Likes
              </div>
            </form>";
    } else {
      echo "<form action='like.php?post_id=" . $post_id . "'method='POST'>
              <input type='submit' class='comment-like' name='like-button' value='Like'>
              <div class='like_value'>
                " . $total_likes . " Likes
              </div>
            </form>";
    }
  ?>

</body>
</html>
