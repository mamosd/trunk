<?php
/**
 * Description of ClientController
 *
 * @author Ramon
 */
class ClientController extends Controller {

    // this is to be retrieved by implementation (hosting customer) when SaaS
    public $menu = array(
           // array('label'=>'Titles', 'url'=>array('client/titles')),
           // array('label'=>'Order Listing', 'url'=>array('client/orders')),
        );

    /**
     * Used to retrieve the users with granted access to this controller's actions.
     * @return <type>
     */
    public function getValidUsers()
    {
        $result = array();
        $logins = Login::model()->findAll("LoginRoleId=:role", array(":role"=>LoginRole::CLIENT));
        foreach ($logins as $login) {
            $result[] = $login->UserName;
        }
        return $result;
    }

    public function actionTitles()
    {
        echo "disabled by development <br/>";
        echo CHtml::link('logout', array('account/logout'));
        Yii::app()->end();

        $model = new OrderGroupForm();

        // collect user input data
        if(isset($_POST['OrderDetails']))
        {
            //$model->setAttributes($_POST['OrderForm'], false);
            $model->setOrdersDetails($_POST['OrderDetails']);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save($_POST['txtSaveStatus'])) {
                //$this->redirect(array('client/titles'));
                $this->refresh();
            }
        }


        $this->render('titles', array('model'=>$model));
    }

    public function actionOrders()
    {
        $model = new OrderListing();
        $message = '';

        if(isset($_POST['OrderDetails']))
        {
            // save order details
            if($model->saveOrder($_POST['OrderDetails']))
                $message = 'Order information saved succesfully';
            else
                $message = 'There was an error while attempting to save, please try again.';
        }
        
        $model->populate(Yii::app()->user->loginId);
        $this->render('orders', array('model'=>$model, 'message'=>$message));
    }
/*
    public function actionOrder($titleId =  NULL)
    {
        $model = new OrderForm();

        // collect user input data
        if(isset($_POST['OrderForm']))
        {
            $model->setAttributes($_POST['OrderForm'], false);
            $model->setOrderDetails($_POST['OrderDetails']);

            // validate user input and redirect to the suppliers page if valid
            if($model->validate() && $model->save()) {
                $this->redirect(array('client/titles'));
            }
        }
        else {
            if (isset($titleId)) {
                $model->populate($titleId);
            }
        }

        $this->render('order', array('model'=>$model));
    }
 *
 */
}
?>
