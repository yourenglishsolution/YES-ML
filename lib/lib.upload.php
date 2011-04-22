<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

/**
 * @package 	admin-library
 * @category 	File managment
 * @author 		Emanuele Sandri <esandri@tiscali.it>
 * @version 	$Id: lib.upload.php 193 2006-03-31 07:31:01Z fabio $
 */

$ftpConn = NULL;	// Chache for the last connection

function sl_open_fileoperations() {
	$uploadType = Get::cfg('uploadType');
	if( $uploadType == "ftp" ) {
		return sl_open_fileoperations_ftp();
	} elseif( $uploadType == "cgi" ) {
		return TRUE;
	} else {
		return TRUE;
	}
}

function sl_close_fileoperations() {
	$uploadType = Get::cfg('uploadType');
	if( $uploadType == "ftp" ) {
		return sl_close_fileoperations_ftp();
	} elseif( $uploadType == "cgi" ) {
		return TRUE;
	} else {
		return TRUE;
	}
}

function sl_mkdir( $path, $mode ) {
	$uploadType = Get::cfg('uploadType');

	if( $uploadType == "ftp" ) {
		return sl_mkdir_ftp( $path, $mode );
	} elseif( $uploadType == "cgi" ) {
		return FALSE;
	} else {
		$result = mkdir( $GLOBALS['where_files_relative'].$path, $mode );
		return $result;
	}
}

function sl_fopen( $filename, $mode ) {
	$uploadType = Get::cfg('uploadType');

	$mfirst = $mode{0};
	if( $uploadType == "ftp" && $mfirst != 'r' ) {
		return sl_fopen_ftp( $filename, $mode );
	} elseif( $uploadType == "cgi" ) {
		return FALSE;
	} else {
		return fopen( $GLOBALS['where_files_relative'].$filename, $mode);
	}
}

function sl_upload( $srcFile, $dstFile ) {
	$uploadType = Get::cfg('uploadType');
	$dstFile =stripslashes($dstFile);
	if( $uploadType == "ftp" ) {
		return sl_upload_ftp( $srcFile, $dstFile );
	} elseif( $uploadType == "cgi" ) {
		return sl_upload_cgi( $srcFile, $dstFile );
	} else {
		return sl_upload_fs( $srcFile, $dstFile );
	}
}

function sl_touch( $filename, $time ) {
	$uploadType = Get::cfg('uploadType');
	if( $uploadType == "ftp" ) {
		return TRUE;
	} elseif( $uploadType == "cgi" ) {
		return TRUE;
	} else {
		return touch( $GLOBALS['where_files_relative'].$filename, $time );
	}
}

function sl_is_file( $filename ) {
	return @is_file($GLOBALS['where_files_relative'].$filename);
}

function sl_is_dir( $path ) {
	return @is_dir($GLOBALS['where_files_relative'].$path);
}

function sl_is_readable( $filename ) {
	return is_readable( $GLOBALS['where_files_relative'].$filename );
}

function sl_is_writeable( $filename ) {
	return is_writeable( $GLOBALS['where_files_relative'].$filename );
}

function sl_filesize( $filename ) {
	return filesize( $GLOBALS['where_files_relative'].$filename );
}

function sl_filemtime($filename ) {
	return filemtime( $GLOBALS['where_files_relative'].$filename );
}

function sl_file_exists( $filename ) {
	return file_exists( $GLOBALS['where_files_relative'].$filename );
}

function sl_chmod( $filename, $mode ) {
	return chmod( $GLOBALS['where_files_relative'].$filename, $mode );
}

function sl_copy( $srcFile, $dstFile ) {
	$uploadType = Get::cfg('uploadType');
	if( $uploadType == "ftp" ) {
		return sl_upload_ftp( $GLOBALS['where_files_relative'].$srcFile, $dstFile );
	} elseif( $uploadType == "cgi" ) {
		return sl_upload_cgi( $srcFile, $dstFile );
	} else {
		return copy($GLOBALS['where_files_relative'].$srcFile, $GLOBALS['where_files_relative'].$dstFile);
	}
}

/** file system implementation **/

function sl_upload_fs( $srcFile, $dstFile ) {

	$re = move_uploaded_file($srcFile, $GLOBALS['where_files_relative'].$dstFile);
	if(!$re) die("Error on move_uploaded_file from: $srcFile to $dstFile");
	return $re;
}

