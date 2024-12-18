<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

echo "<h3>Chat with Members</h3>";

$result = queryMysql("SELECT user FROM members WHERE user != '$user' ORDER BY user");
$num = $result->num_rows;

echo "<ul>";
for ($j = 0; $j < $num; ++$j) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    echo "<li><a href='chat.php?view=" . $row['user'] . "'>" . $row['user'] . "</a></li>";
}
echo "</ul>";

require_once 'footer.php';
?>