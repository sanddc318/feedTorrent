// Expand triggers
$(document).ready(function() {
  // Expands live search bar
  $("#search-text-input").focus(function() {
    if (window.matchMedia("(min-width: 800px)").matches) {
      $(this).animate({width: "250px"}, 500);
    }
  });

  // Expands new post area
  $("#post-text").focus(function() {
    if (window.matchMedia("(min-width: 800px)").matches) {
      $(this).animate({height: "150px"}, 500);
      $("#post-message, #post_button").css({"display": "block"});
    }
  });

  // Submit live search query
  $(".button-holder").on("click", function() {
    document.search_form.submit();
  });

  // Button for profile posting
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

// Dismiss dropdowns when clicking away from them
$(document).click(function(e) {

  if (e.target.class != "search-results" && e.target.id != "search-text-input") {
    $(".search-results").html("");
    $(".search-results-footer").html("");
    $(".search-results-footer").toggleClass("search-results-footer-empty");
    $(".search-results-footer").toggleClass("search-results-footer");
  }

  if (e.target.class != "dropdown-data-window") {
    $(".dropdown-data-window").html("").css({"padding": "0px", "height": "0px"});
  }

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
      pageName = "ajax-load-notifications.php";
      $("span").remove("#unread-notification");

    } else if (type == "message") {
      pageName = "ajax-load-messages.php";
      $("span").remove("#unread-message");
    }

    var ajaxreq = $.ajax({
      url: "includes/handlers/" + pageName,
      type: "POST",
      data: "page=1&loggedInUser=" + user,
      cache: false,
      success: function(response) {
        $(".dropdown-data-window").html(response);
        $(".dropdown-data-window").css({"padding": "0px", "height": "200px", "border": "1px solid #dadada"});
        $("#dropdown-data-type").val(type);
      }
    });

  } else {
    $(".dropdown-data-window").html("");
    $(".dropdown-data-window").css({"padding": "0px", "height": "0px", "border": "none"});
  }
};

function getLiveSearchUsers(value, user) {
  $.post("includes/handlers/ajax-search.php", {query: value, loggedInUser: user}, function(data) {
    if ( $(".search-results-footer-empty")[0] ) {
      $(".search-results-footer-empty").toggleClass("search-results-footer");
      $(".search-results-footer-empty").toggleClass("search-results-footer-empty");
    }

    $(".search-results").html(data);
    $(".search-results-footer").html("<a href='search.php?q=" + value + "'>See All Results</a>");

    if (data == "") {
      $(".search-results-footer").html("");
      $(".search-results-footer").toggleClass("search-results-footer-empty");
      $(".search-results-footer").toggleClass("search-results-footer");
    }
  });
};
