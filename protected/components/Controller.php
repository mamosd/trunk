<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/loggedIn';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

    public function __construct($id,$module=null)
    {
        parent::__construct($id, $module);
        $this->menu = MenuManager::get($id);
    }
        
    /**
     * defined to set all controllers to use the accessControl filter
     * @return array
     */
    public function filters()
    {
        return array(
          'accessControl','permissionCheck'
        );
    }

    /**
     *
     * @return array
     */
    public function  accessRules() {
        return array(
            array('allow',
                'users' => $this->getValidUsers()),
            array('deny',
                'users' => array('*'))
        );
    }

    /**
     * to be overriden by custom controllers
     * @return <type>
     */
    public function getValidUsers() {
        return array('*');
    }
    
    public function  getActionPermissions(){
        return array();
    }

    public function filterPermissionCheck($filterChain) {
        $action = $filterChain->action->id;
        $permissions = $this->getActionPermissions();
        if (empty($permissions)) {
            $filterChain->run();
            return;
        } else {
            if (!isset($permissions[$action]) || empty($permissions[$action])) {
                $filterChain->run();
                return;
            }
            if (Login::checkPermission($permissions[$action])) {
                $filterChain->run();
                return;
            }
        }
        if (YII_DEBUG) {
            error_log("Unauthorized action: ".$action);
            error_log("Requested perms: ".var_export($permissions,true));
            error_log("User perms:".var_export(Yii::app()->user->permissions,true));
        }
        throw new CHttpException ( 403, 'Not allowed' );
    }
}