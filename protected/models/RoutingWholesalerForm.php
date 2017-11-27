<?php
/**
 * Description of RoutingWholesalerForm
 *
 * @author ramon
 */
class RoutingWholesalerForm extends CFormModel 
{
    /**
     * ClientWholesalerId (@ client_wholesaler)
     * @var int
     */
    public $wholesalerId;
    
    public $clientId;
    public $code;
    public $name;
    public $alias;
    public $address1;
    public $address2;
    public $address3;
    public $address4;
    public $address5;
    public $groupUnder;
    
    public $linkedWholesalers;
    
    public function rules()
    {
        return array(
            array('code, name, alias', 'required'),
            );
    }
    
    public function populate()
    {
        if (!isset($this->wholesalerId) || empty($this->wholesalerId))
                return FALSE;
        
        $info = ClientWholesaler::model()->findByPk($this->wholesalerId);
        if (!isset($info))
            return FALSE;
        
        // populate details
        $this->clientId = $info->ClientId;
        $this->code = $info->WholesalerId;
        $this->name = $info->Name;
        $this->alias = $info->FriendlyName;
        $this->address1 = $info->Address1;
        $this->address2 = $info->Address2;
        $this->address3 = $info->Address3;
        $this->address4 = $info->Address4;
        $this->address5 = $info->Address5;
        $this->groupUnder = $info->MainClientWholesalerId;
        
        // populate linked wholesalers
        $this->linkedWholesalers = ClientWholesaler::model()->findAll( array (
                    'condition' => 'MainClientWholesalerId = :wsid',
                    'params' => array(':wsid' => $this->wholesalerId),
                    'order' => 'FriendlyName ASC'
                ));
        
        return TRUE;
    }
    
    public function save()
    {
        $info = ClientWholesaler::model()->findByPk($this->wholesalerId);
        
        $info->WholesalerId = $this->code;
        $info->Name = $this->name;
        $info->FriendlyName = $this->alias;
        $info->Address1 = $this->address1;
        $info->Address2 = $this->address2;
        $info->Address3 = $this->address3;
        $info->Address4 = $this->address4;
        $info->Address5 = $this->address5;
        $info->MainClientWholesalerId = ($this->groupUnder != '') ? $this->groupUnder : NULL;
        
        $info->save();
        return TRUE;
    }
    
    public function getWholesalerOptions()
    {
        // list all wholesalers used in routes, for current client
        // exclude same wholesaler
        $query = "select w.*
                from client_wholesaler w left join client_route_details d
                on w.ClientWholesalerId = d.ClientWholesalerId
                where w.ClientId = :cid
                  and w.ClientWholesalerId != :wsid
                  and d.ClientRouteDetailsId is not null
                order by FriendlyName";
        $data = Yii::app()->db
                ->createCommand($query)
                ->queryAll(true, array(
                    ':cid' => $this->clientId,
                    ':wsid' => $this->wholesalerId,
                ));
        
        $result = array();
        foreach($data as $w)
        {
            $result[$w['ClientWholesalerId']] =   $w['WholesalerId']." - ".$w['FriendlyName'];
            $result[$w['ClientWholesalerId']] .= ($w['FriendlyName'] != $w['Name']) ? " ({$w['Name']})" : "";
        }
        return $result;
    }
}
?>
