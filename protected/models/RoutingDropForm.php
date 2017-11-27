<?php
/**
 * Description of RoutingDropForm
 *
 * @author ramon
 */
class RoutingDropForm extends CFormModel
{
    public $routeInstanceId;
    public $wholesalerId;
    public $details;
    
    public function populate($routeInstanceId, $wholesalerId)
    {
        $info = ClientRouteInstanceInfo::model()->findAll(array(
                                                'condition' => "ClientRouteInstanceId = :rid and ClientWholesalerId = :wsid",
                                                'params' => array(':rid' => $routeInstanceId, ':wsid' => $wholesalerId),
                                                'order' => 'DepartureTimeSort ASC, RouteId ASC, ArrivalTimeSort ASC, TitleName ASC'
                    ));
        
        if (!empty($info))
        {
            $this->details = $info;
            $this->routeInstanceId = $routeInstanceId;
            $this->wholesalerId = $wholesalerId;
        }
        else
            return FALSE;
    }
    
    public function saveDrops($data)
    {
        foreach($data as $dropId => $info)
        {
            $drop = ClientRouteInstanceDrop::model()->findByPk($dropId);
            if (isset($drop))
            {
                $drop->PubPagination = $info['Pagination'];
                $drop->PubWeight = $info['Weight'];
                $drop->BundleSize = $info['BundleSize'];
                $drop->Quantity = $info['Quantity'];
                $drop->DateUpdated = new CDbExpression('NOW()');
                $drop->save();
            }
        }
    }
    
    public function getTitleOptions()
    {
        $titles = ClientTitle::model()->findAll(array(
            'condition' => 'ClientId = :cid and IsLive=1',
            'params' => array(':cid' => $this->details[0]->ClientId),
            'order' => 'TitleType ASC, Name ASC'
        ));
        
        $options = array();
        foreach($titles as $t)
        {
            $options[$t->ClientTitleId] = $t->TitleId.' - '.$t->Name;
        }
        
        $attrs = array();
        $info = ClientRouteInstanceInfo::model()->findAll(array(
                    'condition' => "ClientId = :cid
                                      and TitleType = 'M'
                                      and DateUpdated > cast(date_sub(curdate(), interval WEEKDAY(curdate()) day) as datetime) /* Last Monday */
                                      and PrintCentreId = :pcid",
                    'params' => array(':cid' => $this->details[0]->ClientId, ':pcid' => $this->details[0]->PrintCentreId),
                    'group' => 'ClientTitleId'
                ));
        $attrs = array();
        foreach ($info as $item)
        {
            $attrs[$item->ClientTitleId] = array(
                'pagination' => $item->PubPagination,
                'bundle' => $item->BundleSize,
                'weight' => $item->PubWeight
            );
        }
        $result = array('options' => $options, 'attrs' => $attrs);
        
        return $result;
    }
    
    public static function addTitle($data)
    {
        $details = ClientRouteInstanceInfo::model()->find(array(
            'condition' => 'ClientRouteInstanceDetailsId = :did',
            'params' => array(':did' => $data['DetailsId'])
        ));
        
        if (isset($details))
        {
            $tId = $data['Id'];
            if (empty($tId)) // non-existing title
            {
                $title = new ClientTitle();
                $title->ClientId = $details->ClientId;
                $title->TitleId = $data['Code'];
                $title->TitleType = $data['Type'];
                $title->Name = $data['Name'];
                $title->save();
                $tId = $title->ClientTitleId;
            }
            
            $existing = ClientRouteInstanceDrop::model()->find(array(
                'condition' => 'ClientRouteInstanceDetailsId = :did AND ClientTitleId = :tid',
                'params' => array(':did' => $details->ClientRouteInstanceDetailsId, ':tid' => $tId)
            ));
            if (!isset($existing))
            {
                $drop = new ClientRouteInstanceDrop();
                $drop->ClientRouteInstanceDetailsId = $details->ClientRouteInstanceDetailsId;
                $drop->ClientTitleId = $tId;
                $drop->PubPagination = $data['Pagination'];
                $drop->PubWeight = $data['Weight'];
                $drop->BundleSize = $data['BundleSize'];
                $drop->Quantity = $data['Quantity'];
                $drop->DateUpdated = new CDbExpression('NOW()');
                $drop->save();
            }
        }
    }
    
    public static function deleteTitle($dropId)
    {
        ClientRouteInstanceDrop::model()->deleteByPk($dropId);
    }
}

?>
