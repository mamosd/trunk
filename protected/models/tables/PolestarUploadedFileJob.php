<?php
/**
 * @property int    FileId
 * @property int    JobId
*/
class PolestarUploadedFileJob extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_uploaded_file_job';
    }

}
?>