<?php

class m141125_105610_128_invoice_optimization extends CDbMigration
{
	public function up()
	{
            $this->execute("ALTER TABLE `finance_comment`
                            ADD INDEX `RouteInstanceId_OutputOnInvoice` (`RouteInstanceId`, `OutputOnInvoice`);");
            $this->execute("ALTER TABLE `finance_invoice`
                            ADD INDEX `ContractorId_WeekStarting` (`ContractorId`, `WeekStarting`);");
	}

	public function down()
	{
            echo "m141125_105610_128_invoice_optimization does not support migration down.\n";
            return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}