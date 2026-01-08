jQuery(document).ready(function($) {
  var mediaUploader;
  
  $('#instructor_photo_button').on('click', function(e) {
    e.preventDefault();
    
    // If the uploader object has already been created, reopen the dialog
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    
    // Extend the wp.media object
    mediaUploader = wp.media({
      title: 'Choose Profile Photo',
      button: {
        text: 'Choose Photo'
      },
      multiple: false
    });
    
    // When a file is selected, grab the attachment ID and set it as the text field's value
    mediaUploader.on('select', function() {
      var attachment = mediaUploader.state().get('selection').first().toJSON();
      $('#instructor_photo').val(attachment.id);
      $('#instructor_photo_preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; display: block;" />');
      $('#instructor_photo_remove').show();
    });
    
    // Open the uploader dialog
    mediaUploader.open();
  });
  
  $('#instructor_photo_remove').on('click', function(e) {
    e.preventDefault();
    $('#instructor_photo').val('');
    $('#instructor_photo_preview').html('');
    $(this).hide();
  });
});
