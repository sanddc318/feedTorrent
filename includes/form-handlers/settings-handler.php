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
?>
