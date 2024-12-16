<?php // Example 27-11: messages.php
  require_once 'header.php';
  require_once 'functions.php';

  if (!$loggedin) die("</div></body></html>");

  if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
  else                      $view = $user;

  $error = "";

  if (isset($_POST['text']) && isset($_POST['pm']))
  {
    $text = sanitizeString($_POST['text']);
    $pm = sanitizeString($_POST['pm']);

    if (!isMessageValid($text, 10)) {
        $error = "<span class='error'>&nbsp;&#x2718; The message must be more than 10 characters long</span>";
    } else {
        $pm   = substr(sanitizeString($_POST['pm']),0,1);
        $time = time();
        queryMysql("INSERT INTO messages VALUES(NULL, '$user', '$view', '$pm', $time, '$text', 0, 0)");
        $error = "<span class='success'>&nbsp;&#x2714; Message posted successfully</span>";
    }
  }

  if (isset($_POST['like']) || isset($_POST['dislike'])) {
    $messageId = sanitizeString($_POST['messageId']);
    if (isset($_POST['like'])) {
        queryMysql("UPDATE messages SET likes = likes + 1 WHERE id = $messageId");
    } else if (isset($_POST['dislike'])) {
        queryMysql("UPDATE messages SET dislikes = dislikes + 1 WHERE id = $messageId");
    }
  }

  if ($view != "")
  {
    if ($view == $user) $name1 = $name2 = "Your";
    else
    {
      $name1 = "<a href='members.php?view=$view'>$view</a>'s";
      $name2 = "$view's";
    }

    echo "<h3>$name1 Messages</h3>";
    showFriendMessages($user, $view);
    
    echo <<<_END
      <form method='post' action='messages.php?view=$view' onsubmit='return validateMessage()'>
        <fieldset data-role="controlgroup" data-type="horizontal">
          <legend>Type here to leave a message</legend>
          <input type='radio' name='pm' id='public' value='0' checked='checked'>
          <label for="public">Public</label>
          <input type='radio' name='pm' id='private' value='1'>
          <label for="private">Private</label>
        </fieldset>
        <textarea name='text' id='text'></textarea>
        <input data-transition='slide' type='submit' value='Post Message'>
      </form><br>
      $error
      <script>
        function validateMessage() {
          var text = document.getElementById('text').value;
          if (text.length <= 10) {
            alert('The message must be more than 10 characters long');
            return false;
          }
          return true;
        }
      </script>
_END;

    date_default_timezone_set('UTC');

    if (isset($_GET['erase']))
    {
      $erase = sanitizeString($_GET['erase']);
      queryMysql("DELETE FROM messages WHERE id=$erase AND recip='$user'");
    }
    
    $query  = "SELECT * FROM messages WHERE recip='$view' ORDER BY time DESC";
    $result = queryMysql($query);
    $num    = $result->num_rows;
    
    for ($j = 0 ; $j < $num ; ++$j)
    {
      $row = $result->fetch_array(MYSQLI_ASSOC);

      if ($row['pm'] == 0 || $row['auth'] == $user || $row['recip'] == $user)
      {
        echo date('M jS \'y g:ia:', $row['time']);
        echo " <a href='messages.php?view=" . $row['auth'] .
             "'>" . $row['auth']. "</a> ";

        if ($row['pm'] == 0)
          echo "wrote: &quot;" . $row['message'] . "&quot; ";
        else
          echo "whispered: <span class='whisper'>&quot;" .
            $row['message']. "&quot;</span> ";

            echo "<form method='post' action='messages.php?view=$view' class='like-dislike-buttons'>
            <input type='hidden' name='messageId' value='" . $row['id'] . "'>
            <input type='image' name='like' src='arrow-u-black.png' alt='Like' style='width: 20px; height: 25px;'>" . $row['likes'] . "
             <input type='image' name='dislike' src='arrow-d-black.png' alt='Dislike' style='width: 20px; height: 25px;'>" . $row['dislikes'] . "
          </form>";
    
        if ($row['recip'] == $user)
          echo "[<a href='messages.php?view=$view" .
               "&erase=" . $row['id'] . "'>erase</a>]";

        echo "<br>";
      }
    }
  }

  if (!$num)
    echo "<br><span class='info'>No messages yet</span><br><br>";

  echo "<br><a data-role='button'
        href='messages.php?view=$view'>Refresh messages</a>";
?>

<?php
function isMessageValid($message, $minLength)
{
    return strlen($message) > $minLength;
}
?>

<style>
form {
    margin: 20px 0;
}

textarea {
    width: 100%;
    height: 100px;
    margin-bottom: 10px;
}

input[type="submit"] {
    display: block;
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}

.error {
    color: red;
    font-weight: bold;
}

.success {
    color: green;
    font-weight: bold;
}

.like-dislike-buttons {
    display: flex;
    gap: 10px;
}

.like-dislike-buttons input[type="image"] {
    width: 30px;
    height: 30px;
    cursor: pointer;
}
</style>

    </div><br>
  </body>
</html>
