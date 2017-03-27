<?php
  class Post {
    private $user_obj;
    private $con;

    public function __construct( $con, $user ) {
      $this->con = $con;
      $this->user_obj = new User( $con, $user );
    }



    public function submitPost( $body, $user_to ) {
      $body = strip_tags( $body ); // Removes HTML tags
      $body = mysqli_real_escape_string( $this->con, $body ); // Removes special characters
      $check_empty = preg_replace( "/\s/", "", "$body" ); // Removes all whitespace

      if ( $check_empty != "" ) {
        $date_added = date( "Y-m-d H:i:s" );
        $added_by = $this->user_obj->getUsername();

        // If user is own profile, set $user_to to "none"
        if ( $user_to == $added_by ) {
          $user_to = "none";
        }

        // 1. Insert post
        $query = mysqli_query( $this->con, "INSERT INTO posts
                                            VALUES ('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')" );
        $returned_id = mysqli_insert_id( $this->con );

        // 2. Insert notification for receiving party

        // 3. Update post count for posting user
        $num_posts = $this->user_obj->getNumPosts();
        $num_posts++;
        $update_query = mysqli_query( $this->con, "UPDATE users
                                                   SET num_posts = '$num_posts'
                                                   WHERE username = '$added_by'" );
      }
    }

    public function loadPostsFriends() {
      $str = "";
      $data = mysqli_query( $this->con, "SELECT * FROM posts
                                         WHERE deleted = 'no'
                                         ORDER BY id DESC" );
      while ( $row = mysqli_fetch_array($data) ) {
        $id = $row["row"];
        $body = $row["body"];
        $added_by = $row["added_by"];
        $date_time = $row["date_added"];

        // Preprare user_to string so it can be included even if not posted to a user
        if ( $row["user_to"] == "none" ) {
          $user_to = "";
        } else {
          $user_to_obj = new User( $con, $row["user_to"] );
          $user_to_name = $user_to_obj->getFirstAndLastName();
          // Return link to user profile page and their first + last name as the link text
          $user_to = "<a href='" . $row["user_to"] . "'>" . $user_to_name . "</a>";
        }

        // Check if posting user has their account closed
        $added_by_obj = new User( $con, $added_by );
        if ( $added_by_obj->isClosed() ) {
          continue;
        }

        $user_details_query = mysqli_query( $this->con, "SELECT first_name, last_name, profile_pic
                                                         FROM users
                                                         WHERE username = '$added_by'" );
        $user_row = mysqli_fetch_array( $user_details_query );

        // Get a timestamp
        $date_time_now = date( "Y-m-d H:i:s" );
        $start_date = new Datetime( $date_time ); // Time of post creation
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
            $days = " ago"; // If exactly a month, just add "ago" after the month (e.g. "4 months ago")
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

        }
      } // End while
    }

  }
?>
