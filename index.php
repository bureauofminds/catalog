<!-- 
	Indexer 1.1 was coded, developed, and envisioned by Tim Erickson
	Please do not steal the code, it belongs to me. I may decide to release it as open source in the future.
	With help from friends. exoinverts.com, weirdbro.com, studiopd.com, sparkbomb.com	
	
	README:
	This is a simple index file that can be thrown in any folder and will manage and display all files and folders in the directory it resides.
	It has several specific features, including the ability to read photo EXIF data, play music, and even movies.
	Because php currently has no way of auto-detecting the size of the movie file, the script uses a nifty work-around to show the video properly.
	Simply create a txt file for the movie found in the folder with the width and height information in html.
	The naming schema for these txt files is *.desc.txt where * represents the name of the movie file.
	For example: Video.mov.desc.txt would be the describing info for the Video.mov file.
	Since its html, you can add any tags for the movie that you may want, such as the autoplay tag.
	For example: height="430" width="612" autoplay="false"
	Quick tip, you should add about 16 pixels to the height of each video, to allow for the player controls to show up.
	
	KNOWN BUG LIST:
	It doesn't like files with an ampersand (&) in the name 
	Will list, but not properly handle invisible files
	Too much padding when no folders exist
	HTML in txt/js/css/xml files can override the page styling
	TextFiles have bad line-breaking / spacing in the source
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/
	TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>Indexer File Gallery - <?php echo $_GET["file"] ?></title>
	<style>
	body {background: #999; font: 12px Helvetica, Arial, sans-serif; color: #000;}
	h1 {font: 18px line-height: 35px; letter-spacing: -2px; font-weight: bold;}
	img {border: 0;}
	a, a:visited, a:link {color: #000; padding: 0.2em;}
	a:hover {background: #f00; color: #fff;}
	#container {padding: 0 0 20px 0; float: left; display: inline;}
	#filelist {float: left; width: 250px;}
	#file_container {position: absolute; left: 250px; top: 30px; margin: 0 20px 0 0;}
	.box {background: #fff; padding: 4px; color: #000;}
	#exif {display: none; position: fixed; max-height: 100%; margin: 40px 0 0 10px; padding: 4px; background: #fff; font: 12px Courier, monospace; overflow: scroll;}
	</style>
	
	<script type="text/javascript">
	<!-- 
	function hideElement(exif) { if (document.getElementById) {document.getElementById(exif).style.display = 'none';} }
	function showElement(exif) { if (document.getElementById) {document.getElementById(exif).style.display = 'block';} }
	// -->
	</script>
</head>
<body>
<div id="container">
<?php

	define("desc_txt", ".desc.txt");
	$files = array();
	$folders = array();
	$dir = opendir(".");
	
	// looping through the directory to add all the files and folders to their seperate arrays, ignoring system files
	while($file = readdir($dir)) 
	{
		if(is_dir($file) && $file != "." && $file != "..") 
		{ 
			$folders[] = $file; 
		}
		elseif($file[0] != "." && $file != end(explode('/',__FILE__)) && $file != "error_log" && substr($file, -9) != ".desc.txt") 
		{ 
			$files[] = $file; 
		}
	}
	
	natcasesort($folders);
	natcasesort($files);
	
	// the following if checks to see if the given file exists, if it doesnt, it tells you so later on
	if (file_exists($_GET["file"]))
	{
		$fileexists = true;
	} 
	else 
	{
		$fileexists = false;
	}
	
	// the two functions below create the html code for the filelist links, one is for the folder array, and the other is for the regular file array
	function giveLink(&$link) 
	{
		if (strlen($link) >= 35) 
		{ 
			$link = "\n \t\t<a href=\"?file=$link\">" . substr_replace($link, "...", 35) . "</a>";
		}
		else 
		{ 
			$link = "\n \t\t<a href=\"?file=$link\">$link</a>"; 
		}
	}
	function givefolderLink(&$link) 
	{
		if (strlen($link) >= 35) 
		{ 
			$link = "\n \t\t&#172; <a href=\"$link\">" . substr_replace($link, "...", 35) . "</a>";
		}
		else 
		{ 
			$link = "\n \t\t&#172; <a href=\"$link\">$link</a>"; 
		}
	}

	$file = $_GET["file"];
	// calculating the file size
	if ($fileexists)
	{
		$size = sprintf("%u", filesize($file));
		$sizemb = round(($size/1048576), 2);
	}
	
	function isCorrectType($type,$array)
	{		
		for($i = 0; $i < count($array); $i++)
		{
			if($type == $array[ $i ])
			{
				return true;
			}
		}				
		return false;
	}
	
	// this function grabs the extension from the file
	function type($file) 
	{
	  $pos = strrpos($file, '.');
	  $str = substr($file, $pos, strlen($file));
	  return $str;
	}
	$type = strtolower(type($file));
	
	// below are filetype definitions
	$extImageFiles = array(".gif", ".png", ".bmp", ".tga");
	$extMusicFiles = array(".mp3", ".wav", ".mid", ".aif", ".aiff");
	$extMovieFiles = array(".mov", ".mpg", ".mpeg", ".avi", ".wmv", ".mp4");
	$extTextFiles = array(".txt", ".text", ".js", ".css", ".xml", ".log");
	$extWebFiles = array(".html", ".htm", ".shtml", ".shtm", ".php", ".php3", ".asp", ".jsp", ".cfm", ".cfml", ".java", ".class", ".pl", ".cgi");
	$extFlashFiles = array(".swf", ".flv");
	$extExifFiles = array(".jpg", ".jpeg", ".tif", ".tiff");
	
	// this reads the exif data from a jpg or a tif, and formats it for output
	$exifdata = array();
	if (isCorrectType($type,$extExifFiles) && $fileexists) 
	{
		$exif = @exif_read_data($file, 0, true);
		foreach ($exif as $key => $section) {
		    foreach ($section as $name => $val) {
		        $exifdata[] =  "\t\t$name: $val<br />\n";
		    }
		}
	}
	
	// walk it out! spits out pretty html to each array item
	array_walk($folders,"givefolderLink");
	array_walk($files,"giveLink");
	// devnote: to increase speed of the script, don't use a loop
	
	// this is what outputs the filelist, first it spits pretty html 
	echo "\n \t<div id=\"filelist\">\n \t";
	echo "<h1>", basename(dirname(__FILE__)), "</h1>";
	// then it outputs two arrays, one for the folders and another for the regular files
	echo implode($folders, " <br />"), "<br />\n";
	echo implode($files, " <br />");
	echo "\n \t</div>";

	
	// we have now begun the file container, and are checking the file by type, making the files look and interact like each different filetype should
	if($file != "")
	{
		echo "\n \t<div id=\"file_container\">";
	}
	if(!$fileexists && $file != "")
	{
		echo "The file you requested does not exist.";
	}
	elseif(isCorrectType($type,$extImageFiles))
	{
		echo "<img src=\"$file\" class=\"box\"/>";
	}
	elseif(isCorrectType($type,$extExifFiles)) 
	{
		echo "\n \t\t<a href=\"javascript:showElement('exif')\">Show EXIF Data</a> <a href=\"javascript:hideElement('exif')\">Hide EXIF Data</a> <br /> \n \t\t<img src=\"$file\" class=\"box\"/>\n";
		echo "\t</div>\n";
		echo "\t<div id=\"exif\">\n \t";
		foreach ($exifdata as $value) { 
			echo preg_replace("/[^0-9a-zA-Z =\"_<>:\/.-]/","",$value), "\n \t";
		}
		echo "</div>\n";
	}
	elseif(isCorrectType($type,$extMusicFiles)) 
	{
		echo "<embed src=\"$file\" height=\"16px\" autoplay=\"false\" class=\"box\"/>";
	}
	elseif(isCorrectType($type,$extMovieFiles)) 
	{
		echo "The movie you are watching is $sizemb mb. <br /><embed src=\"$file\" ", readfile($file . desc_txt), " autoplay=\"false\" class=\"box\"/>";
	}
	elseif(isCorrectType($type,$extTextFiles)) 
	{
		echo "<div class=\"box\">", nl2br(file_get_contents($file)), "</div>";
	}
	elseif(isCorrectType($type,$extWebFiles)) 
	{
		echo "This file is a web document. <a href=\"$file\" class=\"box\">Launch Webpage</a>";
	}
	elseif(isCorrectType($type,$extFlashFiles)) 
	{
		echo "<object type=\"application/x-shockwave-flash\" data=\"$file\" ", readfile($file . desc_txt), " class=\"box\"><param name=\"movie\" value=\"$file\"/></object>";
	}
	// ok, its not anything that shows in the browser, so were going to let the user download this file
	elseif($file != "") 
	{
		echo "The file you are about to download is $sizemb mb. <a href=\"$file\" class=\"box\">Download File</a>";
	}
	
	if(!isCorrectType($type,$extExifFiles) && $file != "") {
		echo "</div>\n";
	}
	
?>

</div>
</body>
</html>