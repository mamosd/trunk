<?php

require_once( dirname(__FILE__) . '/../components/helpers.php');

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
        'homeUrl'=>array('account/login'),
	'name'=>'Aktrion Logistics',
        
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
                'application.models.tables.*' ,
                'application.models.processors.*' ,
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
		),
		*/
	),

	// application components
	'components'=>array(
            'user'=>array(
                // enable cookie-based authentication
                'allowAutoLogin'=>true,
                'loginUrl'=>array('account/login'),
            ),

		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
            /*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
             *
             */
		// uncomment the following to use a MySQL database
/*                'cache' => array(
                    'class' => 'CDbCache',
                    'connectionID' => 'db',
                ),
 * 
 */
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=aktrion',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'r00t',
			'charset' => 'utf8',
//                        'schemaCachingDuration'=>3600,
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
            
            'mail' => array(
                   'class' => 'ext.yii-mail.YiiMail',

                   /*'transportType' => 'smtp',
                   'transportOptions'=>array(
                          'host'=>'smtp.sendgrid.net',
                          'username'=>'pantherparcels',
                          'password'=>'pg9002a',
                          'port'=>25,
                    ),*/

                    'viewPath' => 'application.views.mail',
                    'logging' => true,
                    'dryRun' => false
                 ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
            
            'notificationsEmailName' => 'Aktrion Finance',
            'notificationsEmail' => 'no-reply@logistics.aktrion.com',
            'dtrAdmins' => 'jocrump,admin',  // can be comma separated
            'dtrCanAck' => array(
                "admin",
                "psutcliffe",
                "jfulton",
                "ekeegan",
                "cratcliffe",
                "duttley",
                "dsquibb",
                "pcurtis",
                "glarkin",
                "dspratt",
                "knjaka",
                "pcurtis2",
                "pmahoney",
                "dthomas",
                "sgudek",
                "donnah",
                "shornsey",
                "msimmons",
                "nkhan",
                "jvojnovic",
                "alex"
            )
	),
);