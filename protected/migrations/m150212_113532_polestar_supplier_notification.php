<?php

class m150212_113532_polestar_supplier_notification extends CDbMigration
{
	public function up()
	{
            $this->createTable('polestar_supplier_notification',array(
                'Id' => 'pk',
                'ContactId' => 'int',
                'JobId' => 'int',
                'Type' => "varchar(25) not null default 'advice'",
                'SentBy' => 'INT',
                'SentDate' => 'DATETIME',
            ));
            $this->createIndex('polestar_supplier_notification_contact_job','polestar_supplier_notification','ContactId, JobId');
	}

	public function down()
	{
            $this->dropTable('polestar_supplier_notification');
	}
}