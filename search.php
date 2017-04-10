<?php
  include("includes/header.php");


  if (isset($_GET["q"])) {
    $query = $_GET["q"];
  } else {
    $query = "";
  }

  if (isset($_GET["type"])) {
    $type = $_GET["type"];
  } else {
    $type = "name";
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title></title>
  <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>

</body>
</html>
<div class="main-column column" id="main-column">

  <?php
    if ($query == "") {
      echo "You must enter something in the search box";
    } else {

      // If query contains underscore, assume user is searching by username
      if ($type == "username") {
        $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users
                                                  WHERE username LIKE '$query%'
                                                  AND user_closed = 'no'
                                                  LIMIT 8");
      } else {
        $names = explode(" ", $query);

        // If there are three words, assume they are first, middle and last name respectively
        if (count($names) == 3) {
          $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users
                                                    WHERE
                                                    (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%')
                                                    AND user_closed = 'no'");

        // If there are two words, assume they are first and last name respectively
        } else if (count($names) == 2) {
          $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users
                                                    WHERE
                                                    (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%')
                                                    AND user_closed = 'no'
                                                    LIMIT 8");

        // If query has only one word, search all first and last names
        } else {
          $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users
                                                    WHERE
                                                    (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%')
                                                    AND user_closed = 'no'
                                                    LIMIT 8");
        } // End if block count($names) == n


        // Check if results were found
        if (mysqli_num_rows($usersReturnedQuery) == 0) {
          echo "We can't find any users with a " . strtoupper($type) . " like: " . "<em>'$query'</em>
                <br><br><br>";
          echo "<p id='gray'>Try searching for:</p>";
          echo "<a href='search.php?q=" . $query . "&type=name'>Names</a>,
              <a href='search.php?q=" . $query . "&type=username'>Usernames</a>
              <hr>";
        } else {
          echo mysqli_num_rows($usersReturnedQuery) . " results found: <br><br>";
        }

        while ($row = mysqli_fetch_array($usersReturnedQuery)) {
          $user_obj = new User($con, $user["username"]);
          $button = "";
          $mutual_friends = "";

          if ($user["username"] != $row["username"]) {

            // Generate button depending on friendship status
            if ($user_obj->isFriend($row["username"])) {
              $button = "<input type='submit' name='" . $row['username'] . "'
                                class='danger profile-button' value='Remove Friend'>";
            } else if ($user_obj->didReceiveRequest($row["username"])) {
              $button = "<input type='submit' name='" . $row['username'] . "'
                                class='warning profile-button' value='Respond To Request'>";
            } else if ($user_obj->didSendRequest($row["username"])) {
              $button = "<input class='default profile-button' value='Request Sent'>";
            } else {
              $button = "<input type='submit' name='" . $row['username'] . "'
                                class='success profile-button' value='Add Friend'>";
            }

            $mutual_friends = $user_obj->getMutualFriends($row["username"]) . " friends in common";

          } else {
            $mutual_friends = "This is you";
          } // End outer if block

          echo "<div class='search-result'>
                  <div class='search-page-friend-button'>
                    <form action='' method='POST'>
                      " . $button . "
                      <br>
                    </form>
                  </div>

                  <div class='result-profile-pic'>
                    <a href='" . $row['username'] . "'>
                      <img src='" . $row['profile_pic'] . "' style='height: 100px;'>
                    </a>
                  </div>

                  <a href='" . $row['username'] . "'>
                    " . $row['first_name'] . " " . $row['last_name'] . "
                    <p id='gray'>" . $row['username'] . "</p>
                  </a>
                  <br>

                  " . $mutual_friends . "
                  <br>
                </div>
                <hr>";

        } // End while loop


      } // End if block $type == "username"

    } // End if block $query == ""
  ?>

</div>
