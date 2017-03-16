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
  $error_array = array(); // Holds error messages

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
    $_SESSION["reg_email"] = $email;

    $email2 = strip_tags( $_POST["reg_email2"] );
    $email2 = str_replace( " ", "", $email2 );
    $_SESSION["reg_email2"] = $email2;

    $password = strip_tags( $_POST["reg_password"] );
    $password2 = strip_tags( $_POST["reg_password2"] );

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
          array_push( $error_array, "Email already exists <br>" );
        } // End unique email check

      } else {
        array_push( $error_array, "Invalid format <br>" );
      }

    } else {
      array_push( $error_array, "Emails don't match <br>" );
    } // End email validation ------


    // First name & last name validation ------
    if ( strlen($fname) > 25 || strlen($fname) < 2 ) {
      array_push( $error_array, "Your first name must be between 2 and 25 characters <br>" );
    }

    if ( strlen($lname) > 25 || strlen($lname) < 2 ) {
      array_push( $error_array, "Your last name must be between 2 and 25 characters <br>" );
    }


    // Password validation ------
    if ( $password != $password2 ) {
      array_push( $error_array, "Your passwords don't match <br>" );
    } else {

      // Regex for making sure password uses English characters
      if ( preg_match("/[^A-Za-z0-9]/", $password) ) {
        array_push( $error_array, "Your password can only contain English letters and numbers <br>" );
      }

    }

    if ( strlen($password) > 30 || strlen($password) < 5 ) {
      array_push( $error_array, "Your password must be between 5 and 30 characters <br>" );
    }


    // If there are no errors
    if ( empty($error_array) ) {
      $password = md5( $password ); // Hash the password.

      // Generate username by concatenating first name and last name
      $username = strtolower( $fname . "_" . $lname );

      // Check if username already exists...
      $username_check = mysqli_query( $con, "SELECT username FROM users WHERE username = '$username'" );

      //...If it does, keep adding number to username until it is unique
      $i = 0;
      while ( mysqli_num_rows($username_check) != 0 ) {
        $i++;
        $username = $username . "_" . $i;
        $username_check = mysqli_query( $con, "SELECT username FROM users WHERE username = '$username'" );
      }

      // Assign profile picture
      $rand = rand( 1, 8 ); // Create a random number between 1 and 8

      switch ( $rand ) {
        case 1:
          $profile_pic = "assets/images/profile-pics/defaults/abstract.jpg";
          break;
        case 2:
          $profile_pic = "assets/images/profile-pics/defaults/emblem.jpg";
          break;
        case 3:
          $profile_pic = "assets/images/profile-pics/defaults/hexagon.jpg";
          break;
        case 4:
          $profile_pic = "assets/images/profile-pics/defaults/mushroom.jpg";
          break;
        case 5:
          $profile_pic = "assets/images/profile-pics/defaults/x.jpg";
          break;
        case 6:
          $profile_pic = "assets/images/profile-pics/defaults/smiley.jpg";
          break;
        case 7:
          $profile_pic = "assets/images/profile-pics/defaults/whale.jpg";
          break;
        default:
          $profile_pic = "assets/images/profile-pics/defaults/orange.jpg";
      }
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
    <!-- First name -->
    <input type="text" name="reg_fname" placeholder="First Name"
          value="<?php
            if ( isset($_SESSION["reg_fname"]) ) {
              echo $_SESSION["reg_fname"];
            }
          ?>"
    required>
    <br>
    <?php if ( in_array("Your first name must be between 2 and 25 characters <br>", $error_array) )
            echo "Your first name must be between 2 and 25 characters <br>"; ?>
    <!-- Last name -->
    <input type="text" name="reg_lname" placeholder="Last Name"
          value="<?php
            if ( isset($_SESSION["reg_lname"]) ) {
              echo $_SESSION["reg_lname"];
            }
          ?>"
    required>
    <br>
    <?php if ( in_array("Your last name must be between 2 and 25 characters <br>", $error_array) )
            echo "Your last name must be between 2 and 25 characters <br>"; ?>


    <!-- Email -->
    <input type="email" name="reg_email" placeholder="Email"
          value="<?php
            if ( isset($_SESSION["reg_email"]) ) {
              echo $_SESSION["reg_email"];
            }
          ?>"
    required>
    <br>
    <!-- Confirm email -->
    <input type="email" name="reg_email2" placeholder="Confirm Email"
          value="<?php
            if ( isset($_SESSION["reg_email2"]) ) {
              echo $_SESSION["reg_email2"];
            }
          ?>"
    required>
    <br>
    <?php if ( in_array("Email already exists <br>", $error_array) )
            echo "Email already exists <br>";
    else if ( in_array("Invalid format <br>", $error_array) )
            echo "Invalid format <br>";
    else if ( in_array("Emails don't match <br>", $error_array) )
            echo "Emails don't match <br>";
    ?>


    <!-- Password -->
    <input type="password" name="reg_password" placeholder="Password" required>
    <br>
    <!-- Confirm password -->
    <input type="password" name="reg_password2" placeholder="Confirm Password" required>
    <br>
    <?php if ( in_array("Your passwords don't match <br>", $error_array) )
            echo "Your passwords don't match <br>";
    else if ( in_array("Your password can only contain English letters and numbers <br>", $error_array) )
            echo "Your password can only contain English letters and numbers <br>";
    else if ( in_array("Your password must be between 5 and 30 characters <br>", $error_array) )
            echo "Your password must be between 5 and 30 characters <br>";
    ?>

    <input type="submit" name="reg_button" value="Register">
  </form>


</body>
</html>
