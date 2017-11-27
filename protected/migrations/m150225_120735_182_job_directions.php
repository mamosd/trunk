<?php

class m150225_120735_182_job_directions extends CDbMigration
{
	public function up()
	{
            $this->createTable('polestar_job_directions',array(
                'Id' => 'pk',
                'JobId' => 'int',
                'Directions' => 'MEDIUMTEXT',
                'CreatedBy' => 'INT',
                'CreatedDate' => 'DATETIME',
            ));
            $this->createIndex('JobId','polestar_job_directions','JobId');
            
            $this->execute('ALTER TABLE `polestar_job`
                CHANGE COLUMN `Mileage` `Mileage` VARCHAR(50) NULL DEFAULT NULL AFTER `SpecialInstructions`;');
            
            $this->execute('ALTER TABLE `polestar_load`
                CHANGE COLUMN `Mileage` `Mileage` VARCHAR(50) NULL DEFAULT NULL AFTER `SpecialInstructions`;');
            
            $this->insert('setting', array (
                'Subsystem' => 'polestar',
                'Name'      => 'google-api-key',
                'Value'     => 'AIzaSyDFn4TW1Tu0SBR80O5K_4RIEgZNm_rFGY0'
            ));
	}

	public function down()
	{
            $this->dropTable('polestar_job_directions');
            
            $this->execute('ALTER TABLE `polestar_job`
                CHANGE COLUMN `Mileage` `Mileage` INT NULL DEFAULT NULL AFTER `SpecialInstructions`;');
            
            $this->execute('ALTER TABLE `polestar_load`
                CHANGE COLUMN `Mileage` `Mileage` INT NULL DEFAULT NULL AFTER `SpecialInstructions`;');
            
            $this->execute("delete from setting where Subsystem = 'polestar' AND Name = 'google-api-key'");
	}
}