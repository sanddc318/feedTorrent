<?php
  $con = mysqli_connect( "localhost", "root", "root", "social" );

  if ( mysqli_connect_errno() ) {
    echo "Failed to connect: " . mysqli_connect_errno();
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

    $lname = strip_tags( $_POST["reg_lname"] );
    $lname = str_replace( " ", "", $lname );
    $lname = ucfirst( strtolower($lname) );

    $email = strip_tags( $_POST["reg_email"] );
    $email = str_replace( " ", "", $email );
    $email = ucfirst( strtolower($email) );

    $email2 = strip_tags( $_POST["reg_email2"] );
    $email2 = str_replace( " ", "", $email2 );
    $email2 = ucfirst( strtolower($email2) );

    $password = strip_tags( $_POST["reg_password"] );

    $password2 = strip_tags( $_POST["reg_password2"] );

    $date = date( "Y-m-d" ); // Gets current date and formats it ( e.g. 2017-03-16 )
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
    <input type="text" name="reg_fname" placeholder="First Name" required>
    <br>
    <input type="text" name="reg_lname" placeholder="Last Name" required>
    <br>
    <input type="email" name="reg_email" placeholder="Email" required>
    <br>
    <input type="email" name="reg_email2" placeholder="Confirm Email" required>
    <br>
    <input type="password" name="reg_password" placeholder="Password" required>
    <br>
    <input type="password" name="reg_password2" placeholder="Confirm Password" required>
    <br>
    <input type="submit" name="reg_button" value="Register" required>
  </form>


</body>
</html>
