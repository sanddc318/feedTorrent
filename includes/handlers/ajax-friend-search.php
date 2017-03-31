<?php
  include("../../config/config.php");
  include("../classes/User.php");


  $query = $_POST["query"];
  $loggedInUser = $_POST["loggedInUser"];
  $names = explode(" ", $query);

  if (strpos($query, "_") !== false) { // Search by username
    $usersReturned = mysqli_query($con, "SELECT * FROM users
                                        WHERE username LIKE '$query%'
                                        AND user_closed = 'no'
                                        LIMIT 8");
  } else if (count($names) == 2) { // Search by first and/or lastname
    $usersReturned = mysqli_query($con, "SELECT * FROM users
                                        WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%')
                                        AND user_closed = 'no'
                                        LIMIT 8");
  } else {
    $usersReturned = mysqli_query($con, "SELECT * FROM users
                                        WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%')
                                        AND user_closed = 'no'
                                        LIMIT 8");
  }

  if ($query != "" ) {
    while ($row = mysqli_fetch_array($usersReturned)) {
      $user = new User($con, $loggedInUser);

      if ($row["username"] != $loggedInUser) {
        $mutualFriends = $user->getMutualFriends($row["username"]) . " friends in common";
      } else {
        $mutualFriends = "";
      }

      if ($user->isFriend($row["username"])) {
        echo "<div class='result-display'>
                <a href='messages.php?u='" . $row["username"] . "'style='color: #000;'>
                  <div class='live-search-profile-pic'>
                    <img src='" . $row["profile_pic"] . "'>
                  </div>

                  <div class='live-search-text'>
                    " . $row["first_name"] . " " . $row["last_name"] . "
                    <p>" . $row["username"] . "</p>
                    <p id='gray'>" . $mutualFriends . "</p>
                  </div>
                </a>
              </div>";
      }

    }
  }
?>
