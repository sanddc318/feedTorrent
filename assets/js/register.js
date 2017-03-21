$(document).ready( function() {

  // Onclick signup, hide login and show registration form
  $("#signup").click( function() {
    $("#first").slideUp("slow", function() {
      $("#second").slideDown("slow");
    })
  } );

  // Onclick signin, hide registration and show login form
  $("#signin").click( function() {
    $("#second").slideUp("slow", function() {
      $("#first").slideDown("slow");
    })
  } );

} );
