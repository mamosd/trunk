<?php

class m141006_112816_66_changes extends CDbMigration
{
	public function up()
	{
            $this->addColumn("finance_comment","OutputOnInvoice","TINYINT NOT NULL DEFAULT 1");
            
            $this->execute("CREATE TABLE `finance_adjustment_expense` (
                    `AdjustmentExpenseId` INT NOT NULL AUTO_INCREMENT,
                    `AdjustmentId` INT NOT NULL,
                    `Amount` DECIMAL(10,2) NOT NULL,
                    `Comment` TEXT NOT NULL,
                    `IsActive` TINYINT NOT NULL DEFAULT '1',
                    `CreatedBy` INT NOT NULL,
                    `DateCreated` DATETIME NOT NULL,
                    PRIMARY KEY (`AdjustmentExpenseId`),
                    INDEX `AdjustmentId` (`AdjustmentId`)
            )
            COLLATE='latin1_swedish_ci'
            ENGINE=InnoDB;");
            
            $this->execute("insert into finance_adjustment_expense (AdjustmentId, Amount, Comment, IsActive, CreatedBy, DateCreated)
                    select AdjustmentId,
                            MiscFee,
                            case when MiscFee >= 0 then 'Expense' else 'Deduction' end,
                            1,
                            CreatedBy,
                            CreatedDate
                    from finance_adjustment
                    where ifnull(MiscFee,0) != 0");
            
            $this->execute("create or replace view finance_route_instance_details
                    as
                    select `ri`.`RouteInstanceId` AS `RouteInstanceId`,`rc`.`RouteCategoryId` AS `RouteCategoryId`,`rc`.`Description` AS `Category`,`rc`.`ContractType` AS `CategoryType`,`r`.`RouteId` AS `RouteId`,`r`.`Code` AS `Route`,`ri`.`Date` AS `RouteDate`,`ri`.`EntryType` AS `EntryType`,`ri`.`ContractorId` AS `ContractorId`,`c`.`Code` AS `ContractorCode`,`c`.`FirstName` AS `ContractorFirstName`,`c`.`LastName` AS `ContractorLastName`,`c`.`ContractorTypeId` AS `ContractorTypeId`,`ct`.`Description` AS `ContractorType`,`c`.`AccountNumber` AS `ContractorAccountNumber`,`adj`.`ContractorId` AS `AdjContractorId`,`ca`.`Code` AS `AdjContractorCode`,`ca`.`FirstName` AS `AdjContractorFirstName`,`ca`.`LastName` AS `AdjContractorLastName`,`ca`.`ContractorTypeId` AS `AdjContractorTypeId`,`cta`.`Description` AS `AdjContractorType`,`ca`.`AccountNumber` AS `AdjContractorAccountNumber`,`ri`.`Fee` AS `Fee`,`adj`.`Fee` AS `AdjFee`,`adj`.`MiscFee` AS `MiscFee`,`ri`.`IsBase` AS `IsBase`
                    ,(case when isnull(`adj`.`AdjustmentId`) then 0 else 1 end) AS `IsAdjustment`
                    , `adj`.`AdjustmentId`
                    ,`ri`.`CreatedDate` AS `CreatedDate`,`ri`.`CreatedBy` AS `CreatedById`,`lc`.`FriendlyName` AS `CreatedBy`,`ri`.`AckDate` AS `AckDate`,`ri`.`AckBy` AS `AckById`,`la`.`FriendlyName` AS `AckBy`,`adj`.`CreatedDate` AS `AdjCreatedDate`,`adj`.`CreatedBy` AS `AdjCreatedBy`,`adj`.`AckDate` AS `AdjAckDate`,`adj`.`AckBy` AS `AdjAckById`,`laa`.`FriendlyName` AS `AdjAckBy`,`c`.`ApplicableTaxId` AS `ContractorTaxId`,`t`.`Code` AS `ContractorTaxCode`,`t`.`Percentage` AS `ContractorTaxPercentage`,`ca`.`ApplicableTaxId` AS `AdjContractorTaxId`,`ta`.`Code` AS `AdjContractorTaxCode`,`ta`.`Percentage` AS `AdjContractorTaxPercentage`,`c`.`ParentContractorId` AS `ParentContractorId`,`ca`.`ParentContractorId` AS `AdjParentContractorId` from ((((((((((((`finance_route_instance` `ri` left join `finance_route` `r` on((`r`.`RouteId` = `ri`.`RouteId`))) left join `finance_route_category` `rc` on((`rc`.`RouteCategoryId` = `r`.`RouteCategoryId`))) left join `finance_contractor` `c` on((`c`.`ContractorId` = `ri`.`ContractorId`))) left join `finance_contractor_type` `ct` on((`ct`.`Id` = `c`.`ContractorTypeId`))) left join `login` `lc` on((`lc`.`LoginId` = `ri`.`CreatedBy`))) left join `login` `la` on((`la`.`LoginId` = `ri`.`AckBy`))) left join `finance_adjustment` `adj` on((`adj`.`RouteInstanceId` = `ri`.`RouteInstanceId`))) left join `finance_contractor` `ca` on((`ca`.`ContractorId` = `adj`.`ContractorId`))) left join `finance_contractor_type` `cta` on((`cta`.`Id` = `ca`.`ContractorTypeId`))) left join `login` `laa` on((`laa`.`LoginId` = `adj`.`AckBy`))) left join `finance_tax` `t` on((`t`.`Id` = `c`.`ApplicableTaxId`))) left join `finance_tax` `ta` on((`ta`.`Id` = `ca`.`ApplicableTaxId`)));");
	}

	public function down()
	{
            $this->dropColumn("finance_comment","OutputOnInvoice");
	}
}