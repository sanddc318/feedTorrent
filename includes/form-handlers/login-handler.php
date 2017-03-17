<?php
  if ( isset($_POST["login_button"]) ) {
    $email = filter_var( $_POST["log_email"], FILTER_SANITIZE_EMAIL ); // Makes sure email format is valid
    $_SESSION["log_email"] = $email;

    $password = md5( $_POST["log_password"] ); // Compare apples to apples since password is hashed

    // Make sure email and password are in the database
    $check_database_query = mysqli_query( $con, "SELECT * FROM users WHERE email = '$email'
                                                AND password = '$password'" );
    $check_login_query = mysqli_num_rows( $check_database_query );

    // If it = 1, then a matching email and password combo has been found
    if ( $check_login_query == 1 ) {
      $row = mysqli_fetch_array( $check_database_query ); // Now the query results can be accessed

      $username = $row["username"];
      $_SESSION["username"] = $username;

      // Go to home page if all is well
      header( "Location: index.php" );
      exit();
    } else {
      array_push( $error_array, "Email and/or password incorrect <br>" );
    }
  }
?>
