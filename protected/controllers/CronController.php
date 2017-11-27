<?php
/**
 * Description of CronController
 *
 * @author ramon
 */
class CronController extends Controller  {
    
    private $checkCode = 'Rj2l4Su6e1mu43';
    
    //put your code here
    public function actionCleanuprounds($code)
    {
        if($code != $this->checkCode) { 
            throw new CHttpException(403, 'You are not authorized to see this page.');
        }
        
        $rounds = SecondaryRouteRound::model()->deleteAll('DateUpdated < date_sub(now(), interval 4 week)');
        echo "$rounds rounds deleted.<br/>";
        $routes = SecondaryRoute::model()->deleteAll('DateUpdated < date_sub(now(), interval 4 week)');
        echo "$routes routes deleted.<br/>";
        echo "Cleanup completed.<br/>";
    }
}

?>
