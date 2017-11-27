<?php
/**
 * Description of TitleForm
 *
 * @author Sanim
 */
class ClientTitleForm extends CFormModel
{
    public $ClientTitleId;
    public $ClientId;
    public $TitleId;
    public $TitleType;
    public $Name;
    public $IsLive;
   
    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('TitleId, Name', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    
//    public function attributeLabels()
//    {
//        return array(
//            'printCentreId'=>'Print Centre',
//            'clientLoginId'=>'Login / Region',
//        );
//    }
    
    public function getOptionsClientId()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->condition .= "RoutingEnabled = 1";
        $criteria->order = "Name ASC";
        $dps = Client::model()->findAll($criteria);
        foreach ($dps as $dp) {
            $result[$dp->ClientId] = $dp->Name;
        }
        return $result;
    }

   

    public function populate($id)
    {
        if (isset($id)) {
            $title = ClientTitle::model()->findByPk($id);
            if (isset($title)) {
                $this->ClientTitleId  = $title->ClientTitleId;
                $this->ClientId  = $title->ClientId;
                $this->TitleId  = $title->TitleId;
                $this->TitleType  = $title->TitleType;
                $this->Name  = $title->Name;
                $this->IsLive = $title->IsLive;
            }
        }
    }


    
    public function save()
    {
        $title = ($this->ClientTitleId !== '') ? ClientTitle::model()->findByPk($this->ClientTitleId) : new ClientTitle();
        $title->ClientId = $this->ClientId;
        $title->TitleId = $this->TitleId;
        $title->TitleType = $this->TitleType;
        $title->Name = $this->Name;
        $title->IsLive = $this->IsLive;
        return $title->save();
    }
}
?>
