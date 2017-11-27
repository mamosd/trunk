<?php

class m150115_095254_polestar_jobs extends CDbMigration
{
	public function up()
	{
            $this->createTable ( 'polestar_printcentre', array (
                'Id'                => 'pk',
                'Name'              => 'varchar(50) not null',
                'JobPrefix'         => 'varchar(5) not null',
                'Live'              => 'int(1) not null default 1',
                'Address1'          => 'varchar(50)',
                'Address2'          => 'varchar(50)',
                'Address3'          => 'varchar(50)',
                'Address4'          => 'varchar(50)',
                'Postcode'          => 'varchar(50)',
                'LateAdviseCutoff'  => 'time',
                'CreatedBy'         => 'int not null',
                'CreatedDate'       => 'datetime not null',
                'EditedBy'          => 'int',
                'EditedDate'        => 'datetime',
            ));
            
            $this->insert ( 'polestar_printcentre', array (
                'Name'              => 'Bicester',
                'JobPrefix'         => 'BIC',
                'Address1'          => 'Polestar',
                'Address2'          => 'Chaucer Business Park',
                'Address3'          => 'Launton Road',
                'Address4'          => 'Bicester, Oxon',
                'Postcode'          => 'OX26 4QZ',
                'LateAdviseCutoff'  => '14:00',
                'CreatedBy'         => '1',
                'CreatedDate'       => new CDbExpression('now()')
            ));
            
            $this->insert ( 'polestar_printcentre', array (
                'Name'              => 'Leeds',
                'JobPrefix'         => 'LDS',
                'Address1'          => 'Polestar UK Print Ltd - Petty',
                'Address2'          => 'Whitehall Road',
                'Address3'          => 'Leeds',
                'Address4'          => '',
                'Postcode'          => 'LS12 1BD',
                'LateAdviseCutoff'  => '13:00',
                'CreatedBy'         => '1',
                'CreatedDate'       => new CDbExpression('now()')
            ));
            
            $this->insert ( 'polestar_printcentre', array (
                'Name'              => 'Polestar Stones',
                'JobPrefix'         => 'PST',
                'Address1'          => 'Unit 10',
                'Address2'          => 'Wates Way',
                'Address3'          => 'Banbury',
                'Address4'          => 'Oxon',
                'Postcode'          => 'OX16 3ES',
                'LateAdviseCutoff'  => '13:00',
                'CreatedBy'         => '1',
                'CreatedDate'       => new CDbExpression('now()')
            ));
            
            $this->insert ( 'polestar_printcentre', array (
                'Name'              => 'Sheffield',
                'JobPrefix'         => 'SHF',
                'Address1'          => 'Polestar Sheffield Ltd',
                'Address2'          => 'Shepcote Lane',
                'Address3'          => 'Tinsley',
                'Address4'          => 'South Yorkshire',
                'Postcode'          => 'S9 1RF',
                'LateAdviseCutoff'  => '13:00',
                'CreatedBy'         => '1',
                'CreatedDate'       => new CDbExpression('now()')
            ));
            
            $this->insert ( 'polestar_printcentre', array (
                'Name'              => 'Wakefield',
                'JobPrefix'         => 'WKF',
                'Address1'          => 'Polestar Chantry',
                'Address2'          => 'Wakefield 41',
                'Address3'          => 'West Yorkshire',
                'Address4'          => '',
                'Postcode'          => 'WF2 0XQ',
                'LateAdviseCutoff'  => '13:00',
                'CreatedBy'         => '1',
                'CreatedDate'       => new CDbExpression('now()')
            ));
            
            $this->insert ( 'polestar_printcentre', array (
                'Name'              => 'Web Offset Sheffield',
                'JobPrefix'         => 'SHW',
                'Address1'          => 'Polestar Sheffield Ltd',
                'Address2'          => 'Shepcote Lane',
                'Address3'          => 'Tinsley',
                'Address4'          => 'South Yorkshire',
                'Postcode'          => 'S9 1RF',
                'LateAdviseCutoff'  => '13:00',
                'CreatedBy'         => '1',
                'CreatedDate'       => new CDbExpression('now()')
            ));
            
            $this->createTable ( 'polestar_vehicle', array (
                'Id'                => 'pk',
                'Name'              => 'varchar(50) not null',
            ));
            
            $this->createTable ( 'polestar_status', array (
                'Id'                => 'varchar(2) not null',
                'Name'              => 'varchar(50) not null',
                'Code'              => 'varchar(50) not null',
                'PRIMARY KEY(Id)'
            ));
            
            $this->insert ( 'polestar_status', array (
                'Id'    => 'NA',
                'Name'  => 'Newly Added',
                'Code'  => 'newly-added'
            ));
            
            $this->insert ( 'polestar_status', array (
                'Id'    => 'B',
                'Name'  => 'Booked',
                'Code'  => 'booked'
            ));
            
            $this->insert ( 'polestar_status', array (
                'Id'    => 'A',
                'Name'  => 'Amended',
                'Code'  => 'amended'
            ));
            
            $this->insert ( 'polestar_status', array (
                'Id'    => 'CA',
                'Name'  => 'Cancelled',
                'Code'  => 'cancelled'
            ));
            
            $this->insert ( 'polestar_status', array (
                'Id'    => 'SD',
                'Name'  => 'Same Day Advice',
                'Code'  => 'same-day'
            ));
            
            $this->insert ( 'polestar_status', array (
                'Id'    => 'LA',
                'Name'  => 'Late Advice',
                'Code'  => 'late-advice'
            ));
            
            $this->insert ( 'polestar_status', array (
                'Id'    => 'CO',
                'Name'  => 'Confirmed',
                'Code'  => 'confirmed'
            ));
            
            $this->insert ( 'polestar_status', array (
                'Id'    => 'DC',
                'Name'  => 'Data Completed',
                'Code'  => 'data-completed'
            ));
            
            $this->createTable ( 'polestar_job', array (
                'Id'                    => 'pk',
                'Ref'                   => 'varchar(50) not null',
                'PrintCentreId'         => 'int',
                'CreationDate'          => 'date not null',
                'DeliveryDate'          => 'date not null',
                'JobTypeId'             => 'int',
                'VehicleId'             => 'int',
                'CollAddress'           => 'varchar(250)',
                'CollPostcode'          => 'varchar(50)',
                'CollCompany'           => 'varchar(50)',
                'CollScheduledTime'     => 'time',
                'CollArrivalTime'       => 'time',
                'CollDepartureTime'     => 'time',
                'SpecialInstructions'   => 'varchar(250)',
                'Mileage'               => 'int',
                'SupplierId'            => 'int',
                'DriveName'             => 'varchar(50)',
                'VehicleRegNo'          => 'varchar(50)',
                'ContactNo'             => 'varchar(50)',
                
                'StatusId'              => 'varchar(2)',
                'RevisionNo'            => 'int not null default 1',
                
                'CreatedBy'         => 'int not null',
                'CreatedDate'       => 'datetime not null',
                'EditedBy'          => 'int',
                'EditedDate'        => 'datetime',
            ));
            
            $this->createTable ( 'polestar_load', array (
                'Id'                    => 'pk',
                'JobId'                 => 'int not null',
                'Ref'                   => 'varchar(50) not null',
                'JobTypeId'             => 'int',
                'Publication'           => 'varchar(250)',
                'Quantity'              => 'int',
                'PalletsTotal'          => 'int',
                'PalletsFull'           => 'int',
                'PalletsHalf'           => 'int',
                'PalletsQtr'            => 'int',
                'Kg'                    => 'int',
                
                'DelAddress'            => 'varchar(250)',
                'DelPostcode'           => 'varchar(50)',
                'DelCompany'            => 'varchar(50)',
                'DelScheduledTime'      => 'time',
                'DelTimeCode'           => 'varchar(50)',
                'DelArrivalTime'        => 'time',
                'DelDepartureTime'      => 'time',
                'BookingRef'            => 'varchar(50)',
                'SpecialInstructions'   => 'varchar(250)',
                'Mileage'               => 'int',
                
                'StatusId'              => 'varchar(2)',
                'RevisionNo'            => 'int not null default 1',
                
                'CreatedBy'         => 'int not null',
                'CreatedDate'       => 'datetime not null',
                'EditedBy'          => 'int',
                'EditedDate'        => 'datetime',
            ));
            
            $this->createTable ( 'polestar_load_comment', array (
                'Id'                => 'pk',
                'LoadId'            => 'int',
                'Comment'           => 'varchar(250) not null',
                'CreatedBy'         => 'int not null',
                'CreatedDate'       => 'datetime not null',
            ));
            
            $this->createTable ( 'polestar_job_comment', array (
                'Id'                => 'pk',
                'JobId'             => 'int',
                'Comment'           => 'varchar(250) not null',
                'CreatedBy'         => 'int not null',
                'CreatedDate'       => 'datetime not null',
            ));
	}

	public function down()
	{
            $this->dropTable ( 'polestar_printcentre' );
            $this->dropTable ( 'polestar_vehicle' );
            $this->dropTable ( 'polestar_status' );
            $this->dropTable ( 'polestar_job' );
            $this->dropTable ( 'polestar_load' );
            $this->dropTable ( 'polestar_job_comment' );
            $this->dropTable ( 'polestar_load_comment' );
	}
}