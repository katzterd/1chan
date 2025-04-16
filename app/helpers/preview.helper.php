<?php
/**
 * Фильтр загрузок (разметка):
 */
class PreviewHelper
{
    /**
     * Загрузка:
     */
    public static function upload($url) {
        $kvs = KVS::getInstance();
		if ($kvs -> exists(__CLASS__, $url))
		    return $kvs -> get(__CLASS__, $url);
		
        $data = @get_headers($url, 1);
		// Check if file size is larger than 10MB
	    if (isset($data['Content-Length']) && $data['Content-Length'] > 3*10485760) {
			return false; // File is too large
		}
        if (in_array($data['Content-Type'], array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'))) {
			$tm = tempnam('/tmp', 'img');
			$nam = md5(uniqid('', true));
			$dir = substr($nam, 0, 1);

			$im = fopen($url, 'r');
			$fp = fopen($tm, 'w');
			$start = time();
			$maxSize = 3*10485760; // 30MB in bytes
	        $downloadedSize = 0;

			while(!feof($im) && (time() - $start) < 5) {
				$buffer = fgets($im, 4096);
				$downloadedSize += strlen($buffer);
				if ($downloadedSize > $maxSize) {
					fclose($im);
					fclose($fp);
					@unlink($tm);
					return false; // File is too large
				}
	            fputs($fp, $buffer);
			}
	        fclose($im);
			fclose($fp);

			if ((time() - $start) > 5)
				return false;
            
            self::createThumbnail($tm, UPLOAD_PATH .'/news/'. /*$dir .'/'.*/ $nam .'.png');
            $p = '/uploads/news/'. /*$dir .'/'.*/ $nam .'.png';
            @unlink($tm);
            $kvs -> set(__CLASS__, $url, null, '/uploads/news/'. /*$dir .'/'.*/ $nam .'.png');
            $kvs -> expire(__CLASS__, $url, null, 60 * 60 * 24 * 30);
            
            return $p;
        }
        return false;
    }
    
    /**
	 * Создание тамбнейла:
	 */
	private static function createThumbnail($path, $name)
	{
		$thumb  = new Imagick($path);
		$width  = $thumb -> getImageWidth();
		$height = $thumb -> getImageHeight();
		$s      = self::scaleImage($width, $height, 150, 200);
		$thumb -> scaleImage($s[0], $s[1]);
		$thumb -> writeImage($name);
		$thumb->destroy();
		return true;
	}
    
    /**
	**/
	private static function scaleImage($x,$y,$cx,$cy) {
		//Set the default NEW values to be the old, in case it doesn't even need scaling
		list($nx,$ny)=array($x,$y);
		
		//If image is generally smaller, don't even bother
		if ($x>=$cx || $y>=$cx) {
		        
		    //Work out ratios
		    if ($x>0) $rx=$cx/$x;
		    if ($y>0) $ry=$cy/$y;
		    
		    //Use the lowest ratio, to ensure we don't go over the wanted image size
		    if ($rx>$ry) {
		        $r=$ry;
		    } else {
		        $r=$rx;
		    }
		    
		    //Calculate the new size based on the chosen ratio
		    $nx=intval($x*$r);
		    $ny=intval($y*$r);
		}    
		
		//Return the results
		return array($nx,$ny);
	}
}
