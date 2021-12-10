<?php

namespace sprint\app\helpers;

use \sprint\app\core\Request;

class Helpers 
{
    public static $sprintSlogan;
    public static $defaultCountry;
    public static $googleApiKey;
    public static $folderToUpload;
    public static $folderPermission;
    
    public function __construct()
    {
        self::$defaultCountry           = $_SERVER["DEFAULT_COUNTRY"];
        self::$googleApiKey             = "AIzaSyDNzbw5cYdq47c_ZaC7I1mwE9CujmQ1dlw";
        self::$folderToUpload           = $_SERVER["UPLOADS_FOLDER"];
        self::$folderPermission         = $_SERVER["PERMISSION_UPLOAD_FOLDER"];        
    }

    public static function isHttps() 
    {
        return ($_SERVER["IS_HTTPS"]  == "false") ? "http" : "https";
    }

    public static function root() 
    {
        return self::isHttps() . "://" . $_SERVER['HTTP_HOST'] . $_SERVER["ROOT_DIR"];
    }
    
    public static function asset() 
    {
        return self::root() . $_SERVER["ASSETS_FOLDER"];
    }
    
    public static function upload() 
    {
        return self::root() . $_SERVER["UPLOADS_FOLDER"];
    }

    public static function viewPath($view = null) 
    {
        return ($view === null) ? "/" : rtrim($view, "/") . "/";
    }

    public static function slug($string) 
    {
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        $b = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';
        $string = utf8_decode($string);
        $string = strtr($string, utf8_decode($a), $b);
        $string = strip_tags(trim($string));
        $string = preg_replace("/\s+/", "-", $string);
        return strtolower(utf8_encode($string));
    }

    //Funcao que limita numero de palavras
    public static function excerpt($string, $words = '200', $ending = '(...)') 
    {
        $string = trim(strip_tags($string));
        $count = strlen($string);
        if ($count <= $words) 
        {
            return $string;
        } else 
        {
            $strpos = strrpos(substr($string, 0, $words), ' ');
            return trim(substr($string, 0, $strpos)) . $ending;
        }
    }

    public static function getTimeAgo($time, $prefixText = "cerca de") 
    {
        $time_difference = time() - strtotime($time);

        if ($time_difference < 1) 
        {
            return 'Menos de 1 segundo atras'; 
        }
        
        $condition = array(12 * 30 * 24 * 60 * 60 => 'ano',
            30 * 24 * 60 * 60 => 'mes',
            24 * 60 * 60 => 'dia',
            60 * 60 => 'hora',
            60 => 'minuto',
            1 => 'segundo'
        );

        foreach ($condition as $secs => $str) 
        {
            $d = $time_difference / $secs;

            if ($d >= 1) 
            {
                $t = round($d);
                return trim($prefixText). ' ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' atras';
            }
        }
    }
    
    public static function getWeekDates(\DateTimeInterface $date, $format = 'Y-m-d') 
    {
        $dt        = \DateTimeImmutable::createFromMutable($date);
        
        $first_day = $dt->modify('first day of this month');
        $last_day  = $dt->modify('last day of this month');
        
        $period    = new \DatePeriod(
            $first_day,
            \DateInterval::createFromDateString('next sunday'),
            $last_day,
            \DatePeriod::EXCLUDE_START_DATE
        );
        
        $weeks = [$first_day->format($format)];
        
        foreach ($period as $d) 
        {
            $weeks[] = $d->modify('-1 day')->format($format);
            $weeks[] = $d->format($format);
        }        
        
        $weeks[] = $last_day->format($format);
        
        return array_chunk($weeks, 2);
        
        
    }
    
    public static function fileSize($size)
    {
        $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
    }

