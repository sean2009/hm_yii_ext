<?php
/**
 * 用户输入过滤
 * @author wander
 */
class base_filter {
	
	/**
	 * 判断POST提交中是否包含"<"和">"
	 * @param array $array $_REQUEST提交
	 * @param int $is_ajax	是否ajax请求，默认否
	 * @return 返回true表示不包含，false包含
	 */
	public static function filter_request($array,$is_ajax = 0)
	{
		foreach ($array as $key => $value)
		{
			if(strpos($key, '<') !== false || strpos($key, '>') !== false){
				self::filter_request_vote(false,$is_ajax);
			}
			if(!is_array($value))
			{
				if(strpos($value, '<') !== false || strpos($value, '>') !== false){
					self::filter_request_vote(false,$is_ajax);
				}
			}
			else 
			{
				self::filter_request($value);
			}
		}
		self::filter_request_vote(true,$is_ajax);
	}
	
	public static function filter_request_vote($boolean,$is_ajax){
		if($boolean === false){
			if($is_ajax == 1){
				exit('您输入了非法字符,数据将不被保存,请核对后重新输入。');
			}else{
				header("location:".DOMAIN_USER.'filter_error.php');
				exit();
			}
		}
	}
	
	/**
	 * 替换富文本中的<script></script>标签中的"<",">"为预定义的字符&lt;和&gt;
	 * @param string $content 输入内容
	 */
	public static function escape_script($content)
	{
		//htmlspecialchars
		$content = str_ireplace("<script","&lt;script",$content);
		return str_ireplace("script>","script&gt;",$content);
	}
}