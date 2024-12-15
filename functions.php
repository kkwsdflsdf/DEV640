<?php // Example 27-1: functions.php
  $dbhost  = 'localhost';    // Unlikely to require changing
  $dbname  = 'dev640app';   // Modify these...
  $dbuser  = 'dev630';   // ...variables according
  $dbpass  = 'localhost';   // ...to your installation

  $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if ($connection->connect_error) die("Fatal Error");

  function createTable($name, $query)
  {
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br>";
  }

  function queryMysql($query)
  {
    global $connection;
    $result = $connection->query($query);
    if (!$result) die("Fatal Error");
    return $result;
  }

  function destroySession()
  {
    $_SESSION=array();

    if (session_id() != "" || isset($_COOKIE[session_name()]))
      setcookie(session_name(), '', time()-2592000, '/');

    session_destroy();
  }

  function sanitizeString($var)
  {
    global $connection;
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return $connection->real_escape_string($var);
  }

  function showProfile($user)
  {
    if (file_exists("$user.jpg"))
      echo "<img src='$user.jpg' style='float:left;'>";

    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

    if ($result->num_rows)
    {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      echo stripslashes($row['text']) . "<br style='clear:left;'><br>";
    }
    else echo "<p>Nothing to see here, yet</p><br>";
  }

  function showFriendMessages($user, $friend)
{
  $result = queryMysql("SELECT * FROM messages WHERE auth='$friend' AND recip='$user' ORDER BY time DESC");

  if ($result->num_rows)
  {
    while ($row = $result->fetch_array(MYSQLI_ASSOC))
    {
      $auth = htmlspecialchars($row['auth']);
      $time = htmlspecialchars($row['time']);
      $message = htmlspecialchars($row['message']);

      echo "<div><strong>$auth</strong> at $time<br>$message<br><br></div>";
    }
  }
  else
  {
    echo "<p>No messages from $friend.</p>";
  }
}
?>
