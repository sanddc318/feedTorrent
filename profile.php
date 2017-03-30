<?php
  include("includes/header.php");

  if ( isset($_GET["profile_username"]) ) {
    $username = $_GET["profile_username"];
    $user_details_query = mysqli_query( $con, "SELECT * FROM users WHERE username = '$username'" );
    $user_array = mysqli_fetch_array( $user_details_query );
    $num_friends = ( substr_count($user_array["friends_array"], ",") ) - 1; // Make into a function?
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
  <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>

    <div class="profile-left">
      <img src="<?php echo $user_array['profile_pic']; ?>" alt="">

      <div class="profile-info">
        <p><?php echo "Posts: " . $user_array["num_posts"]; ?></p>
        <p><?php echo "Likes: " . $user_array["num_likes"]; ?></p>
        <p><?php echo "Friends: " . $num_friends; ?></p>
      </div>
    </div>

    <div class="main-column column">
      <?php echo $username; ?>
    </div>

  </div> <!-- /.wrapper (header.php) -->

</body>
</html>
