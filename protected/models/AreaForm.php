<?php
/**
 * Description of AreaForm
 *
 * @author Ramon
 */
class AreaForm extends CFormModel
{
    public $areaId;
    public $name;
    
    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
        );
    }

    public function populate($id)
    {
        if(isset($id)){
            $area = Area::model()->findByPk($id);
            if (isset($area)) {
                $this->areaId = $area->Id;
                $this->name = $area->Name;
            }
        }
    }

    public function save()
    {
        $area = ($this->areaId !== '') ? Area::model()->findByPk($this->areaId) : new Area();
        if ($area->isNewRecord) {
            $area->DateUpdated = new CDbExpression('NOW()');
        }
        $area->Name = $this->name;
        $area->UpdatedBy = Yii::app()->user->name;
        
        return $area->save();
    }
}
?>
