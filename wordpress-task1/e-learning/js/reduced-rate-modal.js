jQuery(document).ready(function($) {
  // Open modal
  $('#open-reduced-rate-modal').on('click', function(e) {
    e.preventDefault();
    $('#reduced-rate-modal').css('display', 'flex').addClass('active');
    $('body').css('overflow', 'hidden');
  });

  function closeModal() {
    $('#reduced-rate-modal').removeClass('active');
    setTimeout(function() {
      $('#reduced-rate-modal').css('display', 'none');
      $('#reduced-rate-form')[0].reset();
      $('#form-success-message').hide();
      $('#reduced-rate-form').show();
    }, 300);
    $('body').css('overflow', 'auto');
  }

  $('#close-reduced-rate-modal').on('click', closeModal);
  $('#cancel-reduced-rate').on('click', closeModal);
  $('#close-success-modal').on('click', closeModal);
  $('#reduced-rate-modal').on('click', function(e) {
    if ($(e.target).is('#reduced-rate-modal')) {
      closeModal();
    }
  });

  $(document).on('keydown', function(e) {
    if (e.key === 'Escape' && $('#reduced-rate-modal').is(':visible')) {
      closeModal();
    }
  });

  $('#reduced-rate-form').on('submit', function(e) {
    e.preventDefault();
    
    var formData = {
      action: 'submit_reduced_rate_application',
      nonce: themeAjax.nonce,
      class_id: $('input[name="class_id"]').val(),
      class_title: $('input[name="class_title"]').val(),
      applicant_name: $('input[name="applicant_name"]').val(),
      applicant_email: $('input[name="applicant_email"]').val(),
      reason: $('textarea[name="reason"]').val()
    };

    var submitBtn = $(this).find('button[type="submit"]');
    var originalText = submitBtn.text();
    submitBtn.prop('disabled', true).text('Submitting...');

    $.ajax({
      url: themeAjax.ajaxurl,
      type: 'POST',
      data: formData,
      success: function(response) {
        if (response.success) {
          $('#reduced-rate-form').hide();
          $('#form-success-message').fadeIn(300);
        } else {
          alert(response.data.message || 'An error occurred. Please try again.');
          submitBtn.prop('disabled', false).text(originalText);
        }
      },
      error: function() {
        alert('An error occurred. Please try again.');
        submitBtn.prop('disabled', false).text(originalText);
      }
    });
  });
});
