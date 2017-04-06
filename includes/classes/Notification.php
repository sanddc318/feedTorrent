<?php
  class Notification {
    private $user_obj;
    private $con;

    public function __construct( $con, $user ) {
      $this->con = $con;
      $this->user_obj = new User( $con, $user );
    }



    public function getUnreadNumber() {
      $loggedInUser = $this->user_obj->getUsername();
      $query = mysqli_query($this->con, "SELECT * FROM notifications
                                         WHERE viewed = 'no'
                                         AND user_to = '$loggedInUser'");
      return mysqli_num_rows($query);
    }

    public function insertNotification($post_id, $user_to, $type) {
      $loggedInUser = $this->user_obj->getUsername();
      $loggedInUserName = $this->user_obj->getFirstAndLastName();
      $date_time = date("Y-m-d H:i:s");

      switch ($type) {
        case "post-comment":
          $message = $loggedInUserName . " commented on your post";
          break;
        case "post-like":
          $message = $loggedInUserName . " liked your post";
          break;
        case "profile-post":
          $message = $loggedInUserName . " posted on your profile";
          break;
        case "profile-comment":
          $message = $loggedInUserName . " commented on your profile post";
          break;
        case "comment-non-owner":
          $message = $loggedInUserName . " commented on a post you commented on";
          break;
        default:
          $message = "";
      }

      $link = "post.php?id=" . $post_id;
      $insert_query = mysqli_query($this->con, "INSERT INTO notifications
                                                VALUES ('', '$user_to', '$loggedInUser', '$message', '$link', '$date_time', 'no', 'no')");
    }

    public function getNotifications($data, $limit) {
      $page = $data["page"];
      $loggedInUser = $this->user_obj->getUsername();
      $return_str = "";

      if ($page == 1) {
        $start = 0;
      } else {
        $start = ($page - 1) * $limit;
      }

      $set_viewed_query = mysqli_query($this->con, "UPDATE notifications
                                                    SET viewed = 'yes'
                                                    WHERE user_to = '$loggedInUser'");

      $query = mysqli_query($this->con, "SELECT * FROM notifications
                                         WHERE user_to = '$loggedInUser'
                                         ORDER BY id DESC");

      if (mysqli_num_rows($query) == 0) {
        echo "You don't have any notifications";
        return;
      }

      $num_iterations = 0; // Number of messages checked
      $count = 1; // Number of messages posted

      while ($row = mysqli_fetch_array($query)) {

        if ($num_iterations++ < $start)
          continue;

        if ($count > $limit) {
          break;
        } else {
          $count++;
        }

        $user_from = $row["user_from"];
        $query = mysqli_query($this->con, "SELECT * FROM users
                                           WHERE username = '$user_from'");
        $user_data = mysqli_fetch_array($query);

        // Get a timestamp
        $date_time_now = date( "Y-m-d H:i:s" );
        $start_date = new Datetime( $row["date"] ); // Time of post creation
        $end_date = new Datetime( $date_time_now ); // Latest activity
        $interval = $start_date->diff( $end_date );

        if ( $interval->y >= 1 ) { // Years

              if ( $interval == 1 ) {
                $time_message = $interval->y . " year ago";
              } else {
                $time_message = $interval->y . " years ago";
              }

        } else if ( $interval->m >= 1 ) { // If at least a month old

              if ( $interval->d == 0 ) {
                $days = " ago"; // If exactly a month, just add "ago" (e.g. "4 months ago")
              } else if ( $interval->d == 1 ) {
                // Otherwise...
                $days = $interval->d . " day ago"; // "1 day ago"
              } else {
                $days = $interval->d . " days ago"; // "(n) days ago"
              }

              // Now concatenate the month(s) and day(s)
              if ( $interval->m == 1 ) {
                $time_message = $interval->m . " month," . $days; // e.g. "1 month, 6 days ago"
              } else {
                $time_message = $interval->m . " months," . $days; // e.g. "8 months, 1 day ago"
              }

        } else if ( $interval->d >= 1 ) {

              if ( $interval->d == 1 ) {
                $time_message = "Yesterday"; // If exactly one day, just say "yesterday"
              } else {
                $time_message = $interval->d . " days ago";
              }

        } else if ( $interval->h >= 1 ) { // Hours

              if ( $interval->h == 1 ) {
                $time_message = $interval->h . " hour ago";
              } else {
                $time_message = $interval->h . " hours ago";
              }

        } else if ( $interval->i >= 1 ) { // Minutes

              if ( $interval->i == 1 ) {
                $time_message = $interval->i . " minute ago";
              } else {
                $time_message = $interval->i . " minutes ago";
              }

        } else { // Seconds

              if ( $interval->s < 30 ) {
                $time_message = "Just now";
              } else {
                $time_message = $interval->s . " seconds ago";
              }

        } // End timestamp code






        $opened = $row["opened"];
        $style = ($row["opened"] == "no") ? "background-color: #ddedff;" : "";

        $return_str .= "<a href='" . $row['link'] . "'>
                          <div class='result-display result-display-notification' style='" . $style . "'>
                            <div class='notifications-profile-pic'>
                              <img src='" . $user_data['profile_pic'] . "'>
                            </div>

                            <p class='timestamp-smaller' id='gray'>" . $time_message . "</p>" . $row['message'] . "
                          </div>
                        </a>";
      } // End foreach loop

      // If posts were loaded
      if ($count > $limit) {
        $return_str .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'>
                        <input type='hidden' class='noMoreDropdownData' value='false'>";
      } else {
        $return_str .= "<input type='hidden' class='noMoreDropdownData' value='true'>
                        <p style='text-align: center;'>No more notifications to load</p>";
      }

      return $return_str;
    }

  }
?>
