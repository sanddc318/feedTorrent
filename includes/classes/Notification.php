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

  }
?>
