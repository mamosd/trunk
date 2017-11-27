<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PolestarPrintCentre
 *
 * @property int    Id
 * @property string Name
 * @property string JobPrefix
 * @property int    Live
 * @property string Address1
 * @property string Address2
 * @property string Address3
 * @property string Address4
 * @property string Postcode
 * @property string LateAdviseCutoff
 * @property int    CreatedBy
 * @property string CreatedDate
 * @property int    EditedBy
 * @property string EditedDate
 *
 * @author ramon
 */
class PolestarPrintCentre extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_printcentre';
    }

    public static function getAllAsOptions() {
        $all = PolestarPrintCentre::model()->findAll(array(
            'order' => 'Name ASC',
            'condition' => 'Live = 1'
        ));
        $result = array();
        foreach ($all as $pc) {
            $result[$pc->Id] = $pc->Name;
        }
        return $result;
    }
    
    public static function getAllForLoginAsOptions() {
        $loginId = Yii::app()->user->loginId;
        
        
        $crit = new CDbCriteria();
        $crit->order =  'Name ASC';
        $crit->condition = 'Live = 1';
        if (Yii::app()->user->role->LoginRoleId != LoginRole::SUPER_ADMIN) {
            $pcIds = PolestarPrintCentreLogin::model()->findAll("LoginId = :lid", array(":lid" => $loginId));
            $ids = array();
            foreach ($pcIds as $id)
                $ids[] = $id->PrintCentreId;
            $crit->addInCondition('Id', $ids);
        }
        
        $all = PolestarPrintCentre::model()->findAll($crit);
        $result = array();
        foreach ($all as $pc) {
            $result[$pc->Id] = $pc->Name;
        }
        return $result;
    }
    
    public function getSingleLineAddress() {
        $result = (empty($this->Address1)) ? '' : $this->Address1;
        if (!empty($result) && !empty($this->Address2)) $result .= ", ";
        $result .= $this->Address2;
        if (!empty($result) && !empty($this->Address3)) $result .= ", ";
        $result .= $this->Address3;
        if (!empty($result) && !empty($this->Address4)) $result .= ", ";
        $result .= $this->Address4;
        
        return $result;
    }
    
    public static function getUsersAsOptions($pcid = NULL) {
        $printCentreIds = array($pcid);
        if (!isset($pcid)) { // only output users for applicable print centres
            $printCentres = PolestarPrintCentre::getAllForLoginAsOptions();
            $printCentreIds = array_keys($printCentres);
        }
        $crit = new CDbCriteria();
        $crit->addInCondition('PrintCentreId', $printCentreIds);
        $crit->alias = 'pc';
        $crit->group = 'pc.LoginId';
        $crit->order = 'Login.FriendlyName';
        $all = PolestarPrintCentreLogin::model()->with('Login')->findAll($crit);
        $result = array();
        foreach ($all as $l) {
            $result[$l->LoginId] = $l->Login->FriendlyName;
        }
        return $result;
    }
}
