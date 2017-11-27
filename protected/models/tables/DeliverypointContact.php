<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DeliverypointContact
 *
 * @author sanim
 */
class DeliverypointContact  extends CActiveRecord{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'deliverypoint_contact';
    }
}
