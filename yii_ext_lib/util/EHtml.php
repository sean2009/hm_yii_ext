<?php
class EHtml {
	
	public static $errorCss = 'error';
	public static $idSuffix = '_invalid';
	
	/**
	 * 显示模型属性的第一个验证错误。
	 * @param object $model  数据模型。
	 * @param string $attribute  属性名称。
	 * @param string $valname  自定义验证属性名。
	 * @param array $htmlOptions 额外的HTML属性要呈现在容器div标签。
	 * @return string 错误显示。空的，如果没有发现任何错误。For example:<div class="error" id="valpersonalemail_invalid">请输入您的用户名或Email</div> 
	 */
	public static function error($model, $attribute, $valname = null, $htmlOptions = array()) {
		CHtml::resolveName ( $model, $attribute ); // turn [a][b]attr into attr
		$error = $model->getError ( $attribute );
		if ($error != '') {
			if (! isset ( $htmlOptions ['class'] ))
				$htmlOptions ['class'] = self::$errorCss;
			if (! isset ( $htmlOptions ['id'] ) && ! empty ( $valname ))
				$htmlOptions ['id'] = $valname . self::$idSuffix;
			return CHtml::tag ( 'div', $htmlOptions, $error );//<div class="error">ssss</div>
		} else
			return '';
	}
	
	/**
	 * 显示错误信息
	 * @param object $model
	 * @return string
	 */
	public static function errorSummary($model){
		if($model->hasErrors()){
			//<div class="server_tip"><span class="fr">×</span>森林砍伐京东方斯蒂芬</div>
			$errors = $model->getErrors();
			$return = CHtml::openTag('div',array('class'=>'server_tip'));
			$return .= CHtml::tag('span',array('class'=>'fr'),'×');
			foreach($errors as $key => $val){
				$return .= $val[0].'; ';
			}
			$return .= CHtml::closeTag('div');
			return $return;
		}
	}
}
?>