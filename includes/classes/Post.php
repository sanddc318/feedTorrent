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
      $check_empty = preg_replace( "/\s+/", "", "$body" ); // Removes all whitespace

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
        if ($user_to != "none") {
          $notification = new Notification($this->con, $added_by);
          $notification->insertNotification($returned_id, $user_to, "profile-post");
        }

        // 3. Update post count for posting user
        $num_posts = $this->user_obj->getNumPosts();
        $num_posts++;
        $update_query = mysqli_query( $this->con, "UPDATE users
                                                   SET num_posts = '$num_posts'
                                                   WHERE username = '$added_by'" );
      }
    }

    public function getSinglePost($post_id) {
      $loggedInUser = $this->user_obj->getUsername();

      $str = "";
      $data_query = mysqli_query( $this->con, "SELECT * FROM posts
                                         WHERE deleted = 'no'
                                         AND id = '$post_id'" );

      if ( mysqli_num_rows($data_query) > 0 ) {

        $row = mysqli_fetch_array($data_query);
          $id = $row["id"];
          $body = $row["body"];
          $added_by = $row["added_by"];
          $date_time = $row["date_added"];

          // Preprare user_to string so it can be included even if not posted to a user
          if ( $row["user_to"] == "none" ) {
            $user_to = "";
          } else {
            $user_to_obj = new User( $this->con, $row["user_to"] );
            $user_to_name = $user_to_obj->getUsername();
            // Return link to user profile page and their first + last name as the link text
            $user_to = "to <a href='" . $row["user_to"] . "'>" . $user_to_name . "</a>";
          }

          // Check if posting user has their account closed
          $added_by_obj = new User( $this->con, $added_by );
          if ( $added_by_obj->isClosed() ) {
            return;
          }

          $user_logged_obj = new User( $this->con, $loggedInUser );
          if ( $user_logged_obj->isFriend($added_by) ) {


            // Delete button
            if ($loggedInUser == $added_by) {
              $delete_button = "<button class='delete-button' id='post$id'>
                                  <i class='fa fa-times' aria-hidden='true'></i>
                                </button>";
            } else {
              $delete_button = '';
            }



            $user_details_query = mysqli_query( $this->con, "SELECT username, profile_pic
                                                            FROM users
                                                            WHERE username = '$added_by'" );
            $user_row = mysqli_fetch_array( $user_details_query );
            $username = $user_row["username"];
            $profile_pic = $user_row["profile_pic"];
?>

            <script>
              function toggle<?php echo $id; ?>() {
                var target = $(event.target);

                if ( !target.is("a") ) {
                  var element = document.getElementById("toggleComment<?php echo $id; ?>");

                  if ( element.style.display == "block" ) {
                    element.style.display = "none";
                  } else {
                    element.style.display = "block";
                  }

                }
              }
            </script>

<?php
            // Check how many comments there are
            $comments_check = mysqli_query( $this->con, "SELECT * FROM comments
                                                  WHERE post_id = '$id'" );
            $comments_check_num = mysqli_num_rows( $comments_check );

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
            // Final output
            $str .= "<div class='status-post' onClick='javascript:toggle$id()'>
                      <div class='post-profile-pic'>
                        <img src='$profile_pic' width='50'>
                      </div>

                      <div class='posted-by' style='color: #acacac;'>
                        <a href='$added_by'>#$username</a>
                        $user_to &nbsp;&nbsp;&nbsp;&nbsp; $time_message
                        $delete_button
                      </div>
                      <div id='post-body'>
                        $body <br><br><br>
                      </div>

                      <div class='newsfeed-post-options'>
                        Comments ($comments_check_num) &nbsp;&nbsp;&nbsp;
                        <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                      </div>
                    </div>

                    <div class='post-comment' id='toggleComment$id' style='display: none;'>
                      <iframe id='comment-iframe'
                              src='comment-frame.php?post_id=$id'
                              frameborder='0'
                      >
                      </iframe>
                    </div>";
?>

         <script>
           $(document).ready(function() {
             $('#post<?php echo $id; ?>').on('click', function(event) {
               bootbox.confirm("Are you sure you wan to delete this post?", function(result) {
                 $.post("includes/form-handlers/delete-post.php?post-id=<?php echo $id; ?>", {result: result})

                 if (result) {
                   location.reload();
                 }
               });
             });
           });
         </script>

<?php

          } else {
            echo "<p>You cannot see this post because you are not friends with this user</p>";
            return;
          } // End if block user_logged_obj

      } else {
        echo "<p>No post found. If you clicked a link, it may be broken</p>";
        return;
      } // End if block ( mysqli_num_rows($data_query) > 0 )
      echo $str;
    }





    public function loadPostsFriends( $data, $limit ) {
      $page = $data["page"];
      $loggedInUser = $this->user_obj->getUsername();

      if ( $page == 1 ) {
        $start = 0;
      } else {
        $start = ( $page - 1 ) * $limit;
      }


      $str = "";
      $data_query = mysqli_query( $this->con, "SELECT * FROM posts
                                         WHERE deleted = 'no'
                                         ORDER BY id DESC" );

      if ( mysqli_num_rows($data_query) > 0 ) {
        $num_iterations = 0; // Number of results checked (not necessarily posted)
        $count = 1;

        while ( $row = mysqli_fetch_array($data_query) ) {
          $id = $row["id"];
          $body = $row["body"];
          $added_by = $row["added_by"];
          $date_time = $row["date_added"];

          // Preprare user_to string so it can be included even if not posted to a user
          if ( $row["user_to"] == "none" ) {
            $user_to = "";
          } else {
            $user_to_obj = new User( $this->con, $row["user_to"] );
            $user_to_name = $user_to_obj->getUsername();
            // Return link to user profile page and their first + last name as the link text
            $user_to = "to <a href='" . $row["user_to"] . "'>" . $user_to_name . "</a>";
          }

          // Check if posting user has their account closed
          $added_by_obj = new User( $this->con, $added_by );
          if ( $added_by_obj->isClosed() ) {
            continue;
          }

          $user_logged_obj = new User( $this->con, $loggedInUser );
          if ( $user_logged_obj->isFriend($added_by) ) {

            if ( $num_iterations++ < $start )
              continue;

            // Once 10 posts have been loaded, break
            if ( $count > $limit ) {
              break;
            } else {
              $count++;
            }


            // Delete button
            if ($loggedInUser == $added_by) {
              $delete_button = "<button class='delete-button' id='post$id'>
                                  <i class='fa fa-times' aria-hidden='true'></i>
                                </button>";
            } else {
              $delete_button = '';
            }



            $user_details_query = mysqli_query( $this->con, "SELECT username, profile_pic
                                                            FROM users
                                                            WHERE username = '$added_by'" );
            $user_row = mysqli_fetch_array( $user_details_query );
            $username = $user_row["username"];
            $profile_pic = $user_row["profile_pic"];
?>

            <script>
              function toggle<?php echo $id; ?>() {
                var target = $(event.target);

                if ( !target.is("a") ) {
                  var element = document.getElementById("toggleComment<?php echo $id; ?>");

                  if ( element.style.display == "block" ) {
                    element.style.display = "none";
                  } else {
                    element.style.display = "block";
                  }

                }
              }
            </script>

<?php
            // Check how many comments there are
            $comment_check = mysqli_query( $this->con, "SELECT * FROM comments
                                                  WHERE post_id = '$id'" );
            $comment_check_num = mysqli_num_rows( $comment_check );

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
            // Final output
            $str .= "<div class='status-post' onClick='javascript:toggle$id()'>
                      <div class='post-profile-pic'>
                        <img src='$profile_pic' width='50'>
                      </div>

                      <div class='posted-by' style='color: #acacac;'>
                        <a href='$added_by'>#$username</a>
                        $user_to &nbsp;&nbsp;&nbsp;&nbsp; $time_message
                        $delete_button
                      </div>
                      <div id='post-body'>
                        $body <br><br><br>
                      </div>

                      <div class='newsfeed-post-options'>
                        Comments ($comment_check_num) &nbsp;&nbsp;&nbsp;
                        <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                      </div>
                    </div>

                    <div class='post-comment' id='toggleComment$id' style='display: none;'>
                      <iframe id='comment-iframe'
                              src='comment-frame.php?post_id=$id'
                              frameborder='0'
                      >
                      </iframe>
                    </div>
                    <hr>";
          } // End if block user_logged_obj
?>

         <script>
           $(document).ready(function() {
             $('#post<?php echo $id; ?>').on('click', function(event) {
               bootbox.confirm("Are you sure you wan to delete this post?", function(result) {
                 $.post("includes/form-handlers/delete-post.php?post-id=<?php echo $id; ?>", {result: result})

                 if (result) {
                   location.reload();
                 }
               });
             });
           });
         </script>

<?php
        } // End while loop

        if ( $count > $limit ) {
          $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                   <input type='hidden' class='noMorePosts' value='false'>";
        } else {
          $str .= "<input type='hidden' class='noMorePosts' value='true'>
                   <p style='text-align: center;'>No more posts to show</p>";
        }

      } // End if block ( mysqli_num_rows($data_query) > 0 )
      echo $str;
    } // End loadPostsFriends function





    public function loadProfilePosts( $data, $limit ) {
      $page = $data["page"];
      $profileUser = $data["profileUsername"];
      $loggedInUser = $this->user_obj->getUsername();

      if ( $page == 1 ) {
        $start = 0;
      } else {
        $start = ( $page - 1 ) * $limit;
      }


      $str = "";
      $data_query = mysqli_query( $this->con, "SELECT * FROM posts
                                         WHERE deleted = 'no'
                                         AND ( (added_by = '$profileUser'
                                         AND user_to = 'none')
                                         OR user_to = '$profileUser' )
                                         ORDER BY id DESC" );

      if ( mysqli_num_rows($data_query) > 0 ) {
        $num_iterations = 0; // Number of results checked (not necessarily posted)
        $count = 1;

        while ( $row = mysqli_fetch_array($data_query) ) {
          $id = $row["id"];
          $body = $row["body"];
          $added_by = $row["added_by"];
          $date_time = $row["date_added"];


            if ( $num_iterations++ < $start )
              continue;

            // Once 10 posts have been loaded, break
            if ( $count > $limit ) {
              break;
            } else {
              $count++;
            }


            // Delete button
            if ($loggedInUser == $added_by) {
              $delete_button = "<button class='delete-button btn-danger' id='post$id'>
                                  <i class='fa fa-times' aria-hidden='true'></i>
                                </button>";
            } else {
              $delete_button = '';
            }



            $user_details_query = mysqli_query( $this->con, "SELECT username, profile_pic
                                                            FROM users
                                                            WHERE username = '$added_by'" );
            $user_row = mysqli_fetch_array( $user_details_query );
            $username = $user_row["username"];
            $profile_pic = $user_row["profile_pic"];
?>

            <script>
              function toggle<?php echo $id; ?>() {
                var target = $(event.target);

                if ( !target.is("a") ) {
                  var element = document.getElementById("toggleComment<?php echo $id; ?>");

                  if ( element.style.display == "block" ) {
                    element.style.display = "none";
                  } else {
                    element.style.display = "block";
                  }

                }
              }
            </script>

<?php
            // Check how many comments there are
            $comment_check = mysqli_query( $this->con, "SELECT * FROM comments
                                                  WHERE post_id = '$id'" );
            $comment_check_num = mysqli_num_rows( $comment_check );

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
            // Final output
            $str .= "<div class='status-post' onClick='javascript:toggle$id()'>
                      <div class='post-profile-pic'>
                        <img src='$profile_pic' width='50'>
                      </div>

                      <div class='posted-by' style='color: #acacac;'>
                        <a href='$added_by'>#$username</a>
                        &nbsp;&nbsp;&nbsp;&nbsp; $time_message
                        $delete_button
                      </div>
                      <div id='post-body'>
                        $body <br><br><br>
                      </div>

                      <div class='newsfeed-post-options'>
                        Comments ($comment_check_num) &nbsp;&nbsp;&nbsp;
                        <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                      </div>
                    </div>

                    <div class='post-comment' id='toggleComment$id' style='display: none;'>
                      <iframe id='comment-iframe'
                              src='comment-frame.php?post_id=$id'
                              frameborder='0'
                      >
                      </iframe>
                    </div>
                    <hr>";
?>

         <script>
           $(document).ready(function() {
             $('#post<?php echo $id; ?>').on('click', function(event) {
               bootbox.confirm("Are you sure you wan to delete this post?", function(result) {
                 $.post("includes/form-handlers/delete-post.php?post-id=<?php echo $id; ?>", {result: result})

                 if (result) {
                   location.reload();
                 }
               });
             });
           });
         </script>

<?php
        } // End while loop

        if ( $count > $limit ) {
          $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                   <input type='hidden' class='noMorePosts' value='false'>";
        } else {
          $str .= "<input type='hidden' class='noMorePosts' value='true'>
                   <p style='text-align: center;'>No more posts to show</p>";
        }

      } // End if block ( mysqli_num_rows($data_query) > 0 )
      echo $str;
    } // End loadPostsFriends function





  }
?>
