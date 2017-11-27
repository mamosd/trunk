<?php

class m150309_101738_181_job_collection_point extends CDbMigration
{
	public function up()
	{
            $this->createTable('polestar_job_collection_point',array(
                'Id' => 'pk',
                'JobId' => 'int',
                'Sequence' => 'int default 1',
                'CollAddress' => 'varchar(250)',
                'CollPostcode' => 'varchar(50)',
                'CollCompany' => 'varchar(50)',
                'CollScheduledTime' => 'time',
                'CollArrivalTime' => 'time',
                'CollDepartureTime' => 'time',
                'SpecialInstructions' => 'varchar(250)',
                'Mileage' => 'varchar(50)',
                'CreatedBy' => 'INT',
                'CreatedDate' => 'DATETIME'
            ));
            $this->createIndex('JobId','polestar_job_collection_point','JobId');
            
            $this->AddColumn('polestar_job', 'TotalMileage', 'varchar(50)');
            $this->AddColumn('polestar_job', 'CollectionSequence', 'varchar(100)');
            
            $this->AddColumn('polestar_job_history', 'TotalMileage', 'varchar(50)');
            $this->AddColumn('polestar_job_history', 'CollectionSequence', 'varchar(100)');
            
            $this->execute("update polestar_job 
                                set TotalMileage = Mileage,
                                    CollectionSequence = CollPostcode;");
            
            $this->execute("update polestar_job_history 
                                set TotalMileage = Mileage,
                                    CollectionSequence = CollPostcode;");
            
            $this->execute("update polestar_job 
                                set Mileage = '0.00'
                            where Mileage is not null;");
            
            $this->execute("update polestar_job_history
                                set Mileage = '0.00'
                            where Mileage is not null;");
            
            $this->AddColumn('polestar_load', 'CollectionSequence', 'varchar(100)');
            $this->AddColumn('polestar_load_history', 'CollectionSequence', 'varchar(100)');
	}

	public function down()
	{
            $this->dropTable('polestar_job_collection_point');
            
            $this->execute("update polestar_job 
                                set Mileage = TotalMileage;");
            
            $this->execute("update polestar_job_history 
                                set Mileage = TotalMileage;");
            
            $this->DropColumn('polestar_job', 'TotalMileage');
            $this->DropColumn('polestar_job', 'CollectionSequence');
            
            $this->DropColumn('polestar_job_history', 'TotalMileage');
            $this->DropColumn('polestar_job_history', 'CollectionSequence');
            
            $this->DropColumn('polestar_load', 'CollectionSequence');
            $this->DropColumn('polestar_load_history', 'CollectionSequence');
	}
}