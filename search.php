<?php
require_once 'header.php';

if (!$loggedin) die("</div></body></html>");

// 如果用户通过表单提交了搜索关键字
$query = "";
if (isset($_POST['keyword']))
{
    $keyword = sanitizeString($_POST['keyword']);
    // 这里假设location和interests保存在profiles表中：
    // 根据实际数据表结构进行调整
    $result = queryMysql("SELECT members.user FROM members 
                          LEFT JOIN profiles ON members.user = profiles.user
                          WHERE members.user LIKE '%$keyword%' 
                          OR profiles.location LIKE '%$keyword%'
                          OR profiles.interests LIKE '%$keyword%'
                          OR profiles.text LIKE '%$keyword%'
                          ORDER BY members.user");

    $num = $result->num_rows;
    if ($num > 0)
    {
        echo "<h3>Search Results for '$keyword'</h3><ul>";
        for ($j = 0 ; $j < $num ; ++$j)
        {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $u = $row['user'];
            echo "<li><a data-transition='slide' href='members.php?view=$u'>$u</a></li>";
        }
        echo "</ul>";
    }
    else
    {
        echo "<h3>No matches found for '$keyword'</h3>";
    }
}

// 搜索表单
echo <<<_END
<form method='post' action='search.php'>
  <input type='text' name='keyword' placeholder='Search by name, location, interest...'>
  <input type='submit' value='Search'>
</form>
_END;

echo "</div></body></html>";
?>