    public static function saveThumbnail($saveToDir, $imagePath, $imageName, $max_x, $max_y) {
        preg_match("'^(.*)\.(gif|jpeg|jpg|png)$'i", $imageName, $ext);

        var_dump($ext);
        
        switch (strtolower($ext[2])) {
            case 'jpg' :
                         $im   = imagecreatefromjpeg ($imagePath);
                         break;
            case 'jpeg': $im   = imagecreatefromjpeg ($imagePath);
                         break;
            case 'gif' : $im   = imagecreatefromgif  ($imagePath);
                         break;
            case 'png' : $im   = imagecreatefrompng  ($imagePath);
                         break;
            default    : $stop = true;
                         break;
        }
       
        if (!isset($stop)) {
            $x = imagesx($im);
            $y = imagesy($im);
       
            if (($max_x/$max_y) < ($x/$y)) {
                $save = imagecreatetruecolor($x/($x/$max_x), $y/($x/$max_x));
            }
            else {
                $save = imagecreatetruecolor($x/($y/$max_y), $y/($y/$max_y));
            }
            imagecopyresized($save, $im, 0, 0, 0, 0, imagesx($save), imagesy($save), $x, $y);
           
            imagejpeg($save, "{$saveToDir}{$imageName}");
            imagedestroy($im);
            imagedestroy($save);
            
            return true;
        }
        
        return false;
    }
	
	public static function buildQuery($blueprint, $query)
	{
		/**
		*@var $currentPage | int define a pagina currente dos imoveis a visualizar
		*é uma variavel dinamica
		*/	
		
		$operators = array(
			"equal" 			=> "= '{0}'",
			"not_equal" 		=> "!= '{0}'",
			"in" 				=> "IN({0})",
			"not_in" 			=> "NOT IN({0})",
			"less" 				=> "< '{0}'",
			"less_or_equal" 	=> "<= '{0}'",
			"greater" 			=> "> '{0}'",
			"greater_or_equal" 	=> ">= '{0}'",
			"between" 			=> "BETWEEN '{0}' AND {1}",
			"not_between" 		=> "NOT BETWEEN '{0}' AND {1}",
			"begins_with" 		=> "LIKE '{0}%'",
			"not_begins_with" 	=> "NOT LIKE '{0}%'",
			"contains" 			=> "LIKE '%{0}%'",
			"not_contains" 		=> "NOT LIKE '%{0}%'",
			"ends_with" 		=> "LIKE '%{0}'",
			"not_ends_with" 	=> "NOT LIKE '%{0}'",
			"is_empty" 			=> "= ''",
			"is_not_empty" 		=> "!= ''",
			"is_null" 			=> "IS NULL",
			"is_not_null" 		=> "IS NOT NULL"
		);
		
		$cond 		= array();
		$url		= array();
		$columns	= array();
		$values		= array();
		$sql		= array();
		
		$url 		= str_replace(":", "/", $query);
		
		$query      = str_replace("=", ":", $query);
		$parts 		= explode(":", $query);
		$column     = "";
		
		if(count($parts) > 1):
		
			foreach($parts as $key => $part)
			{
				if($key % 2 != 0)
				{					
					$values[] 	= $part;					
				}else
				{
					if(array_key_exists($part, $blueprint))
					{
						$column 	= (isset($blueprint[$part][0])) ? $blueprint[$part][0] : [];
						
						$operator 	= (isset($blueprint[$part][1])) ? $operators[$blueprint[$part][1]] : [];
						
						$parsedValues 	= (isset($blueprint[$part]["parse"])) ? $blueprint[$part]["parse"] : [];
						
						$column = implode(" ", [$column, $operator]);	
						
						$columns[] = array(
							"sql" 	=> $column,
							"parse" => $parsedValues
						);
					}
				}
				
			}
			
			foreach($columns as $key => $value)
			{
				$replace 	= !empty($value["parse"]) ? $value["parse"][$values[$key]] : $values[$key];
				
				$sql[] 		= str_replace("{0}", $replace, $value["sql"]);
			}
		
			$sql = implode(" AND ", $sql);

			return array(
				"search" => $url,
				"sql"	 => $sql
			);
		endif;
		
		return array(
			"search" => $url,
			"sql"	 => ""
		);
	}

}
