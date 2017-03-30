<?php include("includes/header.php"); ?>

<div class="main-column column" id="main-column">
  <h4>Friends Requests</h4>

  <?php
    $query = mysqli_query( $con, "SELECT * FROM friend_requests
                                  WHERE user_to = '$loggedInUser'" );

    if ( mysqli_num_rows($query) == 0 ) {
      echo "No friend request at this time :/";
    } else {

      while ( $row = mysqli_fetch_array($query) ) {
        $user_from = $row["user_from"];
        $user_from_obj = new User( $con, $user_from );
        echo $user_from_obj->getFirstAndLastName() . " sent you a friend request";

        $user_from_friend_array = $user_from_obj->getFriendsArray();

        // Accept request
        if ( isset($_POST["accept_request" . $user_from]) ) {
          $add_friend_query = mysqli_query( $con, "UPDATE users
                                                   SET friends_array =
                                                   CONCAT(friends_array, '$user_from,')
                                                   WHERE username = '$loggedInUser'" );

          $add_friend_query = mysqli_query( $con, "UPDATE users
                                                   SET friends_array =
                                                   CONCAT(friends_array, '$loggedInUser,')
                                                   WHERE username = '$user_from'" );

          $delete_query = mysqli_query( $con, "DELETE FROM friend_requests
                                               WHERE user_to = '$loggedInUser'
                                               AND user_from = '$user_from'" );
          echo "You are now friends!";
          header( "Location: requests.php" );
        }

        // Ignore request
        if ( isset($_POST["ignore_request" . $user_from]) ) {
          $delete_query = mysqli_query( $con, "DELETE FROM friend_requests
                                               WHERE user_to = '$loggedInUser'
                                               AND user_from = '$user_from'" );
          echo "Request ignored";
          header( "Location: requests.php" );
        }
  ?>

    <form action="requests.php" method="POST">
      <input class="success"
             id="accept-button"
             type="submit"
             name="accept_request<?php echo $user_from; ?>"
             value="Accept"
      >
      <input class="default"
             id="ignore-button"
             type="submit"
             name="ignore_request<?php echo $user_from; ?>"
             value="Ignore"
      >
    </form>

  <?php
      } // End while loop
    } // End if block
  ?>
</div>
