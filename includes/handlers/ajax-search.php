<?php
  include("../../config/config.php");
  include("../../includes/classes/User.php");


  $query = $_POST["query"];
  $loggedInUser = $_POST["loggedInUser"];
  $names = explode(" ", $query);


  // If query contains underscore, assume user is searching by username
  if (strpos($query, "_") !== false) {
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users
                                              WHERE username LIKE '$query%'
                                              AND user_closed = 'no'
                                              LIMIT 8");

  // If there are two words, assume they are first and last name respectively
  } else if (count($names) == 2) {
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users
                                              WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed = 'no'
                                              LIMIT 8");

  // If query has only one word, search all first and last names
  } else {
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users
                                              WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%')
                                              AND user_closed = 'no'
                                              LIMIT 8");
  }


  if ($query != "") {

    while($row = mysqli_fetch_array($usersReturnedQuery)) {
      $user = new User($con, $loggedInUser);

      if ($row["username"] != $loggedInUser) {
        $mutualFriends = $user->getMutualFriends($row["username"]) . " friends in common";
      } else {
        $mutualFriends = "This is you";
      }

      echo "<div class='result-display'>
              <a href='" . $row['username'] . "' style='color: #1485bd'>
                <div class='live-search-profile-pic'>
                  <img src='" . $row['profile_pic'] . "'>
                </div>

                <div class='live-search-text'>
                  " . $row['first_name'] . " " . $row['last_name'] . "
                  <p>" . $row['username'] . "</p>
                  <p id='gray'>" . $mutualFriends . "</p>
                </div>
              </a>
            </div>";
    } // End while

  } // End if block $query != ""
?>
