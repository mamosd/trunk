<?php
/**
 * Description of RoutingImportForm
 *
 * @author ramon
 */
class RoutingImportForm  extends CFormModel 
{
    public $clientId;
    public $orderFile;
    
    public function attributeLabels()
    {
        return array(
            'clientId' => 'Client',
        );
    }
    
    public function rules()
    {
        return array(
            array('clientId', 'required'),
            array('orderFile', 'file', 'types'=>'csv')
            );
    }
    
    public function parse($fileName){
    	$processor = new CSVOrderImport();
        if (!$processor->parseFile($fileName, $this->clientId)){
        	$this->addError("", $processor->errorMessage);
        }
    }
    
    public function getClientOptions()
    {
        $clients = Client::model()->findAll(array(
                                                'condition' => 'RoutingEnabled = 1',
                                                'order' => 'Name ASC'
            ));
        $result = array();
        foreach ($clients as $client) {
            $result[$client->ClientId] = $client->Name;
        }
        return $result;
    }
}

?>
