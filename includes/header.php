<?php
  require("config/config.php");
  include("includes/classes/User.php");
  include("includes/classes/Post.php");
  include("includes/classes/Message.php");
  include("includes/classes/Notification.php");

  if ( isset($_SESSION["username"]) ) {
    $loggedInUser = $_SESSION["username"];
    $user_details_query = mysqli_query( $con, "SELECT * FROM users WHERE username = '$loggedInUser'" );
    $user = mysqli_fetch_array( $user_details_query );
    $num_friends = ( substr_count($user["friends_array"], ",") ) - 1; // Make into a function?
  } else {
    header( "Location: register.php" );
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to Swirlfeed</title>
  <!-- Googlefonts -->
  <link href="https://fonts.googleapis.com/css?family=Merienda+One" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/css/jquery.Jcrop.css">
  <link rel="stylesheet" href="assets/css/styles.css">
  <!-- JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/jcrop-bits.js"></script>
  <script src="assets/js/jquery-jcrop.js"></script>
  <script src="assets/js/bootbox.min.js"></script>
  <script src="assets/js/swirlfeed.js"></script>
</head>
<body>
  <!-- Navigation bar -->
  <div class="top-bar">
    <div class="logo">
      <a href="index.php">Swirlfeed!</a>
    </div>

    <div class="search">
      <form action="search.php" method="GET" name="search_form">
        <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $loggedInUser; ?>')"
               name="q" placeholder="Search..." autocomplete="off" id="search-text-input"
        >
        <div class="button-holder">
          <img src="assets/images/icons/search-icon.png">
        </div>
      </form>

      <div class="search-results">

      </div>

      <div class="search-results-footer-empty">

      </div>
    </div> <!-- /.search -->

    <nav>

      <?php
        // Unread messages
        $messages = new Message($con, $loggedInUser);
        $num_messages = $messages->getUnreadNumber();

        // Unread notifications
        $notifications = new Notification($con, $loggedInUser);
        $num_notifications = $notifications->getUnreadNumber();

        // Friend requests
        $user_obj = new User($con, $loggedInUser);
        $num_requests = $user_obj->getNumberOfFriendRequests();
      ?>

      <!-- Logged in user -->
      <a href="<?php echo $loggedInUser; ?>" class="user">
        <?php echo $user["first_name"]; ?>
      </a>
      <!-- Home -->
      <a href="index.php" class="nav-link"><i class="fa fa-home" aria-hidden="true"></i></a>
      <!-- Settings -->
      <a href="#" class="nav-link"><i class="fa fa-cog" aria-hidden="true"></i></a>
      <!-- Messages -->
      <a href="javascript:void(0)"
         onclick="getDropdownData('<?php echo $loggedInUser; ?>', 'message')"
         class="nav-link"
      >
        <i class="fa fa-envelope" aria-hidden="true"></i>
        <?php
          if ($num_messages > 0)
            echo '<span class="notification-badge" id="unread-message">' . $num_messages . '</span>';
        ?>
      </a>
      <!-- Notifications -->
      <a href="javascript:void(0)"
         onclick="getDropdownData('<?php echo $loggedInUser; ?>', 'notification')"
         class="nav-link"
      >
        <i class="fa fa-bell" aria-hidden="true"></i>
        <?php
          if ($num_notifications > 0)
            echo '<span class="notification-badge" id="unread-notification">' . $num_notifications . '</span>';
        ?>
      </a>
      <!-- Friend requests -->
      <a href="requests.php" class="nav-link">
        <i class="fa fa-users" aria-hidden="true"></i>
        <?php
          if ($num_requests > 0)
            echo '<span class="notification-badge" id="unread-requests">' . $num_requests . '</span>';
        ?>
      </a>
      <!-- Logout -->
      <a href="includes/handlers/logout.php" class="nav-link">
        <i class="fa fa-sign-out" aria-hidden="true"></i>
      </a>
    </nav>

    <div class="dropdown-data-window" style="height: 0px; border: none;">
      <input type="hidden" id="dropdown-data-type" value="">
    </div>

  </div>



  <script>
    var loggedInUser = "<?php echo $loggedInUser; ?>";

    $(document).ready(function() {
      $(".dropdown-data-window").scroll(function() {
        var inner_height = $(".dropdown-data-window").innerHeight();
        var scroll_top = $(".dropdown-data-window").scrollTop();
        var page = $(".dropdown-data-window").find(".nextPageDropdownData").val();
        var noMoreData = $(".dropdown-data-window").find(".noMoreDropdownData").val();

        if ((scroll_top + inner_height >= $(".dropdown-data-window")[0].scrollHeight) && noMoreData == "false" ) {
          var pageName; // Holds name of page to send ajax request to
          var type = $("#dropdown-data-type").val();

          if (type == "notification") {
            pageName = "ajax-load-notifications.php";
          } else if (type = "message") {
            pageName = "ajax-load-messages.php";
          }

          var ajaxReq = $.ajax({
            url: "includes/handlers/" + pageName,
            type: "POST",
            data: "page=" + page + "&loggedInUser=" + loggedInUser,
            cache: false,
            success: function(response) {
              $(".dropdown-data-window").find(".nextPageDropdownData").remove(); // Removes current .nextPage
              $(".dropdown-data-window").find(".noMoreDropdownData").remove(); // Removes current .noMorePosts
              $(".dropdown-data-window").append(response);
            }
          });
        } // End if block
        return false;

      }); // End window.scroll func
    });
  </script>



  <div class="wrapper">
