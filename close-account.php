<?php
  include("includes/header.php");


  if (isset($_POST["cancel"])) {
    header("Location: settings.php");
  }

  if (isset($_POST["close-account"])) {
    $close_query = mysqli_query($con, "UPDATE users SET user_closed = 'yes' WHERE username = '$loggedInUser'");
    session_destroy();
    header("Location: register.php");
  }
?>

<div class="main-column column">
  <h4>Close Account</h4>
  <p class="close-account-text">Are you sure you want to close your account?</p>
  <span class="close-account-text">
    Closing your account will hide your profile and all your activity from other users.
  </span> <br>
  <span class="close-account-text">
    You can re-open your account at anytime by simply logging back in.
  </span> <br>

  <form action="close-account.php" method="POST">
    <input type="submit" class="danger button" id="close-account" name="close-account" value="Yes! Close It Anyway">
    <input type="submit" class="info button" id="update-details-submit" name="cancel" value="Changed My Mind">
  </form>
</div>
