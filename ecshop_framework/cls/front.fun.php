<?php
	
	/**
	 * 地址选择拼音比较
	 * @param unknown_type $a
	 * @param unknown_type $b
	 * @return number
	 */
	function cmpSpelling($a,$b){
		if($a['spelling'] == $b['spelling']){
			return 0;
		}
		return $a['spelling']<$b['spelling']?-1:1;
	}


?>