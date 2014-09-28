<?php
class pagecommon extends CWidget{
        public $file='';
		public $yiithemes_dir='';
	public function init(){                      
        parent::init();
		$this -> yiithemes_dir = dirname(Yii::getFrameworkPath()). '/themes/';
	}
	public function run(){
		include $this -> yiithemes_dir. 'common/'. $this -> file;
	}
}