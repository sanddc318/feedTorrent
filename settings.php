<?php
  include("includes/header.php");
  include("includes/form-handlers/settings-handler.php");
?>

<div class="main-column column">
  <h4>Account Settings</h4>

  <!-- User avatar -->
  <?php echo "<img src='" . $user["profile_pic"] . "' id='small-profile-pic'>"; ?>
  <br>
  <a href="upload.php">Change profile picture</a>
  <br><br><br>

  <!-- User details -->
  <h4>Modify the values and click 'Update Details'</h4>

  <?php
    $user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users
                                           WHERE username = '$loggedInUser'");
    $row = mysqli_fetch_array($user_data_query);
    $first_name = $row["first_name"];
    $last_name = $row["last_name"];
    $email = $row["email"];
  ?>

  <form action="settings.php" method="POST">
    First Name: <input type="text" name="first-name" value="<?php echo $first_name; ?>"> <br>
    Last Name: <input type="text" name="last-name" value="<?php echo $last_name; ?>"> <br>
    Email: <input type="text" name="email" value="<?php echo $email; ?>"> <br>

    <?php echo $message; ?>

    <input type="submit" id="save-details" name="update-details-submit" value="Update Details">
  </form>

  <!-- Password -->
  <h4>Change Password</h4>
  <form action="settings.php" method="POST">
    Old Password: <input type="password" name="old-password"> <br>
    New Password: <input type="password" name="new-password"> <br>
    Confirm New Password: <input type="password" name="new-password2"> <br>

    <?php echo $password_message; ?>

    <input type="submit" id="save-details" name="change-password-submit" value="Change Password">
  </form>

  <!-- Close Account -->
  <h4>Close Account</h4>
  <form action="settings.php" method="POST">
    <input type="submit" id="close-account" name="close-account-submit" value="Close Account">
  </form>
</div>
