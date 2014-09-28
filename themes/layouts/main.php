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
<script src="<?php echo Yii::app()->baseUrl;?>/data/static/jquery.min.js"></script>
<link rel="stylesheet" href="http://yandex.st/highlightjs/7.3/styles/default.min.css">
<script src="http://yandex.st/highlightjs/7.3/highlight.min.js"></script>
<script>
  hljs.tabReplace = '    ';
  hljs.initHighlightingOnLoad();
  </script>
<style>
.red{color:red}
    body {
      font: small Arial, sans-serif;
    }
    h2 {
      font: bold 100% Arial, sans-serif;
      margin-top: 2em;
      margin-bottom: 0.5em;
    }
    table {
      width: 100%; padding: 0; border-collapse: collapse;
    }
    th {
      width: 12em;
      padding: 0; margin: 0;
    }
    td {
      padding-bottom: 1em;
    }
    td, th {
      vertical-align: top;
      text-align: left;
    }
    pre {
      margin: 0; font-size: medium;
    }
    label{
    	width:100px;display:inline-block;
	}
    #switch {
      overflow: auto; width: 67em;
      list-style: none;
      padding: 0; margin: 0;
    }
    #switch li {
      float: left; width: 12em;
      padding: 0.1em; margin: 0.1em 1em 0.1em 0;
      background: #EEE;
      cursor: pointer;
    }
    #switch li.current {
      background: #CCC;
    }
    .test {
      color: #666;
      font-weight: normal;
      width:100%; height:100%; border:#888 1px solid;
      padding:10px;
    }
    .test var {
      font-style: normal;
    }
    .passed {
      color: green;
    }
    .failed {
      color: red;
    }
    .code {
      font: medium monospace;
    }
    .code .keyword {
      font-weight: bold;
    }
</style>
</head>
<body>
<div>
<br></br>
<a href="<?php echo $this->createUrl('named/index');?>">命名规范</a> | 
<a href="<?php echo $this->createUrl('newProject/index');?>">创建新应用</a> | 
<a href="<?php echo $this->createUrl('controller/index');?>">Controller</a> | 
<a href="<?php echo $this->createUrl('model/index');?>">Model</a> | 
<a href="<?php echo $this->createUrl('view/index');?>">View</a> | 
<a href="<?php echo $this->createUrl('form/index');?>">Form</a> | 
<a href="<?php echo $this->createUrl('widget/index');?>">Widget</a> | 
<a href="<?php echo $this->createUrl('filter/index');?>">Filter</a> |
<a href="<?php echo $this->createUrl('themes/index');?>">模版themes</a> |  
<a href="<?php echo $this->createUrl('error/index');?>">异常</a> | 
<a href="<?php echo $this->createUrl('qianYi/index');?>">框架迁移</a> |  
Web(
<a href="<?php echo $this->createUrl('webService/index');?>">WebService</a> | 
<a href="<?php echo $this->createUrl('curl/index');?>">Http Curl</a> | 
<a href="<?php echo $this->createUrl('hessian/index');?>">hessian</a>
)
单点登录

</div>
<br /><br />
<?php echo $content;?>	
</body>
</html>
