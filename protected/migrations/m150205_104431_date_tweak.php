<?php

class m150205_104431_date_tweak extends CDbMigration
{
	public function up()
	{
            $this->AddColumn('polestar_load', 'DeliveryDate', 'date');
            $this->AddColumn('polestar_load_history', 'DeliveryDate', 'date');
	}

	public function down()
	{
            $this->DropColumn('polestar_load', 'DeliveryDate');
            $this->DropColumn('polestar_load_history', 'DeliveryDate');
	}
}