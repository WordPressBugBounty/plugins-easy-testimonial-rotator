<?php

class Etr_I13_Captcha_Img {


	public function etr_generateI13Captcha() {

		$this->etr_removeOldFiles();
		$md5_hash = md5( uniqid() );
		$security_code = strtoupper( substr( $md5_hash, 16, 5 ) );
		$width = 160;
		$height = 50;

		// Create the image resource
		$image = ImageCreate( $width, $height );

		$white = ImageColorAllocate( $image, 255, 255, 255 );
		$black = ImageColorAllocate( $image, 0, 0, 0 );
		$grey = ImageColorAllocate( $image, 204, 204, 204 );
		$ns = imagecolorallocate( $image, 200, 200, 200 );// noise color

		// Make the background white
		ImageFill( $image, 0, 0, $white );

		$fontArr = array(
			0 => 'Amatic-Bold.ttf',
			1 => 'GenBasB.ttf',
			2 => 'MRF- Blooming Petunia.ttf',
			3 => 'times_new_yorker.ttf',
		);
		$rand = array_rand( $fontArr, 1 );

		$font_file = realpath( __DIR__ . DIRECTORY_SEPARATOR . '..' ) . '/fonts/' . $fontArr[ $rand ];

		if ( 1 == $rand ) {
			imagettftext( $image, 35, 3, 5, 45, $black, $font_file, $security_code );
		} else {
			imagettftext( $image, 35, 5, 35, 45, $black, $font_file, $security_code );
		}

		$noise_level = 25;

		for ( $i = 0; $i < $noise_level; $i++ ) {
			for ( $j = 0; $j < $noise_level; $j++ ) {
				imagesetpixel( $image, rand( 0, $width ), rand( 0, $height ), $ns );
			}
		}

		ImageRectangle( $image, 0, 0, $width - 1, $height - 1, $grey );
		imageline( $image, 0, $height / 2, $width, $height / 2, $grey );
		imageline( $image, $width / 2, 0, $width / 2, $height, $grey );

		$filename = uniqid( 'i13_cap_' );

		$uploads = wp_upload_dir();
		$baseDir = $uploads ['basedir'];
		$baseDir = str_replace( '\\', '/', $baseDir );
		$pathToImagesFolder = $baseDir . '/easy-testimonial-rotator/' . $filename . '.jpeg';
		$pathToImagestxt = $baseDir . '/easy-testimonial-rotator/' . $filename . '.txt';
		$pathToImagesFolder = str_replace( '\\', '/', $pathToImagesFolder );
		$pathToImagestxt = str_replace( '\\', '/', $pathToImagestxt );
		imagejpeg( $image, $pathToImagesFolder );
		// Free up resources
		ImageDestroy( $image );

		$saltc = wp_generate_password( 64 );
		$hashm = hash_hmac( 'md5', $security_code, $saltc );

		$code_gen = $saltc . '|' . $hashm;
		@file_put_contents( $pathToImagestxt, $code_gen );

		return $filename;

	}

	public function etr_verifyCaptcha( $uid, $input ) {

		if ( '' == $uid || null == $uid ) {

			return false;
		}

			$input = preg_replace( '/\s+/', '', $input );
			$input = strtoupper( $input );

			$uploads = wp_upload_dir();
			$baseDir = $uploads ['basedir'];
			$baseDir = str_replace( '\\', '/', $baseDir );
			$pathToImagestxt = $baseDir . '/easy-testimonial-rotator/' . $uid . '.txt';
			$pathToImagestxt = str_replace( '\\', '/', $pathToImagestxt );
			@$secCodeHamc = file_get_contents( $pathToImagestxt );
		if ( '' != $secCodeHamc && null != $secCodeHamc ) {

			$split = explode( '|', $secCodeHamc, 2 );
			$saltc = @$split[0];
			$hashc = @$split[1];
			if ( hash_hmac( 'md5', $input, $saltc ) == $hashc ) {
				 return true;
			}
		}

		   return false;
	}

	public function etr_removeByName( $filename ) {

		$uploads = wp_upload_dir();
		$baseDir = $uploads ['basedir'];
		$baseDir = str_replace( '\\', '/', $baseDir );
		$pathToImgFolder = $baseDir . '/easy-testimonial-rotator/';
		$pathToImgFolder = str_replace( '\\', '/', $pathToImgFolder );
		@unlink( $pathToImgFolder . '/' . $filename . '.jpeg' );
		@unlink( $pathToImgFolder . '/' . $filename . '.txt' );

	}
	public function etr_removeOldFiles() {

		$uploads = wp_upload_dir();
		$baseDir = $uploads ['basedir'];
		$baseDir = str_replace( '\\', '/', $baseDir );
		$pathToImgFolder = $baseDir . '/easy-testimonial-rotator/';
		$pathToImgFolder = str_replace( '\\', '/', $pathToImgFolder );

		$time = current_time( 'timestamp' ) - 3600;

				$handle = @opendir( $pathToImgFolder );
		if ( $handle ) {
			while ( false !== ( $filename = readdir( $handle ) ) ) {

				if ( '.' != $filename && '..' != $filename ) {

					$pos = strpos( $filename, 'i13_cap_' );
					if ( false !== $pos ) {

						 $filelastmodified = filemtime( $pathToImgFolder . '/' . $filename );

						 $filelastmodified = $filelastmodified + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

						if ( $filelastmodified < $time ) {

							@unlink( $pathToImgFolder . '/' . $filename );
						}
					}
				}
			}
		}

	}

}
