<?php
/**
 * Description of AdminReportingPallet
 *
 * @author Ramon
 */
class AdminReportingPallet extends CFormModel
{
    public $reportType = 'mr';
    public $supplier;
    public $supplier1;
    public $deliveryPoint;
    public $from;
    public $to;

    public $details;
    public $printCentres;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('reportType, from, to', 'required'),
        );
    }

    public function populateReport() {
        if ($this->reportType == 'mr' || $this->reportType == 'pc') {
            $this->printCentres = array();
            $printCentres = PrintCentre::model()->findAll(array('order' => 'Name'));
            foreach($printCentres as $printCentre) {
                $criteria = new CDbCriteria();
                $criteria->condition = "PrintCentreId=:PrintCentreId and DeliveryDate BETWEEN :from and :to and PalletsCollected is not null and PalletsDelivered is not null";
                $criteria->params = array('PrintCentreId' => $printCentre->PrintCentreId, ':from' => $this->from, ':to' => $this->to);
                if($this->reportType == 'pc') {
                    $criteria->addCondition('SupplierId = :SupplierId');
                    $criteria->params['SupplierId'] = $this->supplier;
                }
                if(AllRouteInstanceDetails::model()->count($criteria) == 0) continue;
                //
                $criteria = new CDbCriteria();
                $criteria->select = 'DTDate, SupplierId, SupplierName, SUM(PalletsDelivered) AS PalletsDelivered, SUM(PalletsCollected) AS PalletsCollected ';
                $criteria->condition = "PrintCentreId=:PrintCentreId and DeliveryDate BETWEEN :from and :to and PalletsCollected is not null and PalletsDelivered is not null";
                $criteria->params = array('PrintCentreId' => $printCentre->PrintCentreId, ':from' => $this->from, ':to' => $this->to);
                if($this->reportType == 'pc') {
                    $criteria->addCondition('SupplierId = :SupplierId');
                    $criteria->params['SupplierId'] = $this->supplier;
                }
                else {
                    $criteria->group = 'SupplierId';
                }
                $criteria->order = 'DTDate, SupplierName';
                $printCentreDataProvider = new CActiveDataProvider('AllRouteInstanceDetails', array(
                    'criteria' => $criteria,
                    'pagination' => false,
                    ));
                $suppliers = AllRouteInstanceDetails::model()->findAll($criteria);
                //
                $suppliersOut = array();
                foreach($suppliers as $supplier) {
                    $criteria = new CDbCriteria();
                    $criteria->select = 'DeliveryPointId, DeliveryPointName, SUM(PalletsDelivered) as PalletsDelivered, SUM(PalletsCollected) as PalletsCollected ';
                    $criteria->condition = "PrintCentreId=:PrintCentreId and SupplierId = :SupplierId and DeliveryDate BETWEEN :from and :to and PalletsCollected is not null and PalletsDelivered is not null";
                    $criteria->params = array('PrintCentreId' => $printCentre->PrintCentreId, 'SupplierId' => $supplier->SupplierId, ':from' => $this->from, ':to' => $this->to);
                    $criteria->group = 'DeliveryPointId';
                    $criteria->order = 'DeliveryPointName';
                    $dataProvider1 = new CActiveDataProvider('AllRouteInstanceDetails', array(
                        'keyAttribute' => 'DeliveryPointId',
                        'criteria' => $criteria,
                        'pagination' => false,
                        ));
                    $suppliersOut[] = array('supplierName' => $supplier->SupplierName,
                        'supplierId' => $supplier->SupplierId,
                        'supplierDataProvider' => $dataProvider1);
                }
                //
                $criteria = new CDbCriteria();
                $criteria->select = 'SUM(PalletsDelivered) AS PalletsDelivered, SUM(PalletsCollected) AS PalletsCollected ';
                $criteria->condition = "PrintCentreId=:PrintCentreId and DeliveryDate BETWEEN :from and :to and PalletsCollected is not null and PalletsDelivered is not null";
                $criteria->params = array('PrintCentreId' => $printCentre->PrintCentreId, ':from' => $this->from, ':to' => $this->to);
                if($this->reportType == 'pc') {
                    $criteria->addCondition('SupplierId = :SupplierId');
                    $criteria->params['SupplierId'] = $this->supplier;
                }
                $totals = AllRouteInstanceDetails::model()->find($criteria);
                $this->printCentres[] = array('printCentreName' => $printCentre->Name,
                    'printCentreId' => $printCentre->PrintCentreId,
                    'printCentreDataProvider' => $printCentreDataProvider,
                    'suppliers' => $suppliersOut,
                    'totals' => $totals,
                );
//                echo $printCentre->Name . "<br>\n";
            }
        }
        else {
            $criteria = new CDbCriteria();
            if($this->reportType == 'pc' || $this->reportType == 'rt') {
                $criteria->condition = "SupplierId = :SupplierId and DeliveryDate BETWEEN :from and :to and PalletsCollected is not null and PalletsDelivered is not null";
                $criteria->params = array(':SupplierId' => $this->supplier, ':from' => $this->from, ':to' => $this->to);
            }
            if($this->reportType == 'dp') {
                $criteria->condition = "DeliveryPointId = :DeliveryPointId and DeliveryDate BETWEEN :from and :to and PalletsCollected is not null and PalletsDelivered is not null";
                $criteria->params = array(':DeliveryPointId' => $this->deliveryPoint, ':from' => $this->from, ':to' => $this->to);
            }
            if ($this->reportType == 'pc')
                $criteria->order = 'DTDate ASC, PrintCentreName ASC, PrintCentreId ASC';
            if ($this->reportType == 'rt')
                $criteria->order = 'DTDate ASC, RouteName ASC, RouteId ASC';
            if ($this->reportType == 'dp')
                $criteria->order = 'DTDate ASC, DeliveryPointName ASC, DeliveryPointId ASC';
            $this->details = AllRouteInstanceDetails::model()->findAll($criteria);
        }
    }

    public function afterValidate() {
        if(($this->reportType == 'pc' || $this->reportType == 'rt') && $this->supplier == "")
            $this->addError('supplier',  'Supplier is not specified');
        if($this->reportType == 'dp' && $this->deliveryPoint == "")
            $this->addError('deliveryPoint',  'Delivery Point is not specified');
    }

    public function attributeLabels() {
        return array(
            'reportType' => 'Report Type',
        );
    }
}
?>
