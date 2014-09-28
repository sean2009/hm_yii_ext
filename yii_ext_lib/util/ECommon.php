<?php 
class ECommon{
    
    /**
     * 货币格式化
     * 
     * @param number $money
     * @return string
     */
    public static function currency_format($money) {
        return number_format($money, 2, '.', '');
    }
    
	/**
     * 人民币转换成大写
     * 
     * @param number $money
     * @return string
     */
    public static function chinese_rmb($money) {
        $money = round($money, 2);    // 四舍五入  
        if ($money <= 0) {
            return '零元';
        }
        $units = array('', '拾', '佰', '仟', '', '万', '亿', '兆');
        $amount = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
        $arr = explode('.', $money);    // 拆分小数点  
        $money = strrev($arr[0]);        // 翻转整数  
        $length = strlen($money);        // 获取数字的长度  
        for ($i = 0; $i < $length; $i++) {
            $int[$i] = $amount[$money[$i]];    // 获取大写数字  
            if (!empty($money[$i])) {
                $int[$i] .= $units[$i % 4];    // 获取整数位  
            }
            if ($i % 4 == 0) {
                $int[$i] .= $units[4 + floor($i / 4)];    // 取整  
            }
        }
        $con = isset($arr[1]) ? '元' . $amount[$arr[1][0]] . '角' . $amount[$arr[1][1]] . '分' : '元整';
        return preg_replace('/(零{1,})$/u', '', implode('', array_reverse($int))) . $con;    // 整合数组为字符串  
    }
    
    /**
     * 判断是否是手机号码
     * 
     * @param string $str
     * @return boolean
     */
    public static function isMobilePhone($str) {
        return preg_match('/^0?1((3[0-9]{1})|(5[0-9]{1})|(8[0-9]{1})){1}[0-9]{8}$/', $str);
    }
    
    /**
     * 以UTF8编码计算字符串长度
     * 
     * @param string $str
     * @return int
     */
    public static function utf8Strlen($str) {
        return function_exists('mb_strlen') ? mb_strlen($str, 'utf-8') : (function_exists('iconv_strlen') ? iconv_strlen($str, 'utf-8') : strlen($str));
    }
    
    /**
     * 字符串截取
     * @param string $str
     * @param int $start
     * @param int $length
     * @param boolean $ellipsis 是否显示省略号
     * 
     * @return string
     */
    public static function substr($str, $start, $length = null, $ellipsis = false) {
        $strlen = self::utf8Strlen($str);
        if ($strlen > $length) {
            if (function_exists('mb_substr')) {
                $str = mb_substr($str, $start, $length, 'utf-8');
            } elseif (function_exists('iconv_substr')) {
                $str = iconv_substr($str, $start, $length, 'utf-8');
            } else {
                $str = substr($str, $start, $length);
            }
            return $ellipsis ? "$str..." : $str;
        } else {
            return $str;
        }
    }
    
    /**
     * 发送短信
     * 
     * @param mix $mobiles 手机号码，当给多个号码发短信时，使用数组
     * @param string $content 要发送的内容
     * @param string $type 短信的类型，要发送的短信类型必须在集成后台注册过
     * @param int $max 单个号码每天最多发送的次数
     * @return boolean
     */
    public static function sendSms($mobiles,$content,$type='团购后台',$max= 10) {
        Yii::import('yii_ext_lib.common_service.MessageService');
        return MessageService::sendSms($mobiles, $content, $type, $max);
    }
    
    /**
     * 发送邮件
     * 
     * @param string $mail_title 邮件标题
     * @param string $mail_content 邮件内容
     * @param string $mail_to 收件人
     */
    public static function sendMail($mail_title, $mail_content, $mail_to) {
        Yii::import('yii_ext_lib.common_service.MessageService');
        return MessageService::sendEmail($mail_title, $mail_content, $mail_to);
    }
    
    /**
     * 记录日志到服务器上
     * 
     * @param mix $data 日志的内容，如果是数组类型，则数组的键就是mongodb的列名，值就是相应列对应的值
     * @param string $logName 日志名字，对应到mongodb的文档名，必须是拉丁字母和数字下划线
     */
    public static function log($data, $logName) {
        Yii::import('yii_ext_lib.common_service.LogService');
        return LogService::log($data, $logName);
    }
    
    /**
     * 补全图片文件的URL地址的域名部分
     * 
     * @param string $str
     * @return string
     */
    public static function imgUrl($str) {
		if (strlen($str) > 0) {
			if(stristr($str,'image')){
				$str = str_replace(IMG_PATH, $GLOBALS['img_servers'][array_rand($GLOBALS['img_servers'])], $str);
			}else if(stristr($str,'group1')){
				$dfs_domain =  $GLOBALS['dfs_servers']['group1'][array_rand($GLOBALS['dfs_servers']['group1'],1)];
                                $str = $dfs_domain.$str;
			}else if(stristr($str,'group2')){
				$dfs_domain =  $GLOBALS['dfs_servers']['group2'][array_rand($GLOBALS['dfs_servers']['group2'],1)];
                                $str = $dfs_domain.$str;
			}
		}
		return $str;	
	}
    
            /**
     * 补全图片文件的URL地址的域名部分
     * 
     * @param string $str
     * @return string
     */
    public static function imgUrlExt($str,$ext="") {
                $str = self::imgUrl($str);
                $dirname = dirname($str).'/';
                $filename = basename($str);
                $files = explode(".",$filename);
                $str = $dirname.$files[0]."_".$ext.".".$files[1];
		return $str;	
    }
        
    /**
	 * 解密已经加密了的cookie
	 * 
	 * @param string $encryptedText
	 * @return string
	 */
	public static function decrypt($encryptedText)
	{
		return self::authcode($encryptedText,'DECODE');
	}

	/**
	 * 加密cookie
	 *
	 * @param string $plainText
	 * @return string
	 */
	public static function encrypt($plainText)
	{
		return self::authcode($plainText,'ENCODE');
	}
	
	/**
	 * 字符串加密以及解密函数
	 *
	 * @param string $string	原文或者密文
	 * @param string $operation	操作(ENCODE | DECODE), 默认为 DECODE
	 * @param string $key		密钥
	 * @param int $expiry		密文有效期, 加密时候有效， 单位 秒，0 为永久有效
	 * @return string		处理后的 原文或者 经过 base64_encode 处理后的密文
	 *
	 * @example
	 *
	 * 	$a = authcode('abc', 'ENCODE', 'key');
	 * 	$b = authcode($a, 'DECODE', 'key');  // $b(abc)
	 *
	 * 	$a = authcode('abc', 'ENCODE', 'key', 3600);
	 * 	$b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
	 */
	private static function authcode($string, $operation = 'DECODE',$key=COOKIE_KEY, $expiry = 0) 
	{
		$ckey_length = 4;	//note 随机密钥长度 取值 0-32;
					//note 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
					//note 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
					//note 当此值为 0 时，则不产生随机密钥
	
		$key = md5($key ? $key : 'cookie_keycookie_keycookie_keycookie_keycookie_key');
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	
		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);
	
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);
	
		$result = '';
		$box = range(0, 255);
	
		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}
	
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
	
		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
	
		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}
    
}