$(document).ready(function() {
  // Button for profile post
  $("#submit-profile-post").click(function() {
    $.ajax({
      type: "POST",
      url: "includes/handlers/ajax-submit-profile-post.php",
      data: $('form.profile-post').serialize(),
      success: function(msg) {
        $("#post-form").modal('hide');
        location.reload();
      },
      error: function() {
        alert("Failure");
      }
    }); // End ajax
  });

});

function getUsers(value, user) {
  $.post("includes/handlers/ajax-friend-search.php", {
    query: value,
    loggedInUser: user
  }, function(data) {
    $(".results").html(data);
  });
};

function getDropdownData(user, type) {
  if ($(".dropdown-data-window").css("height") == "0px") {
    var pageName;

    if (type == "notification") {

    } else if (type == "message") {
      pageName = "ajax-load-messages.php";
      $("span").remove("#unread-message");
    }

    var ajaxreq = $.ajax({
      url: "includes/handlers/" + pageName,
      type: "POST",
      data: "page=1&user=" + user,
      cache: false,
      success: function() {
        $(".dropdown-data-window").html(response);
        $(".dropdown-data-window").css({"padding": "0px", "height": "200px"});
        $("#dropdown-data-type").valueOf(type);
      }
    });

  } else {
    $(".dropdown-data-window").html("");
    $(".dropdown-data-window").css({"padding": "0px", "height": "0px"});
  }
};
