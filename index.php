<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<!--
  Please see the README.md ヾ(⌐■_■)ノ
-->
<?php
  // don't allow anyone to navigate to a directory above that of this file
  if (strlen(urldecode(realpath(($_GET["dir"] ? dirname(__FILE__)."/".$_GET["dir"] : __FILE__)))) < (strlen(__FILE__)-strlen(basename(__FILE__)))) {
    $_GET["dir"] = null;
    $dir = opendir(".");
  }
  $file = htmlentities($_GET["file"], ENT_QUOTES);

  define("title", basename(realpath(($_GET["dir"] ? $_GET["dir"] : dirname(__FILE__)))))
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title><?php echo title; if ($file) echo " &mdash; ".$file; ?></title>
  <style type="text/css">
    /* simplified reset.css
    http://meyerweb.com/eric/tools/css/reset/ */
    html,body,div,object,h1,p,a,img,ul,li{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent;}
    ul{list-style:none;}

    body{font-family:Helvetica,Arial,sans-serif;font-size:10px;color:#000;line-height:16px;background-color:#fff;}
    a{color:#000;text-decoration:none;}
    a:hover{background-color:#ddd;}
    a.folder{font-weight:bold;}
    a.parent{font-style:italic;}
    a.current{color:#aaa;}
    a.current:hover{background-color:transparent;}
    h1{width:250px;margin-bottom:10px;font-size:12px;font-weight:bold;line-height:100%;}
    h1 a:hover{background-color:transparent;}
    br.clear{clear:both;}
    div#container{padding:20px 10px;}
    ul#files{float:left;width:250px;margin-right:10px;padding-top:10px;border-top:1px solid #ddd;}
    div#file{float:left;padding:0 10px 20px 0;position:absolute;left:270px;}
    div#file p{margin-top:10px;}
    div#file p a{font-weight:bold;}
  </style>
</head>
<body>
  <div id="container">
<?php
  // Define the extension used when defining the width and height of a video file.
  define("dim_ext", ".dim");

  // Define extensions for different types of files.
  $extImageFiles = array(".gif", ".jpg", ".jpeg", ".png", ".tif", ".tiff", ".bmp", ".tga");
  $extMusicFiles = array(".mp3", ".wav", ".mid", ".aif", ".aiff");
  $extMovieFiles = array(".mov", ".mpg", ".mpeg", ".avi", ".wmv", ".mp4");
  $extTextFiles = array(".txt", ".text", ".js", ".css", ".xml", ".log", ".dtd");
  $extWebFiles = array(".html", ".htm", ".shtml", ".shtm", ".php", ".php3", ".asp", ".jsp", ".cfm", ".cfml", ".java", ".class", ".pl", ".cgi");
  $extFlashFiles = array(".swf", ".flv");

  $files = array();
  $folders = array();
  $dir = opendir(($_GET["dir"] ? $_GET["dir"] : "."));

  // Loop through the directory to add all the files and folders to their appropriate arrays, ignoring system files.
  while ($file = readdir($dir)) {
    if (is_dir(($_GET["dir"] ? $_GET["dir"]."/".$file : $file)) && $file[0] != ".") {
      $folders[] = basename($file);
    } elseif ($file[0] != "." && $file != basename(__FILE__) && $file != "error_log" && substr($file, -strlen(dim_ext)) != dim_ext) {
      $files[] = basename($file);
    }
  }

  natcasesort($folders);
  natcasesort($files);

  $file = ($_GET["dir"] ? $_GET["dir"]."/" : "").$_GET["file"];

  // Check if the given file exists.
  if (file_exists($file)) $fileexists = true;

  // Grab the extension from the file.
  function type($file) {
    $pos = strrpos($file, '.');
    return substr($file, $pos, strlen($file));
  }
  $type = strtolower(type($file));

  // Determine if the file type is in the given array (those defined earlier).
  function isCorrectType($type,$array) {
    for ($i = 0; $i < count($array); $i++) {
      if ($type == $array[ $i ]) return true;
    }
    return false;
  }

  // Calculate the file size.
  if ($fileexists) {
    $size = sprintf("%u", filesize($file));
    $sizemb = round(($size/1048576), 2);
  }

  // Begin the HTML generation.
  echo "    <h1><a href=\"".($_GET["dir"] ? "?dir=".urlencode($_GET["dir"]) : "")."\">".title."</a></h1>",
       "\n    <ul id=\"files\">\n";

  // First link to the parent directory.
  if (realpath(($_GET["dir"] ? dirname(__FILE__)."/".$_GET["dir"] : dirname(__FILE__))) != dirname(__FILE__)) {
    $parent_dir = urlencode(substr(realpath($_GET["dir"] ? $_GET["dir"]."/.." : dirname(__FILE__)), strlen(dirname(__FILE__))+1));
    echo "      <li><a href=\"".(strlen($parent_dir) < 1 ? "." : "?dir=$parent_dir")."\" class=\"parent folder\">Parent directory</a></li>\n";
  }

  // HTML for the filelist links. The first is for folders, the second is for files.
  function givefolderLink(&$link) {
    $link = "      <li><a href=\"?dir=".($_GET["dir"] ? urlencode($_GET["dir"]."/".$link) : urlencode($link))."\" class=\"folder\">" . (strlen($link) < 35 ? $link : substr_replace($link, "...", 35)) . "</a></li>";
  }
  function giveLink(&$link) {
    if ($link == $_GET["file"]) $current = ' class="current"';
    $link = "      <li><a href=\"".($_GET["dir"] ? "?dir=".urlencode($_GET["dir"])."&amp;file=".urlencode($link) : "?file=$link")."\"$current>" . (strlen($link) < 35 ? $link : substr_replace($link, "...", 35)) . "</a></li>";
  }

  // Run the folder and file arrays through the above functions to generate the HTML.
  array_walk($folders,"givefolderLink");
  array_walk($files,"giveLink");
  // devnote: to increase speed of the script, don't use a loop

  // Display the folder and file links.
  echo implode("\n", $folders); if (count($folders) > 0) echo "\n";
  echo implode("\n", $files);
  echo "\n    </ul>";

  // Display the file based on its type.
  if ($file != "" && $_GET["file"]) {
    echo "\n    <div id=\"file\">",
         "\n      ";
    if (!$fileexists && strlen($file) > 0) {
      echo "<p>The file you requested does not exist.</p>";
    } elseif (isCorrectType($type,$extImageFiles)) {
      echo "<img src=\"$file\" alt=\"".basename($file)."\"/>";
    } elseif (isCorrectType($type,$extMusicFiles)) {
      echo "<embed src=\"$file\" height=\"16px\" autoplay=\"false\"/>";
    } elseif (isCorrectType($type,$extMovieFiles)) {
      echo "<embed src=\"$file\"", (file_exists($file.dim_ext) ? " ".readfile($file.dim_ext) : " width=\"450\" height=\"450\""), " autoplay=\"false\"/>";
    } elseif (isCorrectType($type,$extTextFiles)) {
      echo "<p><code>".nl2br(htmlspecialchars(file_get_contents($file)))."</code> </p>";
    } elseif (isCorrectType($type,$extWebFiles)) {
      echo "<p>This file is a web document. <a href=\"$file\">Launch Webpage</a>.</p>";
    } elseif (isCorrectType($type,$extFlashFiles)) {
      echo "<object type=\"application/x-shockwave-flash\" data=\"$file\"", (file_exists($file.dim_ext) ? " ".readfile($file.dim_ext) : ''), "><param name=\"movie\" value=\"$file\"/></object>";
    }
    // If the file does not fit any of the extensions defined previously, allow the user to download it.
    elseif ($file != "")
    {
      echo "<p>The file you are about to download is $sizemb mb. <a href=\"$file\">Download File</a>.</p>";
    }
  }

  // And that's what it's all about.
?>

    </div>
    <br class="clear" />
  </div>
</body>
</html>
