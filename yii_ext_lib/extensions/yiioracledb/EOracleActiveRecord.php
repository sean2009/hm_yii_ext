<?php


abstract class EOracleActiveRecord extends CActiveRecord {
	
	protected $_attributes;

    public function init() {
        parent::init();

        $sequenceName = $this->sequenceName();
        if ($sequenceName) {
            $this->getMetaData()->tableSchema->sequenceName = $sequenceName;
        }
    }

    public function sequenceName() {
        
    }
    
    public function setAttributes($values, $safeOnly = false) {
        return parent::setAttributes($values, $safeOnly);
    }
    
}
