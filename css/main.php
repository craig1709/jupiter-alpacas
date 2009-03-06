<?php

header('Content-type: text/css');

$page = (isset($_GET['page'])) ? $_GET['page'] : NULL;

$dir = scandir('./');
array_shift($dir); //remove ./
array_shift($dir); //remove ../

$css = array();
for ($i=0; $i<count($dir); $i++)
{
	$filext = substr($dir[$i], strlen($dir[$i])-3);
	if ($filext == 'css') {
		$css[$i]['header'] = strtoupper(str_replace('.', ' ', $dir[$i]));
		$css[$i]['file'] = $dir[$i];
	}
}

sort($css);

for ($i=0; $i<count($css); $i++) {
	$css[$i]['header'] = $i+1 . '. ' . $css[$i]['header'];
}

echo "/*******************\r\n";
echo "CONTENTS\r\n\r\n";

for ($i=0; $i<count($css); $i++)
{
	echo $css[$i]['header'] . "\r\n";
}

echo "*******************/\r\n\r\n";

foreach ($css as $file) {
	$comment = "/*******************\r\n";
	$comment .= "* " . $file['header'];
	for ($i=0; $i<((17-strlen($file['header']))); $i++) { $comment .= ' '; }
	$comment .= "*\r\n*******************/\r\n\r\n";
	
	$content = file_get_contents($file['file']);
	if ($page != NULL) {
		$content = str_replace('/images/', '/images/garments/', $content);
	}
	echo $comment . $content . "\r\n\r\n";
}

?>