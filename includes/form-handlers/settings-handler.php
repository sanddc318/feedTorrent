<?php
  // User details
  if (isset($_POST["update-details-submit"])) {
    $first_name = $_POST["first-name"];
    $last_name = $_POST["last-name"];
    $email = $_POST["email"];

    $email_check = mysqli_query($con, "SELECT * FROM users
                                       WHERE email = '$email'");
    $row = mysqli_fetch_array($email_check);
    $matched_user = $row["username"];


    if ($matched_user == "" || $matched_user == $loggedInUser) {
      $message = "<span style='color: green;'>Details updated!</span> <br>";
      $query = mysqli_query($con, "UPDATE users
                                   SET first_name = '$first_name', last_name = '$last_name', email = '$email'
                                   WHERE username = '$loggedInUser'");
    } else {
      $message = "That email is already in user <br>";
    }


  } else {
    $message = "";
  }



  // Change password
  if (isset($_POST["change-password-submit"])) {
    $old_password = strip_tags($_POST["old-password"]);
    $new_password = strip_tags($_POST["new-password"]);
    $new_password2 = strip_tags($_POST["new-password2"]);

    $password_query = mysqli_query($con, "SELECT password FROM users
                                       WHERE username = '$loggedInUser'");
    $row = mysqli_fetch_array($password_query);
    $db_password = $row["password"];


    if (md5($old_password) == $db_password) {

      if ($new_password == $new_password2) {


        if (strlen($new_password) <= 5) {
          $password_message = "Sorry, your new password must be greater than 5 characters. <br>";
        } else {
          $new_password_md5 = md5($new_password);
          $password_query = mysqli_query($con, "UPDATE users
                                                SET password = '$new_password_md5'
                                                WHERE username = '$loggedInUser'");
          $password_message = "Password has been updated! <br>";
        } // End if block new password is greater than 5


      } else {
        $password_message = "Your new passwords need to match! <br>";
      } // End if block new passwords match


    } else {
      $password_message = "The old password in incorrect! <br>";
    } // End if block old password matches db password


  } else {
    $password_message = "";
  }



  // Close user account
?>
