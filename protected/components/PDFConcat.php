<?php
/**
 * Description of PDFConcat
 *
 * @author ramon
 */
$fpdfPath = Yii::getPathOfAlias('ext.fpdf');
$fpdiPath = Yii::getPathOfAlias('ext.fpdi');
require_once $fpdfPath.'/fpdf.php';
require_once $fpdiPath.'/fpdi.php';
 
class PDFConcat extends FPDI { 
    var $files = array(); 
    function setFiles($files) { 
        $this->files = $files; 
    } 

    function concat() { 
        foreach($this->files AS $file => $style) { 
            $pagecount = $this->setSourceFile($file); 
            for ($i = 1; $i <= $pagecount; $i++) { 
                 $tplidx = $this->ImportPage($i); 
                 $s = $this->getTemplatesize($tplidx); 
                 $this->AddPage($style, array($s['w'], $s['h'])); 
                 $this->useTemplate($tplidx); 
            } 
        } 
    } 
} 