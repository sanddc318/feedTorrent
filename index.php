<?php
  include("includes/header.php");

  if ( isset($_POST["post"]) ) {
    $post = new Post( $con, $loggedInUser );
    $post->submitPost( $_POST["post-text"], "none" );
    header( "Location: index.php" );
  }
?>

  <!-- User panel -->
  <div class="user-details column">
    <a href="<?php echo $loggedInUser; ?>">
      <img src="<?php echo $user["profile_pic"]; ?>">
    </a>

    <div class="user-details-leftcol-right">
      <span class="name">
        <a href="<?php echo $loggedInUser; ?>">
          <?php echo $user["first_name"] . " " . $user["last_name"] . "<br>"; ?>
        </a>
      </span>
      <?php echo "<em>#" . $user["username"] . "</em> <br><br>"; ?>
    </div>

    <div class="user-details-bottom">
      <div class="posts user-details-column">
        <span class="value">
          <?php echo $user["num_posts"] . "<br>"; ?>
        </span>
        <span class="category">Posts</span>
      </div>

      <div class="likes user-details-column">
        <span class="value">
          <?php echo $user["num_likes"] . "<br>"; ?>
        </span>
        <span class="category">Likes</span>
      </div>

      <div class="friends user-details-column">
        <span class="value">
          <?php echo $num_friends . "<br>"; ?>
        </span>
        <span class="category">Friends</span>
      </div>
    </div>
    <hr>

    <form class="post-form" action="index.php" method="POST">
      <textarea name="post-text" id="post-text" placeholder="Got something to say?"></textarea>
      <span id="post-message">YouTube links work too!</span>
      <input type="submit" name="post" id="post_button" value="Post">
    </form>
  </div>

  <!-- Posts area -->
  <div class="main-column column">
    <div class="posts-area"></div>
    <img id="loading" src="assets/images/icons/spinner.gif" style="width: 100%;">
  </div>

  <!-- Ajax pagination and auto reload -->
  <script>
    var loggedInUser = "<?php echo $loggedInUser; ?>";

    $(document).ready( function() {
      $("#loading").show();

      // Original Ajax request for loading initial set of posts
      $.ajax({
        url: "includes/handlers/ajax-load-posts.php",
        type: "POST",
        data: "page=1&loggedInUser=" + loggedInUser,
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
            url: "includes/handlers/ajax-load-posts.php",
            type: "POST",
            data: "page=" + page + "&loggedInUser=" + loggedInUser,
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
