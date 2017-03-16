<?php
  session_start();


  $con = mysqli_connect( "localhost", "root", "root", "social" );

  if ( mysqli_connect_errno() ) {
    echo "Failed to connect: " . mysqli_connect_errno() . "<br>";
  }

  // Declaring variables to prevent errors
  $fname = "";
  $lname = "";
  $email = "";
  $email2 = "";
  $password = "";
  $password2 = "";
  $date = ""; // Date user signed up
  $error_array = ""; // Holds error messages

  if ( isset($_POST["reg_button"]) ) {
    $fname = strip_tags( $_POST["reg_fname"] ); // Removes all HTML tags
    $fname = str_replace( " ", "", $fname ); // Removes any whitespace
    $fname = ucfirst( strtolower($fname) ); // Capitalize the return value
    $_SESSION["reg_fname"] = $fname; // Stores value into session variable

    $lname = strip_tags( $_POST["reg_lname"] );
    $lname = str_replace( " ", "", $lname );
    $lname = ucfirst( strtolower($lname) );
    $_SESSION["reg_lname"] = $lname;

    $email = strip_tags( $_POST["reg_email"] );
    $email = str_replace( " ", "", $email );
    $email = ucfirst( strtolower($email) );
    $_SESSION["reg_email"] = $email;

    $email2 = strip_tags( $_POST["reg_email2"] );
    $email2 = str_replace( " ", "", $email2 );
    $email2 = ucfirst( strtolower($email2) );
    $_SESSION["reg_email2"] = $email2;

    $password = strip_tags( $_POST["reg_password"] );
    $_SESSION["reg_password"] = $password;

    $password2 = strip_tags( $_POST["reg_password2"] );
    $_SESSION["reg_password2"] = $password2;

    $date = date( "Y-m-d" ); // Gets current date and formats it ( e.g. 2017-03-16 )

    // Email validation ------
    if ( $email == $email2 ) {

      // Check if email has a valid extension...
      if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        // ...Now check if email already exists
        $email_check = mysqli_query( $con, "SELECT email FROM users WHERE email = '$email'"  );
        $num_rows = mysqli_num_rows( $email_check );

        if ( $num_rows > 0 ) {
          echo "Email already exists <br>";
        } // End unique email check

      } else {
        echo "Invalid format <br>";
      }

    } else {
      echo "Emails don't match <br>";
    } // End email validation ------


    // First name & last name validation ------
    if ( strlen($fname) > 25 || strlen($fname) < 2 ) {
      echo "Your first name must be between 2 and 25 characters <br>";
    }

    if ( strlen($lname) > 25 || strlen($lname) < 2 ) {
      echo "Your last name must be between 2 and 25 characters <br>";
    }


    // Password validation ------
    if ( $password != $password2 ) {
      echo "Your passwords don't match <br>";
    } else {

      // Regex for making sure password uses English
      if ( preg_match("/[^A-Za-z0-9]/", $password) ) {
        echo "Your password can only contain English characters and numbers";
      }

    }

    if ( strlen($password > 30) || strlen($password < 5) ) {
      echo "Your password must be between 5 and 30 characters";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Swirlfeed | Sign Up</title>
</head>
<body>
  <form action="register.php" method="POST">
    <input type="text" name="reg_fname" placeholder="First Name"
          value="<?php
            if ( isset($_SESSION["reg_fname"]) ) {
              echo $_SESSION["reg_fname"];
            }
          ?>"
    required>
    <br>
    <input type="text" name="reg_lname" placeholder="Last Name"
          value="<?php
            if ( isset($_SESSION["reg_lname"]) ) {
              echo $_SESSION["reg_lname"];
            }
          ?>"
    required>
    <br>
    <input type="email" name="reg_email" placeholder="Email"
          value="<?php
            if ( isset($_SESSION["reg_email"]) ) {
              echo $_SESSION["reg_email"];
            }
          ?>"
    required>
    <br>
    <input type="email" name="reg_email2" placeholder="Confirm Email"
          value="<?php
            if ( isset($_SESSION["reg_email2"]) ) {
              echo $_SESSION["reg_email2"];
            }
          ?>"
    required>
    <br>
    <input type="password" name="reg_password" placeholder="Password" required>
    <br>
    <input type="password" name="reg_password2" placeholder="Confirm Password" required>
    <br>
    <input type="submit" name="reg_button" value="Register">
  </form>


</body>
</html>
