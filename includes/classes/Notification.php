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

  }
?>
