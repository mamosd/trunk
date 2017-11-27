<?php
/**
 * Description of SecondaryRoutingRouteForm
 *
 * @author Ramon
 */
class SecondaryRoutingRouteForm extends CFormModel
{
    public $routeId;
    public $details;
    public $sortOrder;

    public $selectedRoute;
    public $selectedRounds;
    public $axn;

    public function populate($id)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'SecondaryRouteId=:rid';
        $criteria->params = array(':rid' => $id);
        $criteria->order = 'SortOrder ASC';
        $result = AllSecondaryRoute::model()->findAll($criteria);

        if (isset($result))
        {
            $this->routeId = $id;
            $this->details = $result;
            return TRUE;
        }
        return FALSE;
    }

    public function save()
    {
        $info = explode('|', $this->sortOrder);
        $count = count($info);
        for($i = 0; $i < $count; $i++)
        {
            $roundId = trim($info[$i]);
            if ($roundId != "")
            {
                SecondaryRouteRound::model()->updateAll(array('SortOrder' => ($i+1), 'DateUpdated' => new CDbExpression('NOW()')),
                        "SecondaryRouteId=:routeid AND SecondaryRoundId=:roundid",
                        array(':routeid' => $this->routeId,
                            ':roundid' => $roundId));
            }
        }
        SecondaryRoute::model()->updateByPk($this->routeId, array('DateUpdated' => new CDbExpression('NOW()')));

        return TRUE;
    }

    public function getRoutes()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->condition = 'SecondaryRouteId != :rid';
        $criteria->params = array(':rid' => $this->routeId);
        $criteria->order = "SecondaryRouteId ASC";
        $routes = SecondaryRoute::model()->findAll($criteria);

        foreach ($routes as $r) {
            $result[$r->SecondaryRouteId] = $r->SecondaryRouteId;
        }
        return $result;
    }

    public function moveRounds()
    {
        $info = explode('|', $this->selectedRounds);
        $count = count($info);
        for($i = 0; $i < $count; $i++)
        {
            $roundId = trim($info[$i]);
            if ($roundId != "")
            {
                SecondaryRouteRound::model()->updateAll(array('SecondaryRouteId' => $this->selectedRoute, 
                                                            'SortOrder' => 9999,
                                                            'DateUpdated' => new CDbExpression('NOW()')),
                        "SecondaryRouteId=:routeid AND SecondaryRoundId=:roundid",
                        array(':routeid' => $this->routeId,
                            ':roundid' => $roundId));
            }
        }
        SecondaryRoute::model()->updateByPk($this->routeId, array('DateUpdated' => new CDbExpression('NOW()')));
        SecondaryRoute::model()->updateByPk($this->selectedRoute, array('DateUpdated' => new CDbExpression('NOW()')));

        return TRUE;
    }

    public function deleteRounds()
    {
        $info = explode('|', $this->selectedRounds);
        $count = count($info);
        for($i = 0; $i < $count; $i++)
        {
            $roundId = trim($info[$i]);
            if ($roundId != "")
                SecondaryRouteRound::model()->deleteAll('SecondaryRouteId=:routeid AND SecondaryRoundId=:roundid', array(':routeid' => $this->routeId, ':roundid' => $roundId));
        }
        SecondaryRoute::model()->updateByPk($this->routeId, array('DateUpdated' => new CDbExpression('NOW()')));

        return TRUE;
    }

    public static function deleteRoute($id)
    {
        SecondaryRouteRound::model()->deleteAll('SecondaryRouteId=:rid', array(':rid'=>$id));
        SecondaryRoute::model()->deleteAll('SecondaryRouteId=:rid', array(':rid'=>$id));
    }
}
?>
