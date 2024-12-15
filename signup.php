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
  if (isset($_SESSION['user'])) destroySession();

  if (isset($_POST['user']))
  {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);
    $location = "";
    $interests = "";

    if (isset($_POST['location']))  $location = sanitizeString($_POST['location']);
    if (isset($_POST['interests'])) $interests = sanitizeString($_POST['interests']);

    if ($user == "" || $pass == "")
      $error = 'Not all fields were entered<br><br>';
    else
    {
      $result = queryMysql("SELECT * FROM members WHERE user='$user'");

      if ($result->num_rows)
        $error = 'That username already exists<br><br>';
      else
      {
        // 插入用户信息到 members
        queryMysql("INSERT INTO members VALUES('$user', '$pass')");
        
        // 接着在 profiles 中创建对应的行，可以将 location 和 interests 插入
        // 如果不想在注册时立刻插入，可以只在 members 插入成功后再插入 profiles。
        queryMysql("INSERT INTO profiles (user, text, location, interests) VALUES('$user', '', '$location', '$interests')");
        
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
      <input type='text' maxlength='16' name='user' value='$user' onBlur='checkUser(this)'>
      <label></label><div id='used'>&nbsp;</div>
    </div>
    <div data-role='fieldcontain'>
      <label>Password</label>
      <input type='text' maxlength='16' name='pass' value='$pass'>
    </div>
    
    <!-- 新增字段：Location 和 Interests -->
    <div data-role='fieldcontain'>
      <label>Location</label>
      <input type='text' name='location' value=''>
    </div>
    <div data-role='fieldcontain'>
      <label>Interests</label>
      <input type='text' name='interests' value=''>
    </div>
    
    <div data-role='fieldcontain'>
      <label></label>
      <input data-transition='slide' type='submit' value='Sign Up'>
    </div>
  </form>
  _END;
  
?>
