<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AllRouteDetails
 *
 * @author Ramon
 */
class AllRouteDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'allroutedetails';
    }
}
?>