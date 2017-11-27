<?php
/**
 * Description of TitleForm
 *
 * @author Ramon
 */
class TitleForm extends CFormModel
{
    public $titleId;
    public $clientLoginId;
    public $printCentreId;
    public $name;
    public $printDay;
    public $offPressTime;
    public $weightPerPage;
    public $isLive;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('name, printCentreId, clientLoginId, printDay, offPressTime, weightPerPage', 'required'),
            array('weightPerPage', 'numerical'),
        );
    }

    /**
     * Declares attribute labels.
     */
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
            $title = Title::model()->findByPk($id);
            if (isset($title)) {
                $this->titleId  = $title->TitleId;
                $this->clientLoginId = $title->ClientLoginId;
                $this->printCentreId = $title->PrintCentreId;
                $this->name = $title->Name;
                $this->printDay = $title->PrintDay;
                $this->offPressTime = $title->OffPressTime;
                $this->weightPerPage = $title->WeightPerPage;
                $this->isLive = $title->IsLive;
            }
        }
    }

    public function save()
    {
        $title = ($this->titleId !== '') ? Title::model()->findByPk($this->titleId) : new Title();
        if ($title->isNewRecord) {
            $title->DateCreated = new CDbExpression('NOW()');
        }
        $title->ClientLoginId = $this->clientLoginId;
        $title->PrintCentreId = $this->printCentreId;
        $title->Name = $this->name;
        $title->PrintDay = $this->printDay;
        $title->OffPressTime = $this->offPressTime;
        $title->WeightPerPage = $this->weightPerPage;
        $title->IsLive = $this->isLive;
        $title->DateUpdated = new CDbExpression('NOW()');
        $title->UpdatedBy = Yii::app()->user->name;
        return $title->save();
    }
}
?>
