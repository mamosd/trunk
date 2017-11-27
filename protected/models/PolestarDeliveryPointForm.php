<?php
/**
 * Description of PolestarDeliveryPointForm
 *
 * @author aldroid
 */
class PolestarDeliveryPointForm  extends CFormModel
{
    public $deliveryPointId;
    public $accountNumber;
    public $name;
    public $address;
    public $postalCode;
    public $telephoneNumber;
    public $county;
    public $deliveryComments;
    public $area;
    public $company;
    public $isNQPrimary;
    public $isNQSecondary;

    //NPA Cutoff times
    public $NPAMon;
    public $NPATue;
    public $NPAWed;
    public $NPAThu;
    public $NPAFri;
    public $NPASat;
    public $NPASun;

    //Opening Start times
    public $OpeningStartMon;
    public $OpeningStartTue;
    public $OpeningStartWed;
    public $OpeningStartThu;
    public $OpeningStartFri;
    public $OpeningStartSat;
    public $OpeningStartSun;
    //Opening End times
    public $OpeningEndMon;
    public $OpeningEndTue;
    public $OpeningEndWed;
    public $OpeningEndThu;
    public $OpeningEndFri;
    public $OpeningEndSat;
    public $OpeningEndSun;

    //Closing Start times
    public $ClosingStartMon;
    public $ClosingStartTue;
    public $ClosingStartWed;
    public $ClosingStartThu;
    public $ClosingStartFri;
    public $ClosingStartSat;
    public $ClosingStartSun;
    //Closing End times
    public $ClosingEndMon;
    public $ClosingEndTue;
    public $ClosingEndWed;
    public $ClosingEndThu;
    public $ClosingEndFri;
    public $ClosingEndSat;
    public $ClosingEndSun;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('address, postalCode', 'required'),
        );
    }

    public function attributeLabels()
    {
            return array(
                    'telephoneNumber' => 'Main Telephone Number',
            );
    }

    public function populate($id = NULL)
    {
        if(isset($id)){
            $dp = PolestarDeliveryPoint::model()->findByPk($id);
            if (isset($dp)) {
                $this->deliveryPointId = $dp->DeliveryPointId;
                $this->accountNumber = $dp->AccountNumber;
                $this->name = $dp->Name;
                $this->address = $dp->Address;
                $this->postalCode = $dp->PostalCode;
                $this->telephoneNumber = $dp->TelephoneNumber;
                $this->county = $dp->County;
                $this->deliveryComments = $dp->DeliveryComments;
                $this->area = $dp->Area;
                $this->company = $dp->Company;

                //populate NPA value
                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='NPA' and day='Mon'");
                if ($dpTime)
                {$this->NPAMon = $dpTime->StartTime;}

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='NPA' and day='Tue'");
                if ($dpTime)
                {$this->NPATue = $dpTime->StartTime;}

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='NPA' and day='Wed'");
                if ($dpTime)
                {$this->NPAWed = $dpTime->StartTime;}

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='NPA' and day='Thu'");
                if ($dpTime)
                {$this->NPAThu = $dpTime->StartTime;}

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='NPA' and day='Fri'");
                if ($dpTime)
                {$this->NPAFri = $dpTime->StartTime;}

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='NPA' and day='Sat'");
                if ($dpTime)
                {$this->NPASat = $dpTime->StartTime;}

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='NPA' and day='Sun'");
                if ($dpTime)
                {$this->NPASun = $dpTime->StartTime;}



                //populate Opening Hours value
                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='OpeningHours' and day='Mon'");

                if ($dpTime)
                {
                    $this->OpeningStartMon = $dpTime->StartTime;
                    $this->OpeningEndMon = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='OpeningHours' and day='Tue'");
                if ($dpTime)
                {
                    $this->OpeningStartTue = $dpTime->StartTime;
                    $this->OpeningEndTue = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='OpeningHours' and day='Wed'");
                if ($dpTime)
                {
                    $this->OpeningStartWed = $dpTime->StartTime;
                    $this->OpeningEndWed = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='OpeningHours' and day='Thu'");
                if ($dpTime)
                {
                    $this->OpeningStartThu = $dpTime->StartTime;
                    $this->OpeningEndThu = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='OpeningHours' and day='Fri'");
                if ($dpTime)
                {
                    $this->OpeningStartFri = $dpTime->StartTime;
                    $this->OpeningEndFri = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='OpeningHours' and day='Sat'");
                if ($dpTime)
                {
                    $this->OpeningStartSat = $dpTime->StartTime;
                    $this->OpeningEndSat = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='OpeningHours' and day='Sun'");
                if ($dpTime)
                {
                    $this->OpeningStartSun = $dpTime->StartTime;
                    $this->OpeningEndSun = $dpTime->EndTime;
                }



                //populate Closing Hours value
                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='ClosingHours' and day='Mon'");

                if ($dpTime)
                {
                    $this->ClosingStartMon = $dpTime->StartTime;
                    $this->ClosingEndMon = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='ClosingHours' and day='Tue'");
                if ($dpTime)
                {
                    $this->ClosingStartTue = $dpTime->StartTime;
                    $this->ClosingEndTue = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='ClosingHours' and day='Wed'");
                if ($dpTime)
                {
                    $this->ClosingStartWed = $dpTime->StartTime;
                    $this->ClosingEndWed = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='ClosingHours' and day='Thu'");
                if ($dpTime)
                {
                    $this->ClosingStartThu = $dpTime->StartTime;
                    $this->ClosingEndThu = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='ClosingHours' and day='Fri'");
                if ($dpTime)
                {
                    $this->ClosingStartFri = $dpTime->StartTime;
                    $this->ClosingEndFri = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='ClosingHours' and day='Sat'");
                if ($dpTime)
                {
                    $this->ClosingStartSat = $dpTime->StartTime;
                    $this->ClosingEndSat = $dpTime->EndTime;
                }

                $dpTime = PolestarDeliveryPointTime::model()->find("DeliveryPointId=$id and type='ClosingHours' and day='Sun'");
                if ($dpTime)
                {
                    $this->ClosingStartSun = $dpTime->StartTime;
                    $this->ClosingEndSun = $dpTime->EndTime;
                }


            }
        }
    }

    public function save()
    {
        $dp = ($this->deliveryPointId !== '') ? PolestarDeliveryPoint::model()->findByPk($this->deliveryPointId) : new PolestarDeliveryPoint();
        if ($dp->isNewRecord) {
            $dp->DateCreated = new CDbExpression('NOW()');
        }
        $dp->AccountNumber = $this->accountNumber;
        $dp->Name = $this->name;
        $dp->Address = $this->address;
        $dp->PostalCode = $this->postalCode;
        $dp->TelephoneNumber = $this->telephoneNumber;
        $dp->County = $this->county;
        $dp->DeliveryComments = $this->deliveryComments;
        $dp->Area = $this->area;
        $dp->Company = $this->company;
        $dp->DateUpdated = new CDbExpression('NOW()');
        $dp->UpdatedBy = Yii::app()->user->name;

        if($dp->save())
        {
            $deliveryPointId=Yii::app()->db->getLastInsertID();

            if ( empty ( $deliveryPointId ) )
            {
                $deliveryPointId = $this->deliveryPointId;
            }

            Yii::app()->session['getLastIdPolestarDeliveryPoint'] = Yii::app()->db->getLastInsertID();

            $command = Yii::app()->db->createCommand();
            $command->attachBehavior('InsertUpdateCommandBehavior', new InsertUpdateCommandBehavior);

            //Insert or update NPA
            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Mon', 'StartTime'=> $this->NPAMon),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Mon', 'StartTime'=> $this->NPAMon)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Tue', 'StartTime'=> $this->NPATue),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Tue', 'StartTime'=> $this->NPATue)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Wed', 'StartTime'=> $this->NPAWed),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Wed', 'StartTime'=> $this->NPAWed)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Thu', 'StartTime'=> $this->NPAThu),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Thu', 'StartTime'=> $this->NPAThu)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Fri', 'StartTime'=> $this->NPAFri),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Fri', 'StartTime'=> $this->NPAFri)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Sat', 'StartTime'=> $this->NPASat),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Sat', 'StartTime'=> $this->NPASat)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Sun', 'StartTime'=> $this->NPASun),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'NPA', 'day' => 'Sun', 'StartTime'=> $this->NPASun)
            );




            //Insert or update Opening Hours
            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Mon', 'StartTime'=> $this->OpeningStartMon, 'EndTime'=> $this->OpeningEndMon),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Mon', 'StartTime'=> $this->OpeningStartMon, 'EndTime'=> $this->OpeningEndMon)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Tue', 'StartTime'=> $this->OpeningStartTue, 'EndTime'=> $this->OpeningEndTue),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Tue', 'StartTime'=> $this->OpeningStartTue, 'EndTime'=> $this->OpeningEndTue)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Wed', 'StartTime'=> $this->OpeningStartWed, 'EndTime'=> $this->OpeningEndWed),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Wed', 'StartTime'=> $this->OpeningStartWed, 'EndTime'=> $this->OpeningEndWed)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Thu', 'StartTime'=> $this->OpeningStartThu, 'EndTime'=> $this->OpeningEndThu),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Thu', 'StartTime'=> $this->OpeningStartThu, 'EndTime'=> $this->OpeningEndThu)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Fri', 'StartTime'=> $this->OpeningStartFri, 'EndTime'=> $this->OpeningEndFri),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Fri', 'StartTime'=> $this->OpeningStartFri, 'EndTime'=> $this->OpeningEndFri)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Sat', 'StartTime'=> $this->OpeningStartSat, 'EndTime'=> $this->OpeningEndSat),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Sat', 'StartTime'=> $this->OpeningStartSat, 'EndTime'=> $this->OpeningEndSat)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Sun', 'StartTime'=> $this->OpeningStartSun, 'EndTime'=> $this->OpeningEndSun),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'OpeningHours', 'day' => 'Sun', 'StartTime'=> $this->OpeningStartSun, 'EndTime'=> $this->OpeningEndSun)
            );




            //Insert or update Closing Hours
            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Mon', 'StartTime'=> $this->ClosingStartMon, 'EndTime'=> $this->ClosingEndMon),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Mon', 'StartTime'=> $this->ClosingStartMon, 'EndTime'=> $this->ClosingEndMon)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Tue', 'StartTime'=> $this->ClosingStartTue, 'EndTime'=> $this->ClosingEndTue),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Tue', 'StartTime'=> $this->ClosingStartTue, 'EndTime'=> $this->ClosingEndTue)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Wed', 'StartTime'=> $this->ClosingStartWed, 'EndTime'=> $this->ClosingEndWed),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Wed', 'StartTime'=> $this->ClosingStartWed, 'EndTime'=> $this->ClosingEndWed)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Thu', 'StartTime'=> $this->ClosingStartThu, 'EndTime'=> $this->ClosingEndThu),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Thu', 'StartTime'=> $this->ClosingStartThu, 'EndTime'=> $this->ClosingEndThu)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Fri', 'StartTime'=> $this->ClosingStartFri, 'EndTime'=> $this->ClosingEndFri),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Fri', 'StartTime'=> $this->ClosingStartFri, 'EndTime'=> $this->ClosingEndFri)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Sat', 'StartTime'=> $this->ClosingStartSat, 'EndTime'=> $this->ClosingEndSat),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Sat', 'StartTime'=> $this->ClosingStartSat, 'EndTime'=> $this->ClosingEndSat)
            );

            $command->insertUpdate('polestar_deliverypoint_time',
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Sun', 'StartTime'=> $this->ClosingStartSun, 'EndTime'=> $this->ClosingEndSun),
              array('DeliveryPointId' => $deliveryPointId, 'type' => 'ClosingHours', 'day' => 'Sun', 'StartTime'=> $this->ClosingStartSun, 'EndTime'=> $this->ClosingEndSun)
            );



            return true;
        }

        return false;
        //return $dp->save();
    }
    
    public static function ensureExists($postcode, $address, $company, $update = FALSE) {
        $existing = PolestarDeliveryPoint::model()->find(array(
            'condition' => 'PostalCode = :pc',
            'params' => array(':pc' => $postcode)
        ));
        if (!isset($existing)) { // create new postcode
            $new = new PolestarDeliveryPoint();
            $new->DateCreated = new CDbExpression('NOW()');
            $new->PostalCode = $postcode;
            $new->Address = $address;
            $new->Company = $company;
            $new->save();
        }
        else if ($update) { // update existing if required
            $existing->Address = $address;
            $existing->Company = $company;
            $existing->save();
        }
    }
}
?>
