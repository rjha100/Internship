jQuery(document).ready(function ($) {
  var isUserLoggedIn = $("body").hasClass("logged-in");

  $("#header-signin-button").on("click", function (e) {
    e.preventDefault();
    if (!isUserLoggedIn) {
      openModal("#signin-modal");
    }
  });

  $("#header-logout-button").on("click", function (e) {
    e.preventDefault();
    if (confirm("Are you sure you want to log out?")) {
      $.ajax({
        url: themeAjax.ajaxurl,
        type: "POST",
        data: {
          action: "user_logout",
          nonce: themeAjax.nonce,
        },
        success: function (response) {
          if (response.success) {
            window.location.reload();
          }
        },
        error: function () {
          alert("An error occurred. Please try again.");
        },
      });
    }
  });

  $("#signup-button").on("click", function (e) {
    e.preventDefault();
    if (isUserLoggedIn) {
      alert("Proceeding with class signup...");
    } else {
      openModal("#register-modal");
    }
  });

  function openModal(modalId) {
    $(modalId).css("display", "flex").addClass("active");
    $("body").css("overflow", "hidden");
  }

  function closeModal(modalId) {
    $(modalId).removeClass("active");
    setTimeout(function () {
      $(modalId).css("display", "none");
      $(modalId).find("form")[0].reset();
      $(modalId).find(".form-success").hide();
      $(modalId).find(".form-error").hide();
      $(modalId).find("form").show();
    }, 300);
    $("body").css("overflow", "auto");
  }

  $("#close-signin-modal").on("click", function () {
    closeModal("#signin-modal");
  });

  $("#cancel-signin").on("click", function () {
    closeModal("#signin-modal");
  });

  $("#signin-modal").on("click", function (e) {
    if ($(e.target).is("#signin-modal")) {
      closeModal("#signin-modal");
    }
  });

  // Registration Modal Controls
  $("#close-register-modal").on("click", function () {
    closeModal("#register-modal");
  });

  $("#cancel-register").on("click", function () {
    closeModal("#register-modal");
  });

  $("#register-modal").on("click", function (e) {
    if ($(e.target).is("#register-modal")) {
      closeModal("#register-modal");
    }
  });

  $("#switch-to-register").on("click", function (e) {
    e.preventDefault();
    closeModal("#signin-modal");
    setTimeout(function () {
      openModal("#register-modal");
    }, 350);
  });

  $("#switch-to-signin").on("click", function (e) {
    e.preventDefault();
    closeModal("#register-modal");
    setTimeout(function () {
      openModal("#signin-modal");
    }, 350);
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      if ($("#signin-modal").is(":visible")) {
        closeModal("#signin-modal");
      }
      if ($("#register-modal").is(":visible")) {
        closeModal("#register-modal");
      }
    }
  });

  $("#signin-form").on("submit", function (e) {
    e.preventDefault();

    var emailOrUsername = $("#signin-email").val();
    var password = $("#signin-password").val();
    var rememberMe = $("#signin-remember").is(":checked");
    var submitBtn = $(this).find('button[type="submit"]');
    var originalText = submitBtn.text();
    var errorMsg = $("#signin-error-message");

    errorMsg.hide();
    submitBtn.prop("disabled", true).text("Signing in...");

    $.ajax({
      url: themeAjax.ajaxurl,
      type: "POST",
      data: {
        action: "user_login",
        nonce: themeAjax.nonce,
        email_or_username: emailOrUsername,
        password: password,
        remember: rememberMe,
      },
      success: function (response) {
        if (response.success) {
          $("#signin-form").hide();
          $("#signin-success-message").fadeIn(300);
          // Redirect after 1.5 seconds
          setTimeout(function () {
            window.location.reload();
          }, 1500);
        } else {
          errorMsg
            .text(response.data.message || "Invalid email or password.")
            .fadeIn(300);
          submitBtn.prop("disabled", false).text(originalText);
        }
      },
      error: function () {
        errorMsg.text("An error occurred. Please try again.").fadeIn(300);
        submitBtn.prop("disabled", false).text(originalText);
      },
    });
  });

  $("#register-form").on("submit", function (e) {
    e.preventDefault();

    var name = $("#register-name").val();
    var username = $("#register-username").val();
    var email = $("#register-email").val();
    var password = $("#register-password").val();
    var confirmPassword = $("#register-confirm-password").val();
    var submitBtn = $(this).find('button[type="submit"]');
    var originalText = submitBtn.text();
    var errorMsg = $("#register-error-message");

    errorMsg.hide();

    // Client-side validation
    if (password !== confirmPassword) {
      errorMsg.text("Passwords do not match.").fadeIn(300);
      return;
    }

    if (password.length < 8) {
      errorMsg.text("Password must be at least 8 characters long.").fadeIn(300);
      return;
    }

    submitBtn.prop("disabled", true).text("Creating account...");

    $.ajax({
      url: themeAjax.ajaxurl,
      type: "POST",
      data: {
        action: "user_register",
        nonce: themeAjax.nonce,
        name: name,
        username: username,
        email: email,
        password: password,
      },
      success: function (response) {
        if (response.success) {
          $("#register-form").hide();
          $("#register-success-message").fadeIn(300);
          // Redirect after 2 seconds
          setTimeout(function () {
            window.location.reload();
          }, 2000);
        } else {
          errorMsg
            .text(
              response.data.message || "Registration failed. Please try again.",
            )
            .fadeIn(300);
          submitBtn.prop("disabled", false).text(originalText);
        }
      },
      error: function () {
        errorMsg.text("An error occurred. Please try again later.").fadeIn(300);
        submitBtn.prop("disabled", false).text(originalText);
      },
    });
  });

  var usernameTimeout;
  $("#register-username").on("input", function () {
    var username = $(this).val().trim();
    var $icon = $("#username-validation");
    var $message = $("#username-message");

    clearTimeout(usernameTimeout);

    if (username.length === 0) {
      $icon.removeClass("checking valid invalid");
      $message.text("");
      return;
    }

    if (username.length < 3) {
      $icon.removeClass("checking valid").addClass("invalid");
      $message
        .text("Username must be at least 3 characters.")
        .css("color", "#dc3636");
      return;
    }

    $icon.removeClass("valid invalid").addClass("checking");
    $message.text("Checking...").css("color", "#999");

    usernameTimeout = setTimeout(function () {
      $.ajax({
        url: themeAjax.ajaxurl,
        type: "POST",
        data: {
          action: "check_user_availability",
          nonce: themeAjax.nonce,
          type: "username",
          value: username,
        },
        success: function (response) {
          if (response.success) {
            $icon.removeClass("checking invalid").addClass("valid");
            $message.text(response.data.message).css("color", "#00ACB4");
          } else {
            $icon.removeClass("checking valid").addClass("invalid");
            $message.text(response.data.message).css("color", "#dc3636");
          }
        },
        error: function () {
          $icon.removeClass("checking valid invalid");
          $message.text("").css("color", "");
        },
      });
    }, 500);
  });

  // AJAX validation for email availability
  var emailTimeout;
  $("#register-email").on("input", function () {
    var email = $(this).val().trim();
    var $icon = $("#email-validation");
    var $message = $("#email-message");

    clearTimeout(emailTimeout);

    if (email.length === 0) {
      $icon.removeClass("checking valid invalid");
      $message.text("");
      return;
    }

    $icon.removeClass("valid invalid").addClass("checking");
    $message.text("Checking...").css("color", "#999");

    emailTimeout = setTimeout(function () {
      $.ajax({
        url: themeAjax.ajaxurl,
        type: "POST",
        data: {
          action: "check_user_availability",
          nonce: themeAjax.nonce,
          type: "email",
          value: email,
        },
        success: function (response) {
          if (response.success) {
            $icon.removeClass("checking invalid").addClass("valid");
            $message.text(response.data.message).css("color", "#00ACB4");
          } else {
            $icon.removeClass("checking valid").addClass("invalid");
            $message.text(response.data.message).css("color", "#dc3636");
          }
        },
        error: function () {
          $icon.removeClass("checking valid invalid");
          $message.text("").css("color", "");
        },
      });
    }, 500);
  });
});
