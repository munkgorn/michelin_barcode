    // Working Contact Form
    $('#contact-form').submit(function(e) {
      e.preventDefault();
        var action = $(this).attr('action');
        var message = "";
        if($('#name').val() == "" || $.trim($('#name').val()) == "") {
          message = '<div class="error_message">You must enter your name.</div>';
        } else if($('#email').val() == "" || $.trim($('#email').val()) == "") {
          message = '<div class="error_message">Please enter a valid email address.</div>';
        } else if($('#subject').val() == "" || $.trim($('#subject').val()) == "") {
          message = '<div class="error_message">You must enter subject.</div>';
        } else if($('#message').val() == "" || $.trim($('#message').val()) == "") {
          message = '<div class="error_message">You must enter your message.</div>';
        }
        if(message != "") {
          document.getElementById('message-box').innerHTML = message;
          $('#message-box').slideDown('slow');
          $('#cform img.contact-loader').fadeOut('slow', function() {
              $(this).remove()
          });
          $('#submit').removeAttr('disabled');
          $('#cform').slideUp('slow');
          return false;
        } else {
          $("#message-box").slideUp(750, function() {
              $('#message-box').hide();

              $('#submit')
                  .before('')
                  .attr('disabled', 'disabled');

              $.post(action, {
                      name: $('#name').val(),
                      email: $('#email').val(),
                      subject: $('#subject').val(),
                      message: $('#message').val(),
                  },
                  function(data) {
                      document.getElementById('message-box').innerHTML = data;
                      $('#message-box').slideDown('slow');
                      $('#cform img.contact-loader').fadeOut('slow', function() {
                          $(this).remove()
                      });
                      $('#submit').removeAttr('disabled');
                      $('#cform').slideUp('slow');
                  }
              ).fail(function() {
                document.getElementById('message-box').innerHTML = "<fieldset><div id='success_page'><h3>Email Sent Successfully.</h3><p>Thank you, your message has been submitted to us.</p></div></fieldset>";
                $('#message-box').slideDown('slow');
                $('#cform img.contact-loader').fadeOut('slow', function() {
                    $(this).remove()
                });
                $('#submit').removeAttr('disabled');
                $('#cform').slideUp('slow');
              });
          });
        }

        return false;

    });

// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
    'use strict'
  
    window.addEventListener('load', function () {
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.getElementsByClassName('needs-validation')
  
      // Loop over them and prevent submission
      Array.prototype.filter.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
          if (form.checkValidity() === false) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
    }, false)
  }())