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
