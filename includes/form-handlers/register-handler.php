<?php
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

      /* Everything should be good at this point.
        Now insert user into the table */
      $query = mysqli_query( $con, "INSERT INTO users
                                    VALUES ('', '$fname', '$lname', '$username', '$email', '$password', '$date', '$profile_pic', 0, 0, 'no', ',')"
                            );

      // Just in case there are syntax errors or something...
      if ( !$query ) {
        echo 'Invalid query: ' . mysqli_error($con);
      }

      // Everthing went well, let the user know
      array_push( $error_array, "You're all set! Go ahead and login!" );

      // Finally, clear the session
      $_SESSION["reg_fname"] = "";
      $_SESSION["reg_lname"] = "";
      $_SESSION["reg_email"] = "";
      $_SESSION["reg_email2"] = "";
    }

  }
?>
