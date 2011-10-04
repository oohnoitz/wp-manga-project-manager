<?php

define ('DIRECTORY_CACHE', '../cache');
define ('MAX_WIDTH', 1200);
define ('MAX_HEIGHT', 1600);

$src = get_query( 'src', '' );
if ( $src == '' || strlen($src) <= 3 ) {
	_error('no image specified');
}
$src = get_imgsrc($src);

$mime = get_mimetype( $src );

if ( !function_exists('imagecreatetruecolor') ) {
	_error('GD lib error: the function imagecreatetruecolor does not exist');
}

if ( function_exists('imagefilter') && defined('IMG_FILTER_NEGATE') ) {
	$imgFilters = array (
		1	=> array( IMG_FILTER_NEGATE, 0),
		2	=> array( IMG_FILTER_GRAYSCALE, 0),
		3	=> array( IMG_FILTER_BRIGHTNESS, 1),
		4	=> array( IMG_FILTER_CONTRAST, 1),
		5	=> array( IMG_FILTER_COLORIZE, 4),
		6	=> array( IMG_FILTER_EDGEDETECT, 0),
		7	=> array( IMG_FILTER_EMBOSS, 0),
		8	=> array( IMG_FILTER_GAUSSIAN_BLUR, 0),
		9	=> array( IMG_FILTER_SELECTIVE_BLUR, 0),
		10 => array( IMG_FILTER_MEAN_REMOVAL, 0),
		11 => array( IMG_FILTER_SMOOTH, 0),
	);
}

$w = (int) abs ( get_query( 'w', 0 ) );			// width
$h = (int) abs ( get_query( 'h', 0 ) ); 		// height
$z = (int) get_query( 'z', 1 );							// zoom
$q = (int) abs ( get_query( 'q', 80 ) );		// quality
$a = get_query( 'a', 'c' );									// align
$f = get_query( 'f', '' );									// filter
$s = (bool) get_query( 's', 0 );						// sharpen

if ( $w == 0 && $h == 0 ) {
	$w = 60;
	$h = 60;
}

$w = min( $w, MAX_WIDTH );
$h = min( $h, MAX_HEIGHT );

ini_set( 'memory_limit', '50M' );

if ( $src ) {
	$image = get_image( $mime, $src );
	if ( $image === false ) {
		_error('unable to open img');
	}
	
	$ow = imagesx($image);
	$oh = imagesy($image);
	
	if ( $w && !$h ) {
		$h = floor( $oh * ( $w / $ow ) );
	} elseif ( !$w && $h ) {
		$w = floor( $ow * ( $h / $oh ) );
	}
	
	$canvas = imagecreatetruecolor( $w, $h );
	imagealphablending( $canvas, false );
	
	$color = imagecolorallocatealpha( $canvas, 0, 0, 0, 127 );
	imagefill( $canvas, 0, 0, $color );
	imagesavealpha( $canvas, true );
	
	if ( $z ) {
		$sx = $sy = 0;
		$sw = $ow;
		$sh = $oh;
		
		$cx = $ow / $w;
		$cy = $oh / $h;
		
		if ( $cx > $cy ) {
			$sw = round( ( $ow / $cx * $cy ) );
			$sx = round( ( $ow - ( $ow / $cx * $cy ) ) / 2 );
		} elseif ( $cy > $cx ) {
			$sh = round( ( $oh / $cy * $cx ) );
			$sy = round( ( $oh - ( $oh / $cy * $cx ) ) / 2 );
		}
		
		switch ( $a ) {
			case 't':
			case 'tl':
			case 'lr':
			case 'tr':
			case 'rt':
				$sy = 0;
				break;

			case 'b':
			case 'bl':
			case 'lb':
			case 'br':
			case 'rb':
				$sy = $oh - $sh;
				break;

			case 'l':
			case 'tl':
			case 'lt':
			case 'bl':
			case 'lb':
				$src_x = 0;
				break;

			case 'r':
			case 'tr':
			case 'rt':
			case 'br':
			case 'rb':
				$src_x = $ow - $w;
				$src_x = $ow - $sw;
				break;
				
			default:
				break;
		}
		
		imagecopyresampled( $canvas, $image, 0, 0, $sx, $sy, $w, $h, $sw, $sh );
	} else {
		imagecopyresampled( $canvas, $image, 0, 0, 0, 0, $w, $h, $ow, $oh );
	}
	
	if ( $f != '' && function_exists('imagefilter') && defined('IMG_FILTER_NEGATE') ) {
		$fList = explode( '|', $f );
		foreach ( $fList as $filter ) {
			$fSettings = explode( ',' , $filter );
			if ( isset($imgFilters[$fSettings[0]]) ) {
				for ( $i = 0; $i < 4; $i++ ) {
					if ( !isset($fSettings[$i]) )
						$fSettings[$i] = null;
					else
						$fSettings[$i] = (int) $fSettings[$i];
				}
			}
			
			switch ( $imgFilters[$fSettings[0]][1] ) {
				case 1:
					imagefilter( $canvas, $imgFilters[$fSettings[0]][0], $fSettings[1] );
					break;
				
				case 2:
					imagefilter( $canvas, $imgFilters[$fSettings[0]][0], $fSettings[1], $fSettings[2] );
					break;
				
				case 3:
					imagefilter( $canvas, $imgFilters[$fSettings[0]][0], $fSettings[1], $fSettings[2], $fSettings[3] );
					break;
				
				case 4:
					imagefilter( $canvas, $imgFilters[$fSettings[0]][0], $fSettings[1], $fSettings[2], $fSettings[3], $fSettings[4] );
					break;
				
				default:
					imagefilter( $canvas, $imgFilters[$fSettings[0]][0] );
					break;
			}
		}
	}
	
	if ( $s && function_exists('imageconvolution') ) {
		$sMatrix = array (
			array( -1, -1, -1 ),
			array( -1, 16, -1 ),
			array( -1, -1, -1 ),
		);
		
		$div = 0;
		$off = 0;
		
		imageconvolution( $canvas, $sMatrix, $div, $off );
	}
	
	display_image( $mime, $canvas );
	imagedestory( $canvas );
	die();
	
} else {
	if ( strlen($src) )
		_error('src not found');
	else
		_error('no src defined');
}

function check_source ( $src ) {
	
	return $src;
}

function get_query( $variable, $default = 0 ) {
	if ( isset($_GET[$variable]) )
		return $_GET[$variable];
	else
		return $default;
}

function get_mimetype( $file ) {
	$file = getimagesize($file);
	$mime = $file['mime'];
	
	if ( !preg_match("/jpg|jpeg|gif|png/i", $mime) ) {
		_error('invalid mime type: ' . $mime);
	}
	
	return $mime;
}

function get_image( $mime, $src ) {
	$mime = strtolower($mime);
	
	if ( stristr($mime, 'gif')) {
		$image = imagecreatefromgif($src);
	} elseif ( stristr($mime, 'jpeg')) {
		$image = imagecreatefromjpeg($src);
	} elseif ( stristr($mime, 'png')) {
		$image = imagecreatefrompng($src);
	}
	
	return $image;
}

function display_image( $mime, $canvas ) {
	global $q;
	
	$cache = get_imagecache();
	
	if ( stristr( $mime, 'jpeg' ) ) {
		imagejpeg( $canvas, $cache, floor( $q ) );
	} else {
		imagepng( $canvas, $cache, floor( $q * 0.09 ) );
	}
	
	display_imagecache( $mime );
}

function display_imagecache( $mime ) {
	if ( isset ($_SERVER['HTTP_IF_MODIFIED_SINCE']) ) {
		if ( strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) < strtotime('now') ) {
			header('HTTP/1.1 304 Not Modified');
			die();
		}
	}
	
	$cache = get_imagecache ();
	
	if ( file_exists($cache) ) {
		$gmd_exp = gmdate( 'D, d M Y H:i:s', strtotime('now +10 days') ) . ' GMT';
		$gmd_mod = gmdate( 'D, d M Y H:i:s' ) . ' GMT';
		
		header('Content-Type: ' . $mime);
		header('Accept-Ranges: bytes');
		header('Last-Modified: ' . $gmd_mod);
		header('Content-Length: ' . filesize($cache));
		header('Cache-Control: max-age=864000, must-revalidate');
		header('Expires: ' . $gmd_exp);
		
		if ( !@readfile($cache) ) {
			$content = file_get_contents($cache);
			if ($content)
				echo $content;
			else
				_error('cache could not be loaded');
		}
		
		die();
	}
	return FALSE;
}

function get_imagecache () {
	static $cache;
	global $src, $w, $h;
	
	if ( !$cache ) {
		$cache = DIRECTORY_CACHE . '/' . $w . 'x' . $h . '__' . basename($src);
	}
	
	return $cache;
}

function get_imgsrc( $src ) {
	$hostname = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	$regex = "/^((ht|f)tp(s|):\/\/)(www\.|)" . $hostname . "/i";
	
	$src = preg_replace($regex, '', $src);
	$src = strip_tags($src);
	$src = str_replace(' ', '%20', $src);
	if (strpos($src, '/') == 0) $src = substr($src, -(strlen ($src) - 1));
	$src = preg_replace ("/\.\.+\//", "", $src);
	$src = get_docroot($src) . '/' . $src;
	return $src;
}

function get_docroot( $src ) {
	if (file_exists($_SERVER['DOCUMENT_ROOT']) . '/' . $src) return $_SERVER['DOCUMENT_ROOT'];
	
	$locations = array_diff(explode('/', $_SERVER['SCRIPT_FILENAME']), explode('/', $_SERVER['DOCUMENT_ROOT']));
	$path = $_SERVER['DOCUMENT_ROOT'];
	foreach ($locations as $location) {
		$path .= '/' . $location;
		if (file_exists($path . '/' . $src)) return $path;
	}
	
	$paths = array(
		"./",
		"../",
		"../../",
		"../../../",
		"../../../../",
		"../../../../../"
	);
	foreach ($paths as $path) {
		if (file_exists($path . $src)) return $path;
	}
	
	if (!isset($_SERVER['DOCUMENT_ROOT'])) {
		$path = str_replace("/", "\\", $_SERVER['ORIG_PATH_INFO']);
		$path = str_replace($path, '', $_SERVER['SCRIPT_FILENAME']);
		if (file_exists($path . '/' . $src)) return $path;
	}
}

function _error( $string = '' ) {
	header('HTTP/1.1 400 Bad Request');
	echo '<pre>' . htmlentities($string) . '<br />Query: ' . htmlentities($_SERVER['QUERY_STRING']) . '</pre>';
	die();
}
?>