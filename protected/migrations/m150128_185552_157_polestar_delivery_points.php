<?php
class m150128_185552_157_polestar_delivery_points extends CDbMigration {

    public function up() {
        $this->createTable('polestar_deliverypoint', array (
            'DeliveryPointId'   => 'pk',
            'AccountNumber'     => 'varchar(45)',
            'Name'              => 'varchar(45)',
            'Address'           => 'varchar(255)',
            'PostalCode'        => 'varchar(45)',
            'TelephoneNumber'   => 'varchar(45)',
            'County'            => 'varchar(45)',
            'DeliveryComments'  => 'varchar(255)',
            'NQ'                => 'varchar(20)',
            'DateCreated'       => 'datetime',
            'DateUpdated'       => 'datetime',
            'UpdatedBy'         => 'varchar(45)',
        ));

        $this->createTable('polestar_deliverypoint_contact', array(
            'ContactId'         => 'pk',
            'DeliveryPointId'   => 'int(11)',
            'type'              => 'varchar(20)',
            'department'        => 'varchar(50)',
            'name'              => 'varchar(50)',
            'surname'           => 'varchar(50)',
            'telNumber'         => 'varchar(20)',
            'mobileNumber'      => 'varchar(20)',
            'email'             => 'varchar(50)',
        ));

        $this->createTable('polestar_deliverypoint_time', array(
            'DeliveryPointId'   => 'int(11)',
            'type'              => 'varchar(20)',
            'day'               => 'varchar(15)',
            'StartTime'         => 'varchar(10)',
            'EndTime'           => 'varchar(10)',
            'PRIMARY KEY(DeliveryPointId,type,day)'
        ));
    }

    public function down() {
        $this->dropTable('polestar_deliverypoint_time');
        $this->dropTable('polestar_deliverypoint_contact');
        $this->dropTable('polestar_deliverypoint');
    }
}