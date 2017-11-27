<?php
/**
 * Description of FinanceComment
 *
 * @author ramon
 */
class FinanceComment extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_comment';
    }
    
    public function relations(){
        return array(
                'login' => array (self::BELONGS_TO, "Login", "CreatedBy"),
        );
    }
}

?>