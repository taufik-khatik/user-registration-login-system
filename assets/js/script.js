$(document).ready(function() {
  // Check login status on page load
  checkLoginStatus();

  // Register form validation
  $('#registerForm').submit(function(e) {
    e.preventDefault(); 
    var isValid = true;

    // Reset previous error messages
    $('.error-message').remove();

    // Validate each field
    if ($('#first_name').val().trim() === '') {
      $('#first_name').after('<div class="error-message text-danger">Please enter your first name</div>');
      isValid = false;
    }
    if ($('#last_name').val().trim() === '') {
      $('#last_name').after('<div class="error-message text-danger">Please enter your last name</div>');
      isValid = false;
    }
    if ($('#email').val().trim() === '') {
      $('#email').after('<div class="error-message text-danger">Please enter your email</div>');
      isValid = false;
    }
    if ($('#mobile').val().trim() === '') {
      $('#mobile').after('<div class="error-message text-danger">Please enter your mobile number</div>');
      isValid = false;
    }
    if ($('#password').val().trim() === '') {
      $('#password').after('<div class="error-message text-danger">Please enter a password</div>');
      isValid = false;
    }
    if ($('#confirm_password').val().trim() === '') {
      $('#confirm_password').after('<div class="error-message text-danger">Please confirm your password</div>');
      isValid = false;
    }
    // Check if passwords do not match
    if ($('#password').val().trim() !== $('#confirm_password').val().trim()) {
      $('#confirm_password').after('<div class="error-message text-danger">Passwords do not match</div>');
      isValid = false;
    }

    if (isValid) {
      var formData = new FormData($(this)[0]);
      $.ajax({
          type: 'POST',
          url: './includes/register.php', // Construct the URL
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(response) {
              console.log(response);
              if (response.success) {
                  $('#errorMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                  alert(response.message);
                  console.log(response.message);
                  // Redirect to login page
                  window.location.href = 'login.html';
              } else {
                  $('#errorMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                  console.log(response.message);
              }
          },
          error: function(xhr, status, error) {
            $('#errorMessage').html('<div class="alert alert-danger">An error occurred while processing your request.</div>');
            console.log(xhr.responseText);
          },
      });
    }
  });

  // Login form validation
  $('#loginForm').on('submit', function(e) {
      e.preventDefault(); 

      // Reset previous error messages
      $('.error-message').remove();

      // Validate each field
      if ($('#email').val().trim() === '') {
          $('#email').after('<div class="error-message text-danger">Please enter your email</div>');
      }
      if ($('#password').val().trim() === '') {
          $('#password').after('<div class="error-message text-danger">Please enter your password</div>');
      }

      // Get form data
      var formData = $(this).serialize();

      // Send AJAX POST request to the server
      $.ajax({
          type: 'POST',
          url: './includes/login.php',
          data: formData,
          dataType: 'json',
          success: function(response) {
              if (response.success) {
                  window.location.href = './includes/dashboard.php';
              } else {
                  $('#errorMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
              }
          },
          error: function(xhr, status, error) {
            $('#errorMessage').html('<div class="alert alert-danger">An error occurred while processing your request.</div>');
            console.log(xhr.responseText);
          }
      });
  });

  // Function to check login status
  function checkLoginStatus() {
      $.ajax({
          type: 'GET',
          url: './includes/check_login.php', // Adjust URL as needed
          dataType: 'json',
          success: function(response) {
              if (response.logged_in) {
                  window.location.href = './includes/dashboard.php'; // Redirect to dashboard if logged in
              }
          },
          error: function(xhr, status, error) {
              console.log(xhr.responseText);
          }
      });
  }


  // Remove error message on input change
  $('input').on('input', function() {
      $(this).next('.error-message').remove();
  });

  // Prevent copy and paste
  $('input').on('copy paste', function(e) {
      e.preventDefault();
  });

  // Prevent autocomplete
  $('input').on('focus', function() {
      $(this).attr('autocomplete', 'new-password');
  });

  // Alpha only
  const alphaOnly = document.querySelectorAll('#first_name, #last_name');
  alphaOnly.forEach(function(element) {
      element.addEventListener('beforeinput', function(event) {
          if (event.inputType === 'deleteContentBackward') {
              return;
          }
          var value = this.value;
          if (!/^[a-zA-Z ]$/.test(event.data) || (event.data === ' ' && value.length === 0)) {
              event.preventDefault();
          }
      });
  });

  // Numeric only
  const numericOnly = document.querySelectorAll('#mobile');
  numericOnly.forEach(function(element) {
      element.addEventListener('beforeinput', function(event) {
          if (event.inputType === 'deleteContentBackward') {
              return;
          }

          // Prevent space as the first character
          if (this.value.length === 0 && event.data === ' ') {
              event.preventDefault();
              return;
          }

          // Allow only numeric characters
          if (!/^\d$/.test(event.data)) {
            event.preventDefault();
          }

          // Check if length exceeds 10 digits
          if (this.value.replace(/\D/g, '').length >= 10 && event.inputType !== 'deleteContentBackward') {
              event.preventDefault();
          }
      });
  });


});

