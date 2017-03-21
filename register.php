<?php
  require( "config/config.php" );
  require( "includes/form-handlers/register-handler.php" );
  require( "includes/form-handlers/login-handler.php" );
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Swirlfeed | Sign Up</title>
  <link rel="stylesheet" href="assets/css/register-style.css">
</head>
<body>
  <div class="wrapper">
    <div class="login-box">
      <div class="login-header">
        <h1>Swirlfeed</h1>
        <span>Login or signup below</span>
      </div>
      <!-- Login Form -->
      <form action="register.php" method="POST">
        <input type="email" name="log_email" placeholder="Email Address"
              value="<?php
                if ( isset($_SESSION["log_email"]) )
                  echo $_SESSION["log_email"];
              ?>"
        required>
        <br>
        <input type="password" name="log_password" placeholder="Password" required>
        <br>

        <?php
        if ( in_array("Email and/or password incorrect <br>", $error_array) )
            echo "Email and/or password incorrect <br>";
        ?>

        <input type="submit" name="login_button" value="Login">
      </form>

      <hr>

      <!-- Register Form -->
      <form action="register.php" method="POST">
        <!-- First name -->
        <input type="text" name="reg_fname" placeholder="First Name"
              value="<?php
                if ( isset($_SESSION["reg_fname"]) )
                  echo $_SESSION["reg_fname"];
              ?>"
        required>
        <br>
        <?php if ( in_array("Your first name must be between 2 and 25 characters <br>", $error_array) )
                echo "Your first name must be between 2 and 25 characters <br>"; ?>
        <!-- Last name -->
        <input type="text" name="reg_lname" placeholder="Last Name"
              value="<?php
                if ( isset($_SESSION["reg_lname"]) )
                  echo $_SESSION["reg_lname"];
              ?>"
        required>
        <br>
        <?php if ( in_array("Your last name must be between 2 and 25 characters <br>", $error_array) )
                echo "Your last name must be between 2 and 25 characters <br>"; ?>


        <!-- Email -->
        <input type="email" name="reg_email" placeholder="Email"
              value="<?php
                if ( isset($_SESSION["reg_email"]) )
                  echo $_SESSION["reg_email"];
              ?>"
        required>
        <br>
        <!-- Confirm email -->
        <input type="email" name="reg_email2" placeholder="Confirm Email"
              value="<?php
                if ( isset($_SESSION["reg_email2"]) )
                  echo $_SESSION["reg_email2"];
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
        <br>

        <?php
        if (
          in_array("<span style='color: #14c800'>You're all set! Go ahead and login!</span>", $error_array)
          )
            echo "<span style='color: #14c800'>You're all set! Go ahead and login!</span>";
        ?>
      </form>
    </div> <!-- /.login-box -->
  </div> <!-- /.wrapper -->


</body>
</html>
