<?php
/**
 * Enter description here ...
 * @author xiaopeng
 *
 */
class ERand{
    
    /**
     * 随机css
     * @return string
     */
     public static function randomCSSDomain() {
        $t = array(CSS_DOMAIN1, CSS_DOMAIN2, CSS_DOMAIN3, CSS_DOMAIN4, CSS_DOMAIN5);
        return self::randArrOne($t);
    }
    
    public static function randomJSDomain() {
        $t = array(JS_DOMAIN1, JS_DOMAIN2, JS_DOMAIN3, JS_DOMAIN4, JS_DOMAIN5);
        return self::randArrOne($t);
    }
    
	/**
	 * Enter description here ...
	 * @param unknown_type $array
	 * @return unknown
	 */
	public static function randArrOne($array = array()){
		$key = array_rand($array);
		return $array[$key];
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $length
	 * @return string
	 */
	public static function randString($length = 6){
		$str = 'abcdefghijklmnopqrstuvwxyz0123456ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$return = '';
		for($i=0;$i< $length;$i++){
			$rand = mt_rand(0,strlen($str));
			$return.= substr($str,$rand,1);
		}
		return $return;
	}
}