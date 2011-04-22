<?

// Parameters :
// v : values
// t : texts
// m : max values

// width  : in pixels
// height : in pixels

// ClockWise : (true/false) default is true
// encoding  : UTF-8 or ISO-8859-1 (default)

$v = array();

foreach ($_GET['v'] as $k => $c)
{
	if (empty($_GET['m'][$k]))
		continue;

	$text = str_replace('"', '\"', stripslashes($_GET['t'][$k]));
	$text = wordwrap($text, 30, "\n");

	if (isset($_GET['a']))
		$v[] = array("value" => $c,
								 "max" => $_GET['m'][$k],
								 "avg" => $_GET['a'][$k],
								 "string" => $text);
	else
		$v[] = array("value" => $c,
								 "max" => $_GET['m'][$k],
								 "string" => $text);
}

if (isset($_GET['bClockWise']))
	$bClockWise = !$_GET['bClockWise'];
else
	$bClockWise = true;

if (isset($_GET['width']))
	$ImageWidth = $_GET['width'];
else
	$ImageWidth = 450;

if (isset($_GET['height']))
	$ImageHeight = $_GET['height'];
else
	$ImageHeight = 250;

if (isset($_GET['encoding']))
	$GLOBALS['encoding'] = $_GET['encoding'];
else
	$GLOBALS['encoding'] = 'ISO-8859-1';

if (HasGD2())
	$im = imagecreatetruecolor($ImageWidth, $ImageHeight);
else
	$im = imagecreate($ImageWidth, $ImageHeight);

$black     = imagecolorallocate ($im,   0,   0,   0);
$lightgray = imagecolorallocate ($im, 211, 211, 211);
$gray      = imagecolorallocate ($im, 100, 100, 100);

if (HasGD2())
	$trans_gray = imagecolorallocatealpha($im, 100, 100, 100, 100);
else
	$trans_gray = imagecolorallocate($im, 211, 211, 211);

$navy      = imagecolorallocate ($im,   0,   0, 128);
$red       = imagecolorallocate ($im, 255,   0,   0);
$white     = imagecolorallocate ($im, 255, 255, 255);

imagefilledrectangle($im, 0, 0, $ImageWidth, $ImageHeight, $white);

for ($i=25; $i <= 100; $i = $i + 25)
	DrawPolygon($im, count($v), $i);

DrawRays($im, count($v), 100);

DrawValues($im, $v);

if (isset($_GET['a']))
	DrawAverages($im, $v);

$bShowValuesLabels = !isset($_GET['ShowValueLabels']) || $_GET['ShowValueLabels'] != 'false';

if ($bShowValuesLabels)
	DrawValuesLabels($im, $v);

DrawLabels($im, $v);

header ("Content-type: image/png");
imagepng($im);

imagecolordeallocate($im, $black);
imagecolordeallocate($im, $lightgray);
imagecolordeallocate($im, $trans_gray);
imagecolordeallocate($im, $gray);
imagecolordeallocate($im, $navy);
imagecolordeallocate($im, $red);
imagecolordeallocate($im, $white);

imagedestroy($im);

function InitMaxRayAndOffsets(&$r, &$xoffset, &$yoffset, $size)
{
	global $ImageWidth, $ImageHeight;

  $r = ((min($ImageWidth, $ImageHeight) / 2) -30) * $size / 100;

  $xoffset = $ImageWidth / 2;
  $yoffset = $ImageHeight / 2 - 15;
}

function DrawPolygon($im, $numberOfEdges, $size)
{
	global $lightgray;

  // size : 0 -> 100
	InitMaxRayAndOffsets($r, $xoffset, $yoffset, $size);

	$points = array();

  for ($edge=0; $edge < $numberOfEdges; $edge ++)
  {
    $alpha = pi() / 2 - $edge * 2 * pi() / $numberOfEdges;

    $points[] = $xoffset - $r * cos($alpha); // x
    $points[] = $yoffset - $r * sin($alpha); // y
  }

	// close the polygon
	$points[] = $points[0]; // x
	$points[] = $points[1]; // y

	imagepolygon($im, $points, $numberOfEdges, $lightgray);
}

function DrawRays($im, $numberOfEdges, $size)
{
	global $lightgray;

  InitMaxRayAndOffsets($r, $xoffset, $yoffset, $size);

  for ($edge=0; $edge < $numberOfEdges; $edge ++)
  {
    $alpha = pi()/2 - $edge * 2 * pi() / $numberOfEdges;

    imageline($im,
    					$xoffset, $yoffset,
    					$xoffset - $r * cos($alpha), $yoffset - $r * sin($alpha),
    					$lightgray);
  }
}

function DrawValues($im, $v)
{
	global $navy, $trans_gray, $black, $white, $bClockWise;

	if (HasGD2())
		imagesetthickness($im, 2);

  InitMaxRayAndOffsets($r, $xoffset, $yoffset, 100);

  $points = array();

  for ($edge=0; $edge < count($v); $edge ++)
  {
 		if ($v[GetRealIndex($edge, $v)]['max'] == 0)
 			$CurrentValuePerCent = 0;
 		else
 			$CurrentValuePerCent = round($v[GetRealIndex($edge, $v)]['value'] * 100 / $v[GetRealIndex($edge, $v)]['max']);

    $alpha = pi()/2 - $edge * 2 * pi() / count($v);

		$points[] = $xoffset - $CurrentValuePerCent * $r * cos($alpha) / 100; // x
		$points[] = $yoffset - $CurrentValuePerCent * $r * sin($alpha) / 100; // y
  }

	// close the polygon
	$points[] = $points[0]; // x
	$points[] = $points[1]; // y

  imagefilledpolygon($im, $points, count($v), $trans_gray);
	imagepolygon($im, $points, count($v), $navy);
}


function DrawValuesLabels($im, $v)
{
	global $black, $white, $bClockWise;

	if (HasGD2())
		imagesetthickness($im, 2);

  InitMaxRayAndOffsets($r, $xoffset, $yoffset, 100);

	if (HasGD2())
		imagesetthickness($im, 1);

  for ($edge=0; $edge < count($v); $edge ++)
  {
		if ($v[GetRealIndex($edge, $v)]['max'] == 0)
			$CurrentValuePerCent = 0;
		else
			$CurrentValuePerCent = round($v[GetRealIndex($edge, $v)]['value'] * 100/ $v[GetRealIndex($edge, $v)]['max']);

    $alpha = pi()/2 - $edge * 2 * pi() / count($v) - 0.03;

		$xorigin = $xoffset - ($CurrentValuePerCent + 2) * $r * cos($alpha) / 100;
		$yorigin = $yoffset - ($CurrentValuePerCent + 2) * $r * sin($alpha) / 100;

		DrawStringAt($im, $v[GetRealIndex($edge, $v)]['value'] . '/' . $v[GetRealIndex($edge, $v)]['max'], $black, $white, $xorigin, $yorigin);
  }
}

function DrawAverages($im, $v)
{
	global $red, $black, $white, $bClockWise;

	if (HasGD2())
		imagesetthickness($im, 2);

  InitMaxRayAndOffsets($r, $xoffset, $yoffset, 100);

  $points = array();

  for ($edge=0; $edge < count($v); $edge ++)
  {
 		if ($v[GetRealIndex($edge, $v)]['max'] == 0)
 			$CurrentValuePerCent = 0;
 		else
 			$CurrentValuePerCent = round($v[GetRealIndex($edge, $v)]['avg'] * 100 / $v[GetRealIndex($edge, $v)]['max']);

    $alpha = pi()/2 - $edge * 2 * pi() / count($v);

		$points[] = $xoffset - $CurrentValuePerCent * $r * cos($alpha) / 100; // x
		$points[] = $yoffset - $CurrentValuePerCent * $r * sin($alpha) / 100; // y
  }

	// close the polygon
	$points[] = $points[0]; // x
	$points[] = $points[1]; // y

	imagepolygon($im, $points, count($v), $red);
}

function GetRealIndex($index, &$v)
{
	global $bClockWise;

	if ($bClockWise)
		$index = count($v) - $index;

	if ($index >= count($v))
		$index -= count($v);

	if ($index < 0)
		$index += count($v);

	return $index;
}

function DrawLabels($im, $v)
{
	global $black, $white;

	if (HasGD2())
		imagesetthickness($im, 1);

  InitMaxRayAndOffsets($r, $xoffset, $yoffset, 100);

  for ($edge=0; $edge < count($v); $edge ++)
  {
    $alpha = pi()/2 - $edge * 2 * pi() / count($v);

    $xorigin = $xoffset - $r * cos($alpha) - (cos($alpha) + 1);
    $yorigin = $yoffset - $r * sin($alpha);

		DrawStringAt($im, $v[GetRealIndex($edge, $v)]['string'], $black, $white, $xorigin, $yorigin, 'center');
  }
}

function DrawStringAt($im, $str, $textColor, $backgroundColor, $x, $y,
											$hAlign = 'left', $bCenterV = false,
											$LabelColor = false)
{
	global $ImageWidth;

	$padding = 3;

	$ttfFontSize = 8;
	$ttfFontfile = str_replace('radar.php', 'verdana.ttf', str_replace('\\', '/', __FILE__));

	$x = round($x);
	$y = round($y);

	$lines = explode("\n", $str);

	$rectangleHeight = 0;
	$rectangleWidth = 0;

	$LineHeight = 0;

	foreach ($lines as $line)
	{
		if ($GLOBALS['encoding'] != 'UTF-8')
			$boundingBox = imagettfbbox($ttfFontSize, 0, $ttfFontfile, utf8_encode($line));
		else
			$boundingBox = imagettfbbox($ttfFontSize, 0, $ttfFontfile, $line);

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

	if ($hAlign == 'center')
		$x -= $rectangleWidth / 2;
	else if ($hAlign == 'right')
		$x -= $rectangleWidth;

	$x = max(1, min($x, $ImageWidth - $rectangleWidth - 1));


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
		if ($GLOBALS['encoding'] != 'UTF-8')
			$boundingBox = imagettftext($im, $ttfFontSize, 0, $x + $padding + 1, $y + $ttfFontSize + 3, $textColor, $ttfFontfile, utf8_encode($line));
		else
			$boundingBox = imagettftext($im, $ttfFontSize, 0, $x + $padding + 1, $y + $ttfFontSize + 3, $textColor, $ttfFontfile, $line);

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

function HasGD2()
{
	if (!isset($GLOBALS['bHasGD2']))
	{
		$GLOBALS['bHasGD2'] = false;
		ob_start();
		phpinfo(8);
		$phpinfo=ob_get_contents();
		ob_end_clean();

		$phpinfo = strip_tags($phpinfo);
		$phpinfo = stristr($phpinfo,"gd version");
		$phpinfo = stristr($phpinfo,"version");

		preg_match('/\d/',$phpinfo,$gd);

		if ($gd[0] == '2')
			$GLOBALS['bHasGD2'] = true;
	}

	return $GLOBALS['bHasGD2'];
}

?>