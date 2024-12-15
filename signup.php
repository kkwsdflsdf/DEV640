<?php // Example 27-5: signup.php
  require_once 'header.php';

echo <<<_END
  <script>
    function checkUser(user)
    {
      if (user.value == '')
      {
        $('#used').html('&nbsp;')
        return
      }

      $.post
      (
        'checkuser.php',
        { user : user.value },
        function(data)
        {
          $('#used').html(data)
        }
      )
    }
  </script>  
_END;

  $error = $user = $pass = "";
  $userError = $passError = "";
  if (isset($_SESSION['user'])) destroySession();

  if (isset($_POST['user']))
  {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);

    if ($user == "" || $pass == "")
      $error = 'Not all fields were entered<br><br>';
    else
    {
      // Check if username already exists
      $result = queryMysql("SELECT * FROM members WHERE user='$user'");

      if ($result->num_rows)
      {
        $error = 'That username already exists<br><br>';
        $userError = 'That username already exists';
      }
      // Check if password length is greater than 6 characters
      else if (strlen($pass) <= 6)
      {
        $passError = 'Password must be longer than 6 characters';
      }
      // Check if password is not the same as the username
      else if ($user == $pass)
      {
        $passError = 'Password cannot be the same as the username';
      }else
      {
        queryMysql("INSERT INTO members VALUES('$user', '$pass')");
        die('<h4>Account created</h4>Please Log in.</div></body></html>');
      }
    }
  }

echo <<<_END
      <form method='post' action='signup.php'>$error
      <div data-role='fieldcontain'>
        <label></label>
        Please enter your details to sign up
      </div>
      <div data-role='fieldcontain'>
        <label>Username</label>
        <input type='text' maxlength='16' name='user' value='$user'
          onBlur='checkUser(this)'>
        <label></label><div id='used'>&nbsp;</div>
        <span class='error'>$userError</span>
      </div>
      <div data-role='fieldcontain'>
        <label>Password</label>
        <input type='password' maxlength='16' name='pass' value='$pass'>
        <span class='error'>$passError</span>
      </div>
      <div data-role='fieldcontain'>
        <label></label>
        <input data-transition='slide' type='submit' value='Sign Up'>
      </div>
    </div>
  </body>
</html>
_END;

?>
<style>
form {
    margin: 20px 0;
}
input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}
input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
}
input[type="submit"]:hover {
    background-color: #45a049;
}
.fieldname {
    margin-right: 10px;
}
.error {
    color: red;
    font-weight: bold;
}
</style>