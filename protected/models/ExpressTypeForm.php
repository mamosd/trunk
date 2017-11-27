<?php
/**
 * Description of TitleForm
 *
 * @author Sanim
 */
class ExpressTypeForm extends CFormModel
{
    public $titleId;
    public $name;
    public $isLive;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    
    /*
    public function attributeLabels()
    {
        return array(
            'printCentreId'=>'Print Centre',
            'clientLoginId'=>'Login / Region',
        );
    }

    public function getOptionsClientLogin()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = 'ClientName ASC, LoginName ASC';
        $cls = AllClientLogins::model()->findAll($criteria);
        foreach ($cls as $cl) {
            //$result[$cl->ClientLoginId] = $cl->ClientName.' - '.$cl->LoginName;
            $result[$cl->ClientLoginId] = $cl->FriendlyName;
        }
        return $result;
    }

    public function getOptionsPrintCentre()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = 'Name ASC';
        $pcs = PrintCentre::model()->findAll($criteria);
        foreach ($pcs as $pc) {
            $result[$pc->PrintCentreId] = $pc->Name;
        }
        return $result;
    }

    public function populate($id)
    {
        if (isset($id)) {
            $title = ExpressTitles::model()->findByPk($id);
            if (isset($title)) {
                $this->titleId  = $title->titleId;
                $this->name = $title->name;
                $this->isLive = $title->IsLive;
            }
        }
    }
    */


    public function getOptionsExpressTitles()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->condition .= "IsLive = 1";
        $criteria->order = "Name ASC";
        $dps = ExpressTitles::model()->findAll($criteria);
        foreach ($dps as $dp) {
            $result[$dp->titleId] = $dp->name;
        }
        return $result;
    }
    
    public function save()
    {
        $title = ($this->titleId !== '') ? ExpressTypes::model()->findByPk($this->titleId) : new ExpressTypes();
        $title->name = $this->name;
        $title->IsLive = $this->isLive;
        return $title->save();
    }
}
?>
