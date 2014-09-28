<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<meta name="Keywords" content="<?php echo CHtml::encode($this->pageKeywords); ?>" />
<meta name="Description" content="<?php echo CHtml::encode($this->pageDescription); ?>" />
<meta name="robots" content="noindex, nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="<?php echo Yii::app()->baseUrl?>/data/jquery.min.js"></script>
</head>
<body>
<?php echo $content;?>	
</body>
</html>