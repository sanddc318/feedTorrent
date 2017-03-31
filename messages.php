<?php
  include("includes/header.php");


  $message_obj = new Message($con, $loggedInUser);

  if (isset($_GET["u"])) {
    $user_to = $_GET["u"];
  } else {
    $user_to = $message_obj->getMostRecentUser();

    if ($user_to == false)
      $user_to = "new"; // Means sending a new message

  }

  if ($user_to != "new")
    $user_to_obj = new User($con, $user_to);

  if (isset($_POST["post-message"])) {

    if (isset($_POST["message-body"])) {
      $body = mysqli_real_escape_string($con, $_POST["message-body"]);
      $date = date("Y-m-d H:i:s");
      $message_obj->sendMessage($user_to, $body, $date);
    }

  }
?>

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
    <?php
      echo "<em>#" . $user["username"] . "</em> <br><br>";
      echo "Posts: " . $user["num_posts"] . "<br>";
      echo "Likes: " . $user["num_likes"] . "<br>";
      echo "Friends: " . $num_friends . "<br>";
    ?>
  </div>
</div>

<div class="main-column column" id="main-column">
  <?php
    if ($user_to != "new") {
      echo "<h4>You and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() . "</a></h4> <hr><br>";
      echo "<div class='loaded-messages' id='scroll-messages'>";
        echo $message_obj->getMessages($user_to);
      echo "</div>";
    } else {
      echo "<h4>New Message</h4>";
    }
  ?>

  <div class="message-post">
    <form action="" method="POST">
      <?php
        if ($user_to == "new") {
          echo "Select the friend you would like to message <br><br>";
          echo "To: <input type='text' >";
          echo "<div class='results'>
                </div>";
        } else {
          echo "<textarea name='message-body' id='message-textarea' placeholder='Write your message...'></textarea>";
          echo "<input type='submit' name='post-message' class='info' id='message-submit' value='Send' >";
        }
      ?>
    </form>
  </div>

  <script>
    var div = document.getElementById("scroll-messages");
    div.scrollTop = div.scrollHeight;
  </script>

</div>


<div class="user-details column" id="conversations">
  <h4>Conversations</h4>

  <div class="loaded-conversations">
    <?php echo $message_obj->getConvos(); ?>
  </div>
  <br>
  <a href="messages.php?u=new">New Message</a>
</div>
