<?php
  class Message {
    private $user_obj;
    private $con;

    public function __construct( $con, $user ) {
      $this->con = $con;
      $this->user_obj = new User( $con, $user );
    }



    public function getMostRecentUser() {
      $loggedInUser = $this->user_obj->getUsername();
      $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages
                                         WHERE user_to = '$loggedInUser'
                                         OR user_from = '$loggedInUser'
                                         ORDER BY id DESC LIMIT 1");

      if (mysqli_num_rows($query) == 0) {
        return false;
      }

      $row = mysqli_fetch_array($query);
      $user_to = $row["user_to"];
      $user_from = $row["user_from"];

      if ($user_to != $loggedInUser) {
        return $user_to;
      } else {
        return $user_from;
      }
    }

    public function sendMessage($user_to, $body, $date) {
      if ($body != "") {
        $loggedInUser = $this->user_obj->getUsername();
        $query = mysqli_query($this->con, "INSERT INTO messages
                                           VALUES ('', '$user_to', '$loggedInUser', '$body', '$date', 'no', 'no', 'no')");
      }
    }

    public function getMessages($otherUser) {
      $loggedInUser = $this->user_obj->getUsername();
      $data = "";
      $query = mysqli_query($this->con, "UPDATE messages
                                         SET opened = 'yes'
                                         WHERE user_to = '$loggedInUser'
                                         AND user_from = '$otherUser')");

      $get_messages_query = mysqli_query($this->con, "SELECT * FROM messages
                                                      WHERE (user_to = '$loggedInUser' AND user_from = '$otherUser') OR (user_from = '$loggedInUser' AND user_to = '$otherUser')");

      while ($row = mysqli_fetch_array($get_messages_query)) {
        $user_to = $row["user_to"];
        $user_from = $row["user_from"];
        $body = $row["body"];

        $div_top = ($user_to == $loggedInUser) ? "<div class='message' id='green'>"
                                               : "<div class='message' id='blue'>";
        $data = $data . $div_top . $body . "</div>";
      } // End while loop
      return $data;
    }

    public function getLatestMessage($loggedInUser, $user2) {
      $details_array = array();
      $query = mysqli_query($this->con, "SELECT body, user_to, date FROM messages
                                         WHERE (user_to = '$loggedInUser' AND user_from = '$user2')
                                         OR (user_to = '$user2' AND user_from  = '$loggedInUser')
                                         ORDER BY id DESC LIMIT 1");

      $row = mysqli_fetch_array($query);
      $sent_by = ($row["user_to"] == $loggedInUser) ? "They said: " : "You said: ";

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


      array_push($details_array, $sent_by);
      array_push($details_array, $row["body"]);
      array_push($details_array, $time_message);

      return $details_array;
    }

    public function getConvos() {
      $loggedInUser = $this->user_obj->getUsername();
      $return_str = "";
      $convos = array();
      $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages
                                         WHERE user_to = '$loggedInUser'
                                         OR user_from = '$loggedInUser'");

      while ($row = mysqli_fetch_array($query)) {
        $user_to_push = ($row["user_to"] != $loggedInUser) ? $row["user_to"] : $row["user_from"];

        if (!in_array($user_to_push, $convos)) {
          array_push($convos, $user_to_push);
        }
      } // End while

      foreach ($convos as $username) {
        $user_found_obj = new User($this->con, $username);
        // Get latest message between the two users
        $latest_message_details = $this->getLatestMessage($loggedInUser, $username);

        // Just show three dots if message is longers than 12 characters
        $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
        $split = str_split($latest_message_details[1], 12);
        $split = $split[0] . $dots;

        $return_str .= "<a href='messages.php?u=$username'>
                          <div class='user-found-messages'>
                            <img src='" . $user_found_obj->getProfilePic() . "'
                                 style='border-radius: 5px; margin-right: 5px;'
                            >" . $user_found_obj->getFirstAndLastName() . "
                            <span class='timestamp-smaller' id='gray'>" . $latest_message_details[2] . "</span>
                            <p id='gray' style='margin: 0;'>" . $latest_message_details[0] . $split . "</p>
                          </div>
                        </a>";
      } // End foreach loop
      return $return_str;
    }

  }
?>
