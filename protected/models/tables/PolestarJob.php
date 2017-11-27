<?php
/**
 * Description of PolestarJob
 *
 * @author ramon
 */
class PolestarJob extends PolestarJobLoadRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_job';
    }

    // attributes to be ignored while comparing
    public $ignoreKeyChanges = array(
        'EditedBy',
        'EditedDate'
    );

    public function behaviors() {
        return array( 'CompareBehavior'); // <-- and other behaviors your model may have
    }

    public function relations() {
        return array(
            'Comments'      => array( self::HAS_MANY, 'PolestarJobComment', 'JobId', 'alias' => 'cmm', 'order' => 'cmm.Id DESC'),
            'PrintCentre'   => array( self::BELONGS_TO, 'PolestarPrintCentre', 'PrintCentreId' ),
            'Provider'      => array( self::BELONGS_TO, 'PolestarSupplier', 'ProviderId'),
            'Supplier'      => array( self::BELONGS_TO, 'PolestarSupplier', 'SupplierId'),
            'Vehicle'       => array( self::BELONGS_TO, 'PolestarVehicle', 'VehicleId'),
            'Status'        => array( self::BELONGS_TO, 'PolestarStatus', 'StatusId'),
            'Loads'         => array( self::HAS_MANY, 'PolestarLoad', 'JobId', 'alias' => 'lds',  'order' => 'lds.Sequence ASC'),
            'CollectionPoints'  => array( self::HAS_MANY, 'PolestarJobCollectionPoint', 'JobId', 'alias' => 'cps',  'order' => 'cps.Sequence ASC'),
            'EditedByLogin'     => array( self::BELONGS_TO, 'Login', 'EditedBy'),
            'CreatedByLogin'    => array( self::BELONGS_TO, 'Login', 'CreatedBy'),
        );
    }

    public static function getValidReference($printCentreId, $deliveryDate) {
        $pc = PolestarPrintCentre::model()->findByPk($printCentreId);
        $dt = DateTime::createFromFormat('d/m/Y', $deliveryDate);
        $prefix = $pc->JobPrefix.'/' . $dt->format('d.m.y').'/';
        $counter = 0;
        do {
            $counter++;
            $ref = sprintf('%s%03d',$prefix,$counter);
            $crit = new CDbCriteria();
            $crit->addColumnCondition(array( 'Ref' => $ref ));
            $refExists = PolestarJob::model()->exists($crit);
        } while ($refExists);
        return $ref;
    }
    
    public function isAdviceSheetSent(){
        return PolestarSupplierNotification::model()->exists("JobId = :jid AND Type = 'advice'", array(':jid' => $this->Id));
    }
    
    public function getPermalink($includeHash = TRUE) {
        $dtp = new DateTime($this->DeliveryDate);
        $dtp = $dtp->modify('-1 day');
        $dtp = $dtp->format('d/m/Y');
        $url = Yii::app()->createUrl('polestar/routeview', array(
                    'PolestarRouteViewForm[planningDate]' => $dtp, 
                    'PolestarRouteViewForm[printCentreId]' => $this->PrintCentreId
                ));
        if ($includeHash)
            $url .= "#job-{$this->Id}";
        
        return $url;
    }
    
    public function getPreviousStatus() {
        $curStatusId = $this->StatusId;
        $revisions = PolestarJobHistory::model()->findAll(array(
            'condition' => 'Id = :jid',
            'params' => array(':jid' => $this->Id),
            'order' => 'RevisionNo DESC'
        ));
        
        foreach ($revisions as $rev) {
            if ($rev->StatusId !== $curStatusId)
                return $rev->StatusId;
        }
        return $curStatusId;
    }
}
