<?php

class SiteController extends Controller
{
    public $layout='//layouts/loggedOut';
    
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
            // redirects to user's home page based on its role
            if (!Yii::app()->user->isGuest)
                $this->redirect(array(Yii::app()->user->role->HomeUrl));
            else
                $this->redirect(Yii::app()->user->loginUrl);
	}


        /**
         *
         */
        public function actionSupplier()
        {
            // this is to be retrieved by implementation (hosting customer) when SaaS
           $this->menu = array(
               array('label'=>'Supplier', 'url'=>'#'),
           );

           $this->render('index');
        }

        /**
         *
         */
        public function actionClient()
        {
            // this is to be retrieved by implementation (hosting customer) when SaaS
           $this->menu = array(
               array('label'=>'Client', 'url'=>'#'),
           );

           $this->render('index');
        }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
                {
                    $this->layout = "//layouts/loggedOut";
                    $this->render('error', $error);
                }
	    }
	}

	/**
	 * Displays the contact page
	 *
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}
         *
         */
}