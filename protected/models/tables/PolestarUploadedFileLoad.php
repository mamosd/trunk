<?php
/**
 * @property int    FileId
 * @property int    LoadId
*/
class PolestarUploadedFileLoad extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_uploaded_file_load';
    }

}
?>