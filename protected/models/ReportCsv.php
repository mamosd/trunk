<?php
/**
 * Description of ReportCsv
 *
 * @author ramon
 */
class ReportCsv extends CFormModel 
{
    /**
     *
     * @param array $data Associative array with csv headers as keys
     * @param string $fileName
     * @return type 
     */
    public function output($data, $fileName)
    {
//        $data = $this->getData();
        
        $local_file = tempnam('/tmp','');
        $temp = fopen($local_file,"w");
        $headerDone = FALSE;
        foreach ($data as $d){
            if ($headerDone === FALSE)
            {
                $headerDone = TRUE;
                fputcsv($temp, array_keys($d));
            }
            
            fputcsv($temp, array_values($d));
        }

        fclose($temp);
        //header("Content-Disposition: attachment; filename=\"".date("His")."-wholesalers.csv\"");
        header("Content-Disposition: attachment; filename=\"".$fileName."\"");
        header("Content-Type: application/force-csv");
        header("Content-Length: " . filesize($local_file));
        header("Connection: close");
        readfile($local_file);

        unlink($local_file); // this removes the file
        
        return TRUE;
    }
}

?>
