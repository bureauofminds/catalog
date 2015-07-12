<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<style>
	h1 {font: 100%/2 Helvetica, Arial, sans-serif; font-weight: 100;}
	.container {font: 83%/1.4 Helvetica, Arial, sans-serif;}
	</style>
</head>
<body>
	<h1>File List</h1>
	<div class="container">
	<?php 
	$files = array(); 
	$dir = opendir('.'); 
	print_r($file); 
	while($file = readdir($dir)) { if($file[0] != "." && $file != 'index.php' && $file != "error_log") $files[] = $file; } 
	sort($files); 
	function giveLink(&$item, $key){ $item = "<a href='$item'>$item</a>"; } 
	array_walk($files,'giveLink'); 
	echo implode("<br /> \n \t",$files); 
	?></div>
</body>
</html>

