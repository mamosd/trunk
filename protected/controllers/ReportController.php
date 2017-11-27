<?php
/**
 * Description of ReportController
 *
 * @author ramon
 */
class ReportController extends Controller {
    
    public function getValidUsers()
    {
        $result = array();
        $logins = Login::model()->findAll("LoginRoleId=:role OR LoginRoleId=:rolesa", array(":role"=>LoginRole::ADMINISTRATOR, ":rolesa" => LoginRole::SUPER_ADMIN));
        foreach ($logins as $login) {
            $result[] = $login->UserName;
        }
        return $result;
    }
    
    public function actionWholesalerreport()
    {
        if (isset($_POST['Report']))
        {
            $model = new ReportWholesaler();
            $model->setAttributes($_POST['Report'], false);
            $model->outputCsv();
        }
        Yii::app()->end();
    }
    
    public function actionBoomsheet()
    {
        if (isset($_POST['Report']))
        {
            $model = new ReportBoomSheet();
            $model->setAttributes($_POST['Report'], false);
            $model->outputCsv();
        }
        Yii::app()->end();
    }
}

?>
