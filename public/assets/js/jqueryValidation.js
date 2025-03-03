$(document).ready(function() {
  $('#myForm').validate({
    rules: {
      name: {
        required:true
      },
      email: {
        required: true,
        email: true
      },
      password: {
        required: true,
        minlength: 8
      },
      roles: {
        required: true,
      },
      permission: {
        required:true
      },
      url: {
        url: true,
      },

    }, 
    messages: {
      name: 'Please enter Name.',
      email: {
        required: 'Please enter Email Address.',
        email: 'Please enter a valid Email Address.',
      },
      password: {
        required: 'Please enter Password.',
        minlength: 'Password must be at least 8 characters long.',
      },
      roles: {
        required: 'please choose roles',
      },
      permission: {
        required: 'please select the permissions'
      },

      url: {
      
        url: 'Please enter a valid URL (e.g. https://example.com)',
      }

    },
    submitHandler: function (form) {
      form.submit();
    }
  });
});

