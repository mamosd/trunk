<?php
class m150127_222133_153_suppliers_Receive_Advice_Emails extends CDbMigration {

    public function up() {
        $this->addColumn('polestar_supplier_contact','ReceiveAdviceEmails','INT(1)');
    }

    public function down() {
        $this->dropColumn('polestar_supplier_contact','ReceiveAdviceEmails','INT(1)');
    }

}