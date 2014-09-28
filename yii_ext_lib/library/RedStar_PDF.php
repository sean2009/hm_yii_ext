<?php

define('REDSTAR_PDF_PATH', dirname(__FILE__));
define('PDF_PAGE_FORMAT', 'letter');
define('PDF_UNIT', 'pt');

require_once REDSTAR_PDF_PATH . '/tcpdf/tcpdf.php';

class RedStar_PDF extends TCPDF {
    
    public function __construct() {
        parent::__construct();
        $this->SetCreator('RedStar');
        $this->SetAuthor('星易家');
        $this->SetFont('stsongstdlight', '', 20);
    }
    
}