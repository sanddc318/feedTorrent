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
  }
?>
