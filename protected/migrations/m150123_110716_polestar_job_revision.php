<?php

class m150123_110716_polestar_job_revision extends CDbMigration
{
	public function up()
	{
            $this->addColumn('polestar_job', 'OriginalOrderNo', 'varchar(20)');
            $this->addColumn('polestar_job', 'RevisionChanges', 'text');
            $this->addColumn('polestar_load', 'RevisionChanges', 'text');
            
            $this->createTable ( 'polestar_job_history', array (
                'Id'                    => 'int',
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
                
                'OriginalOrderNo'   => 'varchar(20)',
                'RevisionChanges'   => 'text',
                
                'PRIMARY KEY(Id, RevisionNo)'
            ));
            
            $this->createTable ( 'polestar_load_history', array (
                'Id'                    => 'int',
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
                
                'RevisionChanges'   => 'text',
                
                'PRIMARY KEY(Id, RevisionNo)'
            ));
	}

	public function down()
	{
            $this->dropColumn('polestar_job', 'OriginalOrderNo');
            $this->dropColumn('polestar_job', 'RevisionChanges');
            $this->dropColumn('polestar_load', 'RevisionChanges');
            
            $this->dropTable('polestar_job_history');
            $this->dropTable('polestar_load_history');
	}
        
        
}