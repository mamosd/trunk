<?php
/**
 * Description of MenuManager
 *
 * @author ramon
 */
class MenuManager {

    static function get($type)
    {
        $result = array();

        if (isset(Yii::app()->user->role))
        {
            switch (Yii::app()->user->role->LoginRoleId) {
                case LoginRole::SUPER_ADMIN:
                case LoginRole::ADMINISTRATOR:
                    if ( Login::checkPermission('#^navigation/Polestar/#',true)) {
                        $result[] = array('label' => 'Polestar', 'url' => '#', 'items' => array(
                            array('label'=>'Routes',                'url'=>'#', 'visible' => Login::checkPermission(Permission::PERM__NAV__POLESTAR__ROUTES)
                                , 'items' => self::generatePolestarPrintCentreMenuItems('polestar/routeview')),
                            //array('label'=>'Activity',                'url'=>'#', 'visible' => Login::checkPermission(Permission::PERM__NAV__POLESTAR__ROUTES)
                            //    , 'items' => self::generatePolestarPrintCentreMenuItems('polestar/activity', 0)),
                            array('label'=>'Activity Log',             'url'=>array('polestar/activitylog'), 'visible' => Login::checkPermission(Permission::PERM__NAV__POLESTAR__ROUTES)),
                            array('label'=>'Suppliers',             'url'=>array('polestar/suppliers'), 'visible' => Login::checkPermission(Permission::PERM__NAV__POLESTAR__SUPPLIERS)),
                            array('label'=>'Delivery Points',             'url'=>array('polestar/deliverypoints'), 'visible' => Login::checkPermission(Permission::PERM__NAV__POLESTAR__DELIVERY_POINTS)),
                        ));
                    }

                    if ( Login::checkPermission('#^navigation/lsc/#',true)) {
                        $result[] = array('label' => 'LSC', 'url' => '#', 'items' => array(
                            array('label'=>'LSC Control Screen',    'url'=>array('finance/control'),                'visible' => Login::checkPermission(Permission::PERM__NAV__LSC__CONTROL_SCREEN)),
                            //array('label'=>'Base Routing Plan',     'url'=>array('finance/control', 'base' => 1),   'visible' => Login::checkPermission(Permission::PERM__NAV__LSC__BASE_ROUTING)),
                            array('label'=>'DTC Base Routing Plan',     'url'=>array('finance/baserouting', 'c' => 'DTC'),   'visible' => (Login::checkPermission(Permission::PERM__FUN__LSC__DTC) && Login::checkPermission(Permission::PERM__NAV__LSC__BASE_ROUTING))),
                            array('label'=>'DTR Base Routing Plan',     'url'=>array('finance/baserouting', 'c' => 'DTR'),   'visible' => (Login::checkPermission(Permission::PERM__FUN__LSC__DTR) && Login::checkPermission(Permission::PERM__NAV__LSC__BASE_ROUTING))),
                            array('label'=>'Contractors',           'url'=>array('finance/contractors'),            'visible' => Login::checkPermission(Permission::PERM__NAV__LSC__CONTRACTORS)),
                        ));
                    }

                    if ( Login::checkPermission('#^navigation/Express/routing/#',true)) {
                        $result[] = array('label'=>'Express', 'url'=>'#', 'items' => array(
                                array('label'=>'Import Order File', 'url'=>array('clientrouting/import')),
                                //array('label'=>'Types', 'url'=>array('clientrouting/types')),
                                array('label'=>'Titles', 'url'=>array('clientrouting/titles')),
                                array('label'=>'Schedules Control Screen', 'url'=>array('clientrouting/schedules')),
                                array('label'=>'Clear Data', 'url'=>array('clientrouting/cleardata')),
    //                            array('label'=>'Reporting', 'url'=>'#', 'items' => array(
    //                                array('label'=>'Performance Report', 'url'=>'#'),
    //                                array('label'=>'KPI Report', 'url'=>'#'),
    //                            )),
                            ));
                    }

                    if ( Login::checkPermission('#^navigation/NQ/secondary/#',true)) {
                        $result[] = array('label'=>'NQ Secondary', 'url'=>'#', 'items'=>array(
                                   array('label'=>'Process Rounds', 'url'=>array('admin/secondaryrouting')),
                                   array('label'=>'Route Maintenance', 'url'=>array('admin/secondaryroutingroutes'))
                               ));
                    }

                    if ( Login::checkPermission('#^navigation/NQ/primary/#',true)) {
                        $nqPrimaryItems = array(
                                   array('label'=>'Routes', 'url'=>array('admin/routes')),
                                   array('label'=>'D. Points', 'url'=>array('admin/deliverypoints')),
                                   array('label'=>'Titles', 'url'=>array('admin/titles')),
                                   array('label'=>'P. Centres', 'url'=>array('admin/printcentres')),
                                   array('label'=>'Suppliers', 'url'=>array('admin/suppliers')),
                                   array('label'=>'Orders', 'url'=>'#', 'items' => array(
                                       array('label'=>'Entry', 'url'=>array('admin/orders')),
                                       array('label'=>'Listing', 'url'=>array('admin/ordersedit')),
                                       array('label'=>'Archived Orders', 'url'=>array('admin/reportingcontrol', 'archived'=>'1')),
                                   )),
                                   array('label'=>'Control', 'url'=>'#', 'items'=> array(
                                       array('label'=>'Control Sheet', 'url'=>array('admin/reportingcontrol')),
                                       array('label'=>'Pallet Report', 'url'=>array('admin/reportingpallet')),
                                   )),
                               );

                        if ( Login::checkPermission('#^navigation/NQ/primary/pallets/#',true)) {
                            $nqPrimaryItems[] = array('label'=>'Pallets', 'url'=>'#', 'items'=> array(
                                       array('label' => 'Print Centres', 'url' => '#', 'items' => array(
                                           array('label'=>'Print Print Centre Sheets', 'url'=>array('admin/printpalletsheetspc')),
                                           array('label'=>'Upload Summary', 'url'=>array('admin/uploadpalletsheetpc')),
                                       )),
                                       array('label' => 'Suppliers', 'url' => '#', 'items' => array(
                                           array('label'=>'Print Supplier Sheets', 'url'=>array('admin/printpalletsheets')),
                                           array('label'=>'Upload Summary', 'url'=>array('admin/uploadpalletsheet')),
                                           array('label'=>'Report', 'url'=>array('admin/palletreport')),
                                       )),
                                       array('label'=>'Delivery Point Report', 'url'=>array('admin/palletreportdp')),
                                   ));
                        }

                        $result[] = array('label' => 'NQ Primary', 'url' => '#', 'items' => $nqPrimaryItems);
                    }

                    if ( Login::checkPermission('#^navigation/admin/#',true)) {
                        $result[] = array('label'=>'Admin', 'url'=>'#', 'items'=>array(
                                    array('label'=>'Login Management',  'url'=>array('admin/logins'),   'visible' => Login::checkPermission(Permission::PERM__NAV__ADMIN__LOGIN_MANAGEMENT)),
                                    array('label'=>'Area Management',   'url'=>array('admin/areas'),    'visible' => Login::checkPermission(Permission::PERM__NAV__ADMIN__AREA_MANAGEMENT)),
                                    array('label'=>'Role Management',   'url'=>array('admin/roles'),    'visible' => Login::checkPermission(Permission::PERM__NAV__ADMIN__ROLE_MANAGEMENT)),
                                   ),
                                );
                    }
                    break;
                case LoginRole::SUPPLIER:
                        $result = array(
                            array('label'=>'Home', 'url'=>array('supplier/routes')),
                        );
                    break;
            }
        }

        return $result;
    }

    private static function generatePolestarPrintCentreMenuItems($baseUrl, $allValue = FALSE){
        $printCentreItems = array();

        
        $printCentres = PolestarPrintCentre::getAllForLoginAsOptions();
        if (!empty($printCentres)) {
            if ($allValue !== FALSE) {
                $printCentreItems[] = array('label'=> 'All',   'url'=>array($baseUrl, 'id' => $allValue));
            }
            foreach($printCentres as $pcid => $pcname) {
                $printCentreItems[] = array('label'=>$pcname,   'url'=>array($baseUrl, 'id' => $pcid));
            }
        }
        else {
            $printCentreItems[] = array('label'=>'[no print centre defined]',   'url'=>'#');   
        }

        return $printCentreItems;
    }

}

?>
