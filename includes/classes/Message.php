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

  }
?>
