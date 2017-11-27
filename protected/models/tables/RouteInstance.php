<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RouteInstance
 *
 * @author Ramon
 */
class RouteInstance extends CActiveRecord
{
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_ARCHIVED = 'ARCHIVED';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'routeinstance';
    }
}
?>