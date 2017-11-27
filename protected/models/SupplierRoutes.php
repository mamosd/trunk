<?php
/**
 * Description of SupplierRoutes
 *
 * @author Ramon
 */
class SupplierRoutes extends CFormModel
{
    public $status;
    public $routes;

    public function populate($supplierLoginId, $status = null)
    {
        if ($status == NULL)
            $status = RouteInstance::STATUS_ACTIVE;

        $loginSupplier = SupplierLogin::model()->find('LoginId=:lid', array(':lid'=>$supplierLoginId));
        $routes = null;

        if (isset($loginSupplier))
        {
            $supplierId = $loginSupplier->SupplierId;

            $criteria = new CDbCriteria();
            $criteria->condition = 'SupplierId=:sid';
            $criteria->params = array('sid'=>$supplierId);

            if ($status != "*")
            {
                $criteria->condition .= ' and Status=:stt';
                $criteria->params[':stt'] = $status;
            }

            $criteria->order = "DTDate DESC";
            
            $routes = AllRouteInstanceSuppliers::model()->findAll($criteria);
        }
        $this->routes = $routes;
        $this->status = $status;
    }

    
}
?>
