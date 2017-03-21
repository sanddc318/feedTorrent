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
  <link href="https://fonts.googleapis.com/css?family=Merienda+One" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/register-style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
  <script src="assets/js/register.js"></script>
</head>
<body>
  <?php
    // If there are errors upon registering, keep showing the reg form
    if ( isset($_POST["reg_button"]) ) {
      echo '
        <script>
          $(document).ready( function() {
            $("#first").hide();
            $("#second").show();
          } )
        </script>
      ';
    }
  ?>

  <div class="wrapper">
    <div class="login-box">
      <div class="login-header">
        <h1>Swirlfeed!</h1>
        <span>Login or signup below</span>
      </div>

      <div id="first">
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
              echo "<span style='color: orangered; font-size: 13px'>
                Email and/or password incorrect
              </span><br>";
          ?>

          <input type="submit" name="login_button" value="Login">
          <br>
          <a href="#" id="signup" class="signup">Need an account? Register here</a>
        </form>
      </div> <!-- /#first -->


      <div id="second">
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
                  echo "<span style='color: orangered; font-size: 13px'>
                    Your first name must be between 2 and 25 characters
                  </span><br>"; ?>
          <!-- Last name -->
          <input type="text" name="reg_lname" placeholder="Last Name"
                value="<?php
                  if ( isset($_SESSION["reg_lname"]) )
                    echo $_SESSION["reg_lname"];
                ?>"
          required>
          <br>
          <?php if ( in_array("Your last name must be between 2 and 25 characters <br>", $error_array) )
                  echo "<span style='color: orangered; font-size: 13px'>
                    Your last name must be between 2 and 25 characters
                  </span><br>"; ?>


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
                  echo "<span style='color: orangered; font-size: 13px'>
                    Email already exists
                  </span><br>";
          else if ( in_array("Invalid format <br>", $error_array) )
                  echo "<span style='color: orangered; font-size: 13px'>
                    Invalid format
                  </span><br>";
          else if ( in_array("Emails don't match <br>", $error_array) )
                  echo "<span style='color: orangered; font-size: 13px'>
                    Emails don't match
                  </span><br>";
          ?>


          <!-- Password -->
          <input type="password" name="reg_password" placeholder="Password" required>
          <br>
          <!-- Confirm password -->
          <input type="password" name="reg_password2" placeholder="Confirm Password" required>
          <br>
          <?php if ( in_array("Your passwords don't match <br>", $error_array) )
                  echo "<span style='color: orangered; font-size: 13px'>
                    Your passwords don't match
                  </span><br>";
          else if (in_array("Your password can only contain English letters and numbers <br>", $error_array))
                  echo "<span style='color: orangered; font-size: 13px'>
                    Your password can only contain English letters and numbers
                  </span><br>";
          else if ( in_array("Your password must be between 5 and 30 characters <br>", $error_array) )
                  echo "<span style='color: orangered; font-size: 13px'>
                    Your password must be between 5 and 30 characters
                  </span><br>";
          ?>

          <input type="submit" name="reg_button" value="Register">
          <br>

          <?php
          if (
            in_array("You're all set! Go ahead and login!", $error_array)
            )
              echo "<span style='color: green; font-size: 13px'>
                You're all set! Go ahead and login!
              </span><br>";
          ?>
          <br>

          <a href="#" id="signin" class="signin">Already have an account? Login here</a>
        </form>
      </div> <!-- /#second -->

    </div> <!-- /.login-box -->
  </div> <!-- /.wrapper -->
</body>
</html>
