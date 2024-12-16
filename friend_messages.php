<?php 
  require_once 'header.php';

  if (!$loggedin) die("</div></body></html>");

  if (isset($_GET['view']))
  {
    $view = sanitizeString($_GET['view']);
    
    if ($view == $user) $name = "Your";
    else                $name = "$view's";
    
    echo "<h3>Messages from $view</h3>";
    showFriendMessages($user, $view);
  }
  else
  {
    echo "<h3>No user selected.</h3>";
  }
?>
    </div>
  </body>
</html>