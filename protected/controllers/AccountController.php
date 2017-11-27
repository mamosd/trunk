<?php
/* created by Ramon */

class AccountController extends Controller {

    /**
     * allow access to all users (anonymous + authenticated)
     * to the login action ONLY
     * @return array
     */
    public function  accessRules() {
        return array(
            array('allow',
                'actions' => array('login','forgotpassword','checkuserexist','vertoken'),
                'users' => array('*')),
            array('deny',
                'users' => array('?'))
        );
    }
    
    /**
     * Works with ajax to check if user exists
     */    
    public function actioncheckUserExist()
    {
        
        $model = Login::model()->find("email = '".$_POST['userdata']."' OR username = '".$_POST['userdata']."'");
        if (empty($model))
        {
            //echo "not exist";            
        }
        else 
        {
            if(!filter_var($model->Email, FILTER_VALIDATE_EMAIL))
            {
                echo "No valid Email found";
            }
            else 
            {
                echo CHtml::image('img/icons/accept.png');
            }
            
        }
        

    }
    
    /**
     * Get password reset token value from database
     */    
    public function getToken($token)
    {
        $model=Login::model()->findByAttributes(array('token'=>$token));
        if($model===null)
            throw new CHttpException(404,'The token is not valid.');
        return $model;
    }
  
    /**
     * If new password validated, save new password
     */    
    public function actionVerToken($token)
    {
        $formmodel=new PWResetForm;
        $login=$this->getToken($token);
        
        
        if(isset($_POST['PWResetForm']))
        {
            $formmodel->attributes = $_POST['PWResetForm'];
            // validate user input and redirect to the previous page if valid
            if ( $formmodel->validate() )
            {
                $login->Password=md5($_POST['PWResetForm']['newPassword']);
                $login->token="null";
                $login->save();
                Yii::app()->user->setFlash('PWResetForm','<b>Password has been successfully changed! please login</b>');
                $this->redirect('?r=account/login');
            }
            //$this->redirect(array('site/index'));
            
        }
        else 
        {
            Yii::app()->user->setFlash('PWHints','<b>Password Guidance : Please ensure your password contains at least 1 uppercase letter and 1 number.</b>');
        }
        
        $this->layout = "//layouts/loggedOut";
        $this->render('PWReset',array('model'=>$login,'formmodel'=>$formmodel,));

        
    }   
    
    /**
     * Displays the password reset page
     */
    public function actionForgotPassword()
    {
        //echo "234324";
        $model=new LoginForm;
        
        $this->layout = "//layouts/loggedOut";

        if(isset($_POST['usernameORemail']))
        {
            $userCheck = Login::model()->find("email = '".$_POST['usernameORemail']."' OR username = '".$_POST['usernameORemail']."'");
            
            if (empty($userCheck))
            {
                Yii::app()->user->setFlash('userOrEmail', "User does not exist!");
            }
            else if(!filter_var($userCheck->Email, FILTER_VALIDATE_EMAIL))
            {
                Yii::app()->user->setFlash('userOrEmail', "This user does not have a valid email on file and the password reset email cannot be sent. Please contact an authorized user to modify the user's profile.");
            }
            else
            {
                $getToken=rand(0, 99999);
                $getTime=date("H:i:s");
                $userCheck->token=md5($getToken.$getTime);
                
                //$setpesan="you have successfully reset your password<br/>
                //    ". CHtml::link('Click Here to Reset Password', $this->createAbsoluteUrl('account/vertoken', array('token'=>$userCheck->token)));
                //$token=$userCheck->token;
                
                $link=CHtml::link('Click Here to Reset Password', $this->createAbsoluteUrl('account/vertoken', array('token'=>$userCheck->token)));
                //echo $link;
                if($userCheck->validate())
                {
                    $getEmail= $userCheck->Email;
                    
                    $userCheck->save();
                    Yii::app()->user->setFlash('PWResetLinkSent','Link to reset your password has been sent to your email');
                    //mail($getEmail,$subject,$setpesan,$headers);
                    //$this->refresh();
                    $this->sendEmail($getEmail,"Password Reset Link",  $link);
                }                
                //echo $setpesan;
            }
        }  
                
        
        $this->render('forgotpassword', array('model'=>$model));
        

    }    
    
    public function sendEmail($to,$subject,$link)
    {
        $mailPath = Yii::getPathOfAlias('ext.yii-mail');
        require_once $mailPath.'/YiiMailMessage.php';
        
        $mail = new YiiMailMessage();
        $mail->setSubject($subject);
        $mail->view = "pwreset";
        //$contractor = $this->getContractorInfo();
        //$this->populate();
        $mail->setEncoder(Swift_Encoding::get8BitEncoding());
        $mail->setBody(array("link"=> $link), 'text/html');

        // UNCOMMENT THIS LINE TO PUT LIVE!
        //$recipients = explode(",", $contractor->Email);
        //foreach($recipients as $toEmail)
        //   $mail->addTo(trim($toEmail));
        
        $mail->addTo(trim($to));
        
        $mail->setFrom(array(Yii::app()->params['notificationsEmail'] => Yii::app()->params['notificationsEmailName']));
        
        return (Yii::app()->mail->send($mail) > 0);
    }
    
    
    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model=new LoginForm;

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login()) {
                $this->redirect(Yii::app()->user->returnUrl);
                //$this->redirect(array(Yii::app()->user->role->HomeUrl));
                //$this->redirect(array('site/index'));
            }
        }

        // display the login form
        $this->layout = "//layouts/loggedOut";
        $this->render('login', array('model'=>$model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        // audit logout
        $audit = new AuditLogin();
        $audit->LoginId = Yii::app()->user->loginId;
        $audit->UserName = Yii::app()->user->name;
        $audit->Action = 'logout';
        $audit->Browser = $_SERVER['HTTP_USER_AGENT'];
        $audit->RemoteIp = $_SERVER['REMOTE_ADDR'];
        $audit->DateCreated = new CDbExpression('NOW()');
        $audit->save();

        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }
}
?>
