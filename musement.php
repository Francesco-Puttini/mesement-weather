<?php
ob_start();

function _dump($var = NULL) {    
    echo "<pre style='color:red !important'>"; 
    print_r($var);
    echo "</pre>";
}


class Weather {
    
    public static  $MusementXml      = NULL;
    public static  $SingleCityName   = NULL;
    public static  $SingleDate       = NULL;
    
    public static  $WeatherDate      = array(
                                            "today"=> array(
                                                "value"=> 0, 
                                                "label"=>"Today"
                                            ), 
                                            "tomorrow"=>array(
                                                "value"=> 0, 
                                                "label"=>"Tomorrow"
                                            )
                                        );
    
    private static $MusementEndPoint = "https://api.musement.com/api/v3/cities";
    
    private static $WeatherEndPoint  = "https://api.openweathermap.org/data/2.5/onecall?exclude=current,minutely,hourly&";
    private static $WeatherApiKey    = "086f054e795b91d96738942d06c8bc7e";
    private static $WeatherLanguage  = "en";
    private static $WeatherLimits    = 2;
    
    public function __construct($city = NULL, $date = NULL, $limits = NULL) {
        self::$MusementXml    = self::doFileGetsContens(self::$MusementEndPoint);
        
        self::$WeatherLimits  = is_null($limits) ? self::$WeatherLimits  : (int)$limits;
        self::$SingleCityName = is_null($city)   ? self::$SingleCityName : strip_tags($city);
        self::$SingleDate     = array_key_exists($date, self::$WeatherDate) ? self::$WeatherDate[$date] : NULL;
        
        is_null(self::$SingleCityName) ? self::doWeatherParser() : self::doWeatherCityParser();
    }
    
    private static function doFileGetsContens($file = NULL) {
        return !is_null($file) ? json_decode(file_get_contents($file)) : NULL;
    }
    
    private static function doWeatherCityParser() {
        foreach(self::$MusementXml as $kc => $vc):
            if($vc->name == self::$SingleCityName):
                $WeatherApiParams = "lat=".$vc->latitude."&lon=".$vc->longitude;
                $WeatherApiParams.= "&lang=".self::$WeatherLanguage."&appid=".self::$WeatherApiKey;    
                $WeatherResponse  = self::doFileGetsContens(self::$WeatherEndPoint . $WeatherApiParams);
                self::showWeatherInfo($vc->name, $WeatherResponse->daily);
            endif;
        endforeach;
    }
    
    private static function doWeatherParser() {
        foreach(self::$MusementXml as $kc => $vc):
            if($kc < self::$WeatherLimits):
                $WeatherApiParams = "lat=".$vc->latitude."&lon=".$vc->longitude;
                $WeatherApiParams.= "&lang=".self::$WeatherLanguage."&appid=".self::$WeatherApiKey;    
                $WeatherResponse  = self::doFileGetsContens(self::$WeatherEndPoint . $WeatherApiParams);
                self::showWeatherInfo($vc->name, $WeatherResponse->daily);
            endif;
        endforeach;
    }
    
    private static function showWeatherInfo($CityName = NULL, $Weather = NULL) {
        if(isset(self::$SingleDate["value"])):
            _dump($CityName . " : ".self::$SingleDate["label"]." = ".$Weather[self::$SingleDate["value"]]->weather[0]->main);
        else:
            _dump($CityName . " : Today = ".$Weather[0]->weather[0]->main." | Tommorow = ".$Weather[1]->weather[0]->main);
        endif;
    }
    
}

$Weather = new Weather(NULL, "tomorrow", 2);
//$Weather = new Weather(NULL, NULL, 2);

ob_flush();
ob_clean();
ob_end_flush();