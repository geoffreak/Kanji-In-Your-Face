<?php
$db = mysql_connect("localhost", "root", "root");
if ($db == false)
{
	print('Connection failure');
	exit;
}
if (mysql_select_db("kanji", $db) == FALSE) 
{ 
	print("You cannot use the 'kanji' database"); 
	exit;
} 
function query($query)
{
	global $db;
	$success = mysql_query($query, $db); 
	if ($success == FALSE) 
	{ 
		print("Your query failed: " . mysql_error($db)); 
		exit;
	}
	return $success;
}
query("SET CHARACTER SET 'utf8'");
$query = query('SELECT * FROM `heisig` ORDER BY ' . 'rand( )' . ' LIMIT 1');
$row = mysql_fetch_assoc($query);
$pattern[] = '#\[d\](.*?)\[/d\]#ms';
$replace[] = '$1';
$row['story'] = preg_replace($pattern, $replace, $row['story']);
function load_image($imgname, $type)
{
    /* Attempt to open */
	if ($type == 'jpeg')
	{
    	$im = @imagecreatefromjpeg($imgname);
	}
	else if ($type == 'gif')
	{
		$im = @imagecreatefromgif($imgname);
	}
	else if ($type == 'png')
	{
		$im = @imagecreatefrompng($imgname);
	}
    /* See if it failed */
    if(!$im)
    {
        /* Create a blank image */
        $im  = imagecreatetruecolor(500, 150);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);

        imagefilledrectangle($im, 0, 0, 500, 150, $bgc);

        /* Output an error message */
        imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}
header('Content-Type: image/png');
$font = 'ipam.ttf';
$font2 = 'handwrite.ttf';
$font3 = 'cambria.ttf';
$img = load_image('kanjiface_sm.png', 'png');
$white = imagecolorallocate($img, 255, 255, 255);
$black = imagecolorallocate($img, 0, 0, 0);
imagettftext($img, 50, 0, 40, 105, $black, $font, $row['kanji']);
$bbox = imagettfbbox(12, 0, $font2, $row['definition']);
$x = $bbox[0] + (30 / 2) - ($bbox[4] / 2) + 60;
$y = $bbox[1] + (130 / 2) - ($bbox[5] / 2) + 55;
//print $x . ' ' . $y . '<br>';
//imagettftext($img, 10, 0, 30, 140, $black, $font, $x . ' ' . $y);
imagettftext($img, 12, 0, $x, $y, $black, $font2, $row['definition']);


$char_lim = 55;
$remainder = $row['story'];
$lines = array();
$i = 0;
while (strlen($remainder) && $i <= 7)
{
	$temp = substr($remainder, 0, $char_lim - 1);
	$last_space = strrpos($temp, ' ');
	if (!$last_space || strlen($remainder) < $char_lim)
	{
		$lines[] = substr($remainder, 0, $char_lim);
		$remainder = substr($remainder, $char_lim + 1);
	}
	else
	{
		$lines[] = substr($remainder, 0, $last_space);
		$remainder = substr($remainder, $last_space + 1);
	}
	$i++;
}
$k = 0;
foreach ($lines as $line)
{
	imagettftext($img, 10, 0, 150, 50 + ($k * 13), $black, $font3, $line);
	$k++;
}
imagepng($img);
imagedestroy($img);
?>