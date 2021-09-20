<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Dev&#923; 2</title>
  <meta name="author" content="Miguel Nunes">
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      font-size: 14px;
      padding-left: 20px;
    }
    a {
      color: rgb(40, 95, 156);
      text-decoration: none;
    }
    a:hover {
      color: rgb(115, 164, 221);
    }
    hr {
      border: 0;
      background: rgb(214, 214, 214);
      height: 1px;
      margin: 40px 0;
    }
    code {
      background: rgb(241, 241, 241);
      padding: 2px 5px;
    }
  </style>
</head>

<body>

<?php

if (\class_exists('\\Phalcon\Support\Version')) {
    $link = @mysqli_connect('localhost', 'root', '') or die('MYSQL INOP!');
    @mysqli_select_db($link, 'mysql') or die('MYSQL INOP!');
    echo '<!--phalcon&mysql.working-->';
}

?>

<h1>Dev&#923; 2</h1>

<p><b>Welcome!</b></p>

<p>Make sure that <code>127.0.0.1 cp.test</code> is in your hosts file so you can access the DevA 2 control panel.</p>
<p><a href="http://cp.test/">&rsaquo; Open the Control Panel</a></p>

<hr>

<p><b>Directories:</b></p>

<?php

$dirs = \glob('/var/www/*');

foreach ($dirs as $dir) {
    if (\is_dir($dir)) {
        $dir = \str_replace('/var/www/', '', $dir);
        echo '- <a href="/' . $dir . '">' . $dir . '</a><br>';
    }
}

?>

</body>

</html>
