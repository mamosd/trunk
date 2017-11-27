<?php
/**
 * @property int    Id
 * @property string Md5
 * @property string FileName
 * @property int    UploadedBy
 * @property string UploadedDate
*/
class PolestarUploadedFile extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_uploaded_file';
    }

}
?>