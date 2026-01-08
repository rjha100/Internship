jQuery(document).ready(function ($) {
  $("#subscribe-btn").on("click", function (e) {
    e.preventDefault();

    var email = $("#newsletter-email").val();
    var button = $(this);
    var messageDiv = $("#newsletter-message");

    if (!email || !validateEmail(email)) {
      messageDiv.html(
        '<p style="color: #ff0000;">Please enter a valid email address.</p>',
      );
      return;
    }

    button.prop("disabled", true).text("Subscribing...");
    messageDiv.html("");

    // Send AJAX request
    $.ajax({
      url: themeAjax.ajaxurl,
      type: "POST",
      data: {
        action: "subscribe_newsletter",
        email: email,
        nonce: themeAjax.nonce,
      },
      success: function (response) {
        if (response.success) {
          messageDiv.html(
            '<p style="color: #4CAF50;">' + response.data.message + "</p>",
          );
          $("#newsletter-email").val("");
        } else {
          messageDiv.html(
            '<p style="color: #ff0000;">' + response.data.message + "</p>",
          );
        }
        button.prop("disabled", false).text("Subscribe");
      },
      error: function () {
        messageDiv.html(
          '<p style="color: #ff0000;">An error occurred. Please try again.</p>',
        );
        button.prop("disabled", false).text("Subscribe");
      },
    });
  });

  function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  $("#newsletter-email").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      $("#subscribe-btn").click();
    }
  });
});
