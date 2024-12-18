<?php
require_once 'header.php';

date_default_timezone_set('America/Los_Angeles');

if (!$loggedin) die("</div></body></html>");

if (isset($_GET['view'])) {
    $view = sanitizeString($_GET['view']);
} else {
    die("No user selected for chat.");
}

if (isset($_POST['text'])) {
    $text = sanitizeString($_POST['text']);
    $time = time();
    queryMysql("INSERT INTO chat (`from`, `to`, pm, time, message) VALUES('$user', '$view', '0', '$time', '$text')");
    header("Location: chat.php?view=$view");
}

echo "<h3>Chat with $view</h3>";
showChat($view);

echo <<<_END
<form method='post' action='chat.php?view=$view'>
  <textarea name='text' cols='40' rows='3'></textarea><br>
  <input type='submit' value='Send Message'>
</form><br>
<a data-role='button' data-inline='true' data-icon='back' data-transition="slide" href='chat_list.php'>Back to Chat List</a>
_END;

require_once 'footer.php';

function showChat($view) {
    global $user;
    $result = queryMysql("SELECT * FROM chat WHERE (`from`='$view' AND `to`='$user') OR (`from`='$user' AND `to`='$view') ORDER BY time");
    $num = $result->num_rows;

    $lastDate = '';

    echo "<style>
        .date { text-align: center; margin: 10px 0; font-weight: bold; }
        .message { margin: 5px 0; }
    </style>";

    for ($j = 0 ; $j < $num ; ++$j) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $time = date('H:i:s', $row['time']);
        $date = date('m-d-Y', $row['time']);

        if ($date != $lastDate) {
            echo "<div class='date'><b>$date</b></div>";
            $lastDate = $date;
        }

        if ($row['from'] == $view) {
            echo "<div class='message'><b>[$time] $view:</b> " . stripslashes($row['message']) . "</div>";
        } else {
            echo "<div class='message'><b>[$time] $user:</b> " . stripslashes($row['message']) . "</div>";
        }
    }
}
?>