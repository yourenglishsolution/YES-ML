<?
############################## Epistema (c) 2001 - 2003 #######################
## EPISTEMA Easyquizz
##
## Easyquizz - disk repport image
##
## $Revision: 741 $
##
## $Log: image.php,v $
## Revision 1.1  2003/05/06 10:35:52  bertrand
## initial release
##
###############################################################################

$ImageWidth = 12;
$ImageHeight = 12;

$im = imagecreate($ImageWidth, $ImageHeight);

$aColor = hex2dec($_GET["color"]);

$SquareColor = imagecolorallocate ($im,  $aColor['r'], $aColor['g'],  $aColor['b']);

imagefilledrectangle($im, 0, 0, $ImageWidth, $ImageHeight, $SquareColor);

header ("Content-type: image/png"); 
imagepng($im);

imagecolordeallocate($im, $SquareColor);

imagedestroy($im);

function hex2dec($hex)
{
	$color = str_replace('#', '', $hex);
	$ret = array(
		'r' => hexdec(substr($color, 0, 2)),
		'g' => hexdec(substr($color, 2, 2)),
		'b' => hexdec(substr($color, 4, 2))
	);
	return $ret;
}
?>