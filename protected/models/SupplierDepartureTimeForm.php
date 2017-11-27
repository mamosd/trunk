<?php
/**
 * Description of SupplierDepartureTimeForm
 *
 * @author Ramon
 */
class SupplierDepartureTimeForm extends CFormModel
{
    public $routeInstanceId;
    public $routeName;
    public $date;
    public $departureTime;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('departureTime', 'required'),
        );
    }

    public function populate($id)
    {
        $route = AllRouteInstanceSuppliers::model()->find('RouteInstanceId=:rid', array(':rid' => $id));
        if(isset($route))
        {
            $this->routeInstanceId = $id;
            $this->date = $route->Date;
            $this->routeName = $route->RouteName;
            $this->departureTime = $route->DepartureTime;
        }
    }

    public function save()
    {
        $route = RouteInstance::model()->findByPk($this->routeInstanceId);
        if (isset($route))
        {
            $route->DepartureTime = $this->departureTime;
            return $route->save();
        }
        return false;
    }
}
?>