/**
 * Copy a file, or recursively copy a folder and its contents
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function sl_copyr($source, $dest)
{
    // Simple copy for a file
    if (is_file($GLOBALS['where_files_relative'].$source)) {
        return sl_copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($GLOBALS['where_files_relative'].$dest)) {
        sl_mkdir($dest);
    }

    // Loop through the folder
    $dir = dir($GLOBALS['where_files_relative'].$source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        if ($dest !== "$source/$entry") {
            sl_copyr("$source/$entry", "$dest/$entry");
        }
    }

    // Clean up
    $dir->close();
    return true;
}


/** Ftp implementation **/

function sl_open_fileoperations_ftp() {
	$ftpuser = Get::cfg('ftpuser');
	$ftppass = Get::cfg('ftppass');
	$ftphost = Get::cfg('ftphost');
	$ftpport = Get::cfg('ftpport');

	$result = FALSE;

	$timeout = Get::cfg('ftptimeout', 0);
	if( $timeout == 0 ) {
		$timeout = ini_get('max_execution_time');
		if( $timeout == 0 ) {
			$timeout = 20;
		} elseif( $timeout > 60 ) {
			$timeout = 50;
		} else {
			$timeout = round(($timeout*8)/10);
		}
	}

	$GLOBALS['ftpConn'] = @ftp_connect( $ftphost, $ftpport, $timeout );
	if( $GLOBALS['ftpConn'] === FALSE ) {
		return FALSE;
	}
	if( @ftp_login($GLOBALS['ftpConn'], $ftpuser, $ftppass) )
		return TRUE;
	else
		return FALSE;
}

function sl_close_fileoperations_ftp() {
	if($GLOBALS['ftpConn'] !== false) ftp_close($GLOBALS['ftpConn']);
}

function sl_upload_ftp( $srcFile, $dstFile ) {
	$ftppath = Get::cfg('ftppath')._folder_files_;
	$ftpConn = $GLOBALS['ftpConn'];
	if( !ftp_put( $ftpConn, $ftppath.$dstFile, $srcFile, FTP_BINARY) ) {
		return FALSE;
	} /*
	if( ftp_site( $ftpConn, "CHMOD 0666 $ftppath"."$dstFile" ) === FALSE ) {
		return FALSE;
	}	else {
		return TRUE;
	}	*/
	return TRUE;
}

function sl_mkdir_ftp( $path, $mode = FALSE) {
	$ftppath = Get::cfg('ftppath')._folder_files_;
	$ftpConn = $GLOBALS['ftpConn'];
	if( !@ftp_mkdir($ftpConn, $ftppath.$path) )
		return FALSE;
	if( $mode !== FALSE ) {
		if( ftp_site( $ftpConn, "CHMOD 0777 $ftppath"."$path"  ) === FALSE ) {
			return FALSE;
		}	else {
			return TRUE;
		}
	}	else {
		return TRUE;
	}
	return TRUE;
}

function sl_fopen_ftp( $file, $mode ) {
 	// only create file then open it with fopen
	$ftppath = Get::cfg('ftppath')._folder_files_;
	$ftpConn = $GLOBALS['ftpConn'];
	if( !file_exists( $GLOBALS['where_files_relative'].$file ) ) {
   		if( !ftp_put( $ftpConn, $ftppath.$file, dirname(__FILE__)."/nullfile", FTP_BINARY ) ) {
  			return FALSE;
  		} else {
      		if( ftp_site( $ftpConn, "CHMOD 0666 $ftppath"."$file" ) === FALSE )
      			return FALSE;
	 	}
  	}
	$ret = @fopen( $GLOBALS['where_files_relative'].$file, $mode );
	return $ret;
}

/** CGI Implementation **/

function sl_upload_cgi( $srcFile, $dstFile ) {
	global $url;
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url."testcgi.sh?fname=".$dstFile);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	curl_close($ch);

	$hfileSrc = fopen( $srcFile, "rb" );
	$hfileDst = fopen( $dstFile, "wb" );

	while (!feof($hfileSrc)) {
	$buffer = fread($hfileSrc, 8192);
	fwrite( $hfileDst, $buffer );
	}

	fclose( $hfileSrc );
	fclose( $hfileDst );
	return TRUE;
}

/**
 * @param $path pathname for file retriving
 * @return bool
 **/

 function sl_unlink( $path ) {

	 if( !file_exists($GLOBALS['where_files_relative'].$path) ) return true;
	 return @unlink($GLOBALS['where_files_relative'].$path);
 }

?>
