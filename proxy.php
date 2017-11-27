<?php
/*
$serverdomain = "www.brainwaveweb.com";

if (stristr($_SERVER["SERVER_NAME"], $serverdomain) != FALSE)
{
	if (stristr($_GET["p"], "www.brainwaveweb.com") != FALSE)
	{
		header("Location: ".$_GET["p"]); 
		exit;
	}
}

// Set your return content type
header('Content-type: application/xml');
*/
// Website url to open
$dasite = $_GET["p"];
$daurl = "http://www.dnsstuff.com/tools/traversal/?domain=".$dasite."&type=A";

// Get that website's content
$handle = fopen($daurl, "r");

// If there is something, read and return
if ($handle) {
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        echo $buffer;
    }
    fclose($handle);
}
?>