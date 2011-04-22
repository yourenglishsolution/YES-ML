<?

// To use, call :
// camembert.php?v[]=340&t[]=les%20bleus&c[]=0000FF&v[]=120&t[]=les%20rouges&c[]=FF0000

$trans = array_flip(get_html_translation_table(HTML_ENTITIES));
function PrepareText($str)
{
	global $trans;
	return strip_tags(str_replace('"', '\"', strtr($str, $trans)));
}

$v = array();
$Total = 0;


$ImageWidth = 400;
$ImageHeight = 300;

if ((isset($_GET['ImageWidth']))&&(isset($_GET['ImageHeight'])))
{
	$ImageWidth = $_GET['ImageWidth'];
	$ImageHeight = $_GET['ImageHeight'];
}

$im = imagecreate($ImageWidth, $ImageHeight);

foreach ($_GET['v'] as $k => $c)
{
	$v[] = array("value" => (0+$c),
							 "string" => PrepareText(stripslashes($_GET['t'][$k])));
	$Total += (0+$c);

	if (isset($_GET['c'][$k]))
	{
		$aColor = hex2dec($_GET['c'][$k]);
		$colors[] = imagecolorallocate ($im,  $aColor['r'], $aColor['g'],  $aColor['b']);
	}
}

$black    = imagecolorallocate ($im,   0,   0,   0);
$gray     = imagecolorallocate ($im, 100, 100, 100);
$white    = imagecolorallocate ($im, 255, 255, 255);

$colors[] = imagecolorallocate ($im,  51, 204,  51);
$colors[] = imagecolorallocate ($im, 255,  51,  51);
$colors[] = imagecolorallocate ($im,  51,  51, 153);
$colors[] = imagecolorallocate ($im,  51, 153, 255);
$colors[] = imagecolorallocate ($im,  51, 153, 153);
$colors[] = imagecolorallocate ($im, 255,  51,   0);
$colors[] = imagecolorallocate ($im, 255, 153,  51);
$colors[] = imagecolorallocate ($im, 255, 204,  51);
$colors[] = imagecolorallocate ($im, 204, 153,  51);

$Diameter = 190;
$CenterX = $ImageWidth / 2; // - ($ImageWidth - $Diameter) / 2 + 10;
$CenterY = $ImageHeight / 2; // - ($ImageHeight - $Diameter) / 2 + 10;

$TextTops = array();

imagefilledrectangle($im, 0, 0, $ImageWidth, $ImageHeight, $white);

function DrawSection($im, $CenterX, $CenterY, $Diameter, $Diameter, $Start, $End, $Color, $bFill)
{
	if ($Start == $End)
		return;

	imagearc($im, $CenterX, $CenterY, $Diameter, $Diameter, $Start, $End, $Color);

	// To close the arc with 2 lines between the center and the 2 limits of the arc
	$x = $CenterX + (cos(deg2rad($Start))*($Diameter/2));
	$y = $CenterY + (sin(deg2rad($Start))*($Diameter/2));
	imageline($im, $x, $y, $CenterX, $CenterY, $Color);

	$x = $CenterX + (cos(deg2rad($End))*($Diameter/2));
	$y = $CenterY + (sin(deg2rad($End))*($Diameter/2));
	imageline($im, $x, $y, $CenterX, $CenterY, $Color);

	if ($bFill)
	{
		// To fill the arc, the starting point is a point in the middle of the closed space
		$x = $CenterX + (cos(deg2rad(($Start+$End)/2))*($Diameter/4));
		$y = $CenterY + (sin(deg2rad(($Start+$End)/2))*($Diameter/4));
		imagefilltoborder($im, $x, $y, $Color, $Color);
	}
}

// draw the chart
$deg = 270;
$i = 0;

foreach($v as $section)
{
	$Start = $deg;

	if ($Total > 0)
	{
		$End = $deg + round(($section["value"] * 360 / $Total));

		DrawSection($im, $CenterX, $CenterY, $Diameter, $Diameter, $Start, $End, $colors[$i % count($colors)], true);
		DrawSection($im, $CenterX, $CenterY, $Diameter, $Diameter, $Start, $End, $gray, false);

		$deg = $End;
	}

	$i++;
}

// draw the strings
$deg = 270;
$i = 0;

foreach($v as $section)
{
	$Start = $deg;

	if ($Total > 0)
	{
		$End = $deg + ($section["value"] * 360 / $Total);

		$Middle = $Start + ($End - $Start) / 2;
		$x = $CenterX + (cos(deg2rad($Middle))*(10 + $Diameter/2));
		$y = $CenterY + (sin(deg2rad($Middle))*(10 + $Diameter/2));

		// first draw a white rectangle so we can see the text :
		$font = 3;
		$fontwidth = ImageFontWidth($font);
		$fontheight = ImageFontHeight($font);

		while (in_array(round($y), $TextTops))
			$y += $fontheight + 2;

		$TextTops[] = round($y);

		$padding = 3;

		$section["string"] = $section["string"] . ' (' . number_format($section["value"] * 100 / $Total, 2) . '%)';
		$section["string"] = str_replace('&euro;', 'Euro', $section["string"]);
		$section["string"] = wordwrap($section["string"], 30, "\n");
		DrawStringAt($im, $section["string"], $black, $white, $x, $y, true, true, $colors[$i % count($colors)]);

		$deg = $End;
	}

	$i++;
}

header ("Content-type: image/png");
imagepng($im);

foreach ($colors as $color)
	imagecolordeallocate($im, $color);

imagecolordeallocate($im, $black);
imagecolordeallocate($im, $gray );
imagecolordeallocate($im, $white);

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



function DrawStringAt($im, $str, $textColor, $backgroundColor, $x, $y,
											$bCenterH = false, $bCenterV = false, $LabelColor = false)
{
	$padding = 3;

	$ttfFontSize = 8;
	$ttfFontfile = str_replace('camembert.php', 'verdana.ttf', str_replace('\\', '/', __FILE__));

	$x = round($x);
	$y = round($y);

	$lines = explode("\n", $str);

	$rectangleHeight = 0;
	$rectangleWidth = 0;

	$LineHeight = 0;

	foreach ($lines as $line)
	{
		$boundingBox = imagettfbbox($ttfFontSize, 0, $ttfFontfile, utf8_encode($line));

		$ThisLineWidth = max($boundingBox[0],$boundingBox[2],$boundingBox[4],$boundingBox[6]) -
										 min($boundingBox[0],$boundingBox[2],$boundingBox[4],$boundingBox[6]);

		if ($ThisLineWidth > $rectangleWidth)
			$rectangleWidth = $ThisLineWidth;

		$thisHeight = max($boundingBox[1],$boundingBox[3],$boundingBox[5],$boundingBox[7]) -
									min($boundingBox[1],$boundingBox[3],$boundingBox[5],$boundingBox[7]);

		if ($thisHeight > $LineHeight)
			$LineHeight = $thisHeight;
	}

	$rectangleWidth += $padding * 2;
	$rectangleHeight = $LineHeight * count($lines) + $padding;

	if ($LabelColor !== false)
		$rectangleWidth += $ttfFontSize * 1.5;

	if ($bCenterH)
		$x -= $rectangleWidth / 2;

	if ($bCenterV)
		$y -= $rectangleHeight / 2;

	// verify that this text wont start on another one
	global $TextPanels;
	if (!isset($TextPanels))
		$TextPanels = array();

	$bPointIsInArray = true;
	while ($bPointIsInArray)
	{
		$bPointIsInArray = false;

		foreach ($TextPanels as $aPanel)
		{
			if (intersects($x, $y, $x + $rectangleWidth, $y + $rectangleHeight, $str,
                	   $aPanel['left'], $aPanel['top'], $aPanel['right'], $aPanel['bottom'],
                	   $aPanel['str']))
			{
				$y = $aPanel['bottom'] + 2;
				$bPointIsInArray = true;
				break;
			}
		}
	}

	imagefilledrectangle($im, $x, $y, $x + $rectangleWidth, $y + $rectangleHeight, $backgroundColor);

	if ($LabelColor !== false)
		imagefilledrectangle($im, $x, $y, $x + ($ttfFontSize * 1.5), $y + $rectangleHeight, $LabelColor);

	imagerectangle($im, $x, $y, $x + $rectangleWidth, $y + $rectangleHeight, $textColor);

	$TextPanels[] = array('left' => $x,
												'right' => $x + $rectangleWidth,
												'top' => $y,
												'bottom' => $y + $rectangleHeight,
												'str' => $str);

	if ($LabelColor !== false)
		$x += ($ttfFontSize * 1.5);

	foreach ($lines as $line)
	{
		$boundingBox = imagettftext($im, $ttfFontSize, 0, $x + $padding + 1, $y + $ttfFontSize + 3, $textColor, $ttfFontfile, utf8_encode($line));
		$y += $LineHeight;
	}
}

function intersects($l1, $t1, $r1, $b1, $str1,
                	  $l2, $t2, $r2, $b2, $str2)
{
	if ($l1 >= $l2 && $l1 < $r2)
		$l = $l1;
	else if ($l2 >= $l1 && $l2 < $r1)
		$l = $l2;
	else
		return false;

	if ($t1 >= $t2 && $t1 < $b2)
		$t = $t1;
	else if ($t2 >= $t1 && $t2 < $b1)
		$t = $t2;
	else
		return false;

	if ($r1 > $l2 && $r1 <= $r2)
		$r = $r1;
	else if ($r2 > $l1 && $r2 <= $r1)
		$r = $r2;
	else
		return false;

	if ($b1 > $t2 && $b1 <= $b2)
		$b = $b1;
	else if ($b2 > $t1 && $b2 <= $b1)
		$b = $b2;
	else
		return false;

	//  Final check for empty intersection.  This can happen, e.g., if
	//  one rectangle had zero width, but was enclosed within the other.

	if ($b <= $t || $r <= $l)
		return false;

	return true;
}
?>