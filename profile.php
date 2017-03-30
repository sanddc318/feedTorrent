<?php
  include("includes/header.php");

  if ( isset($_GET["profile_username"]) ) {
    $username = $_GET["profile_username"];
    $user_details_query = mysqli_query( $con, "SELECT * FROM users WHERE username = '$username'" );
    $user_array = mysqli_fetch_array( $user_details_query );
    $num_friends = ( substr_count($user_array["friends_array"], ",") ) - 1; // Make into a function?
  }

  if ( isset($_POST["remove-friend"]) ) {
    $user = new User( $con, $loggedInUser );
    $user->removeFriend( $username );
  }

  if ( isset($_POST["add-friend"]) ) {
    $user = new User( $con, $loggedInUser );
    $user->sendRequest( $username );
  }

  if ( isset($_POST["respond-request"]) ) {
    header( "Location: requests.php" );
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title></title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="profile-left">
      <img src="<?php echo $user_array['profile_pic']; ?>" alt="">

      <div class="profile-info">
        <p><?php echo "Posts: " . $user_array["num_posts"]; ?></p>
        <p><?php echo "Likes: " . $user_array["num_likes"]; ?></p>
        <p><?php echo "Friends: " . $num_friends; ?></p>
      </div>

      <form action="<?php echo $username; ?>" method="POST">
        <?php
          $profile_user_obj = new User( $con, $username );

          if ( $profile_user_obj->isClosed() ) {
            header( "Location: user-closed.php" );
          }


          $logged_in_user_obj = new User( $con, $loggedInUser );

          if ( $loggedInUser != $username ) {

            if ( $logged_in_user_obj->isFriend($username) ) {
              echo "<input type='submit'
                           name='remove-friend'
                           class='profile-button danger'
                           value='Remove Friend'
                    > <br>";
            } else if ( $logged_in_user_obj->didReceiveRequest($username) ) {
              echo "<input type='submit'
                           name='respond-request'
                           class='profile-button warning'
                           value='Respond'
                    > <br>";
            } else if ( $logged_in_user_obj->didSendRequest($username) ) {
              echo "<input type='submit'
                           name=''
                           class='profile-button default'
                           value='Request Sent'
                    > <br>";
            } else {
              echo "<input type='submit'
                           name='add-friend'
                           class='profile-button success'
                           value='Add Friend'
                    > <br>";
            }

          }
        ?>
      </form>
      <!-- Post button -->
      <input class="primary"
             type="submit"
             data-toggle="modal"
             data-target="#post-form"
             value="Post Something"
      >
    </div> <!-- /.profile-left -->

    <!-- Feed -->
    <div class="profile-main-column column">
      <div class="posts-area"></div>
      <img id="loading"
          src="assets/images/icons/spinner.gif"
          alt="Loading icon"
          style="width: 100%;"
      >
    </div>

    <!-- Modal -->
    <div class="modal fade" id="post-form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span     aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Post Something</h4>
          </div>

          <div class="modal-body">
            <p>This will appear on the user's profile page and also their newsfeed for your friends to see!</p>
            <form class="profile-post" action="" method="POST">
              <div class="form-group">
                <textarea class="form-control" name="post-body"></textarea>
                <input type="hidden" name="user-from" value="<?php echo $loggedInUser; ?>">
                <input type="hidden" name="user-to" value="<?php echo $username; ?>">
              </div>
            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button id="submit-profile-post" type="button" class="btn btn-primary" name="post-button">Post</button>
          </div>
        </div>
      </div>
    </div> <!-- End modal -->


    <script>
      var loggedInUser = "<?php echo $loggedInUser; ?>";
      var profileUsername = "<?php echo $username; ?>"

      $(document).ready( function() {
        $("#loading").show();

        // Original Ajax request for loading initial set of posts
        $.ajax({
          url: "includes/handlers/ajax-load-profile-posts.php",
          type: "POST",
          data: "page=1&loggedInUser=" + loggedInUser + "&profileUsername=" + profileUsername,
          cache: false,
          success: function(data) {
            $("#loading").hide();
            $(".posts-area").html(data);
          }
        });

        $(window).scroll( function() {
          var height = $(".posts-area").height();
          var scroll_top = $(this).scrollTop();
          var page = $(".posts-area").find(".nextPage").val();
          var noMorePosts = $(".posts-area").find(".noMorePosts").val();

          if ( (document.body.scrollHeight == document.body.scrollTop + window.innerHeight) &&
                noMorePosts == "false" ) {
            $("#loading").show();

            var ajaxReq = $.ajax({
              url: "includes/handlers/ajax-load-profile-posts.php",
              type: "POST",
              data: "page=" + page + "&loggedInUser=" + loggedInUser + "&profileUsername=" + profileUsername,
              cache: false,
              success: function(response) {
                $(".posts-area").find(".nextPage").remove(); // Removes current .nextPage
                $(".posts-area").find(".noMorePosts").remove(); // Removes current .noMorePosts
                $("#loading").hide();
                $(".posts-area").append(response);
              }
            });
          } // End if block
          return false;

        }); // End window.scroll func
      });
  </script>


  </div> <!-- /.wrapper (header.php) -->

</body>
</html>
