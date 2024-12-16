<?php // Example 27-8: profile.php
  require_once 'header.php';

  if (!$loggedin) die("</div></body></html>");

  echo "<h3>Your Profile</h3>";
  showLocationAndInterests($user);

  // 获取已有资料
  $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
  if ($result->num_rows)
  {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      $text = stripslashes($row['text']);
      $location = stripslashes($row['location']);
      $interests = stripslashes($row['interests']);
  }
  else 
  {
      $text = "";
      $location = "";
      $interests = "";
  }

  // 如果表单提交了新资料
  if (isset($_POST['text']))
  {
      $text = sanitizeString($_POST['text']);
      $text = preg_replace('/\s\s+/', ' ', $text);

      // 同时获取新提交的 location 和 interests
      $location = sanitizeString($_POST['location']);
      $interests = sanitizeString($_POST['interests']);

      if ($result->num_rows)
           queryMysql("UPDATE profiles SET text='$text', location='$location', interests='$interests' WHERE user='$user'");
      else queryMysql("INSERT INTO profiles (user, text, location, interests) VALUES('$user', '$text', '$location', '$interests')");
  }
  else
  {
    if ($result->num_rows)
    {
      $row  = $result->fetch_array(MYSQLI_ASSOC);
      $text = stripslashes($row['text']);
    }
    else $text = "";
  }

  $text = stripslashes(preg_replace('/\s\s+/', ' ', $text));

  if (isset($_FILES['image']['name']))
  {
    $saveto = "$user.jpg";
    move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
    $typeok = TRUE;

    switch($_FILES['image']['type'])
    {
      case "image/gif":   $src = imagecreatefromgif($saveto); break;
      case "image/jpeg":  // Both regular and progressive jpegs
      case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
      case "image/png":   $src = imagecreatefrompng($saveto); break;
      default:            $typeok = FALSE; break;
    }

    if ($typeok)
    {
      list($w, $h) = getimagesize($saveto);

      $max = 100;
      $tw  = $w;
      $th  = $h;

      if ($w > $h && $max < $w)
      {
        $th = $max / $w * $h;
        $tw = $max;
      }
      elseif ($h > $w && $max < $h)
      {
        $tw = $max / $h * $w;
        $th = $max;
      }
      elseif ($max < $w)
      {
        $tw = $th = $max;
      }

      $tmp = imagecreatetruecolor($tw, $th);
      imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
      imageconvolution($tmp, array(array(-1, -1, -1),
        array(-1, 16, -1), array(-1, -1, -1)), 8, 0);
      imagejpeg($tmp, $saveto);
      imagedestroy($tmp);
      imagedestroy($src);
    }
  }

  showProfile($user);

echo <<<_END
<form data-ajax='false' method='post' action='profile.php' enctype='multipart/form-data'>
  <h3>Enter or edit your details and/or upload an image</h3>
  <textarea name='text'>$text</textarea><br>
  Location: <input type='text' name='location' value='$location'><br>
  Interests: <input type='text' name='interests' value='$interests'><br><br>
  Image: <input type='file' name='image' size='14'><br><br>
  <input type='submit' value='Save Profile'>
</form>
_END;
?>

