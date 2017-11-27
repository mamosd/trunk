<?php
class Permission extends CActiveRecord
{
    // NAVIGATION PERMISSIONS - FORMAT: 1nnn
    const PERM__NAV__ADMIN__LOGIN_MANAGEMENT    = 1001;
    const PERM__NAV__ADMIN__AREA_MANAGEMENT     = 1002;
    const PERM__NAV__ADMIN__ROLE_MANAGEMENT     = 1003;
    
    const PERM__NAV__LSC__CONTROL_SCREEN    = 1004;
    const PERM__NAV__LSC__BASE_ROUTING      = 1005;
    const PERM__NAV__LSC__CONTRACTORS       = 1006;
    
    // GENERAL PERMISSIONS TO SHOW/HIDE MENUS
    const PERM__NAV__EXPRESS__ROUTING__MENU     = 1007;
    const PERM__NAV__NQ__SECONDARY__MENU        = 1008;
    const PERM__NAV__NQ__PRIMARY__PALLETS__MENU = 1009;
    const PERM__NAV__NQ__PRIMARY__MENU          = 1010;
    const PERM__NAV__POLESTAR__ROUTES           = 1011;
    const PERM__NAV__POLESTAR__SUPPLIERS        = 1012;
    const PERM__NAV__POLESTAR__DELIVERY_POINTS  = 1013;
    
    
    // FUNCTIONALITY PERMISSIONS - FORMAT: 2nnn
    const PERM__FUN__LOGIN__PERMISSIONS = 2001; // allow adding/editing permissions to logins
    
    const PERM__FUN__LSC__DTR               = 2002;
    const PERM__FUN__LSC__DTC               = 2003;
    const PERM__FUN__LSC__ACKNOWLEDGE       = 2004;
    const PERM__FUN__LSC__OVERRIDE          = 2005;
    const PERM__FUN__LSC__PO                = 2006;
    const PERM__FUN__LSC__INVOICES          = 2007;
    const PERM__FUN__LSC__CONTRACTOR_EDIT   = 2008;
    const PERM__FUN__LSC__INITIALIZE        = 2009;
    
    const PERM__FUN__POLESTAR__ROUTE_EDIT       = 2010;
    const PERM__FUN__POLESTAR__SUPPLIER_EDIT    = 2011;
    const PERM__FUN__POLESTAR__DEL_POINT_EDIT   = 2012;
    const PERM__FUN__POLESTAR__COSTING          = 2013;
    const PERM__FUN__POLESTAR__MILEAGE_EDIT     = 2014;
    const PERM__FUN__POLESTAR__ROUTE_CLONE      = 2015;
    const PERM__FUN__POLESTAR__CLEAR_HIGHLIGHT  = 2016;
    
    
    public static $OLD_PERMS = array(
        //self::OLD_BATHSTORE_IMPORT_DATA,            self::OLD_BATHSTORE_ROUTING,                self::OLD_BATHSTORE_REPORTS,        self::OLD_BATHSTORE_MANIFESTS,
    );

    public static $PERMS = array(
        self::PERM__NAV__ADMIN__LOGIN_MANAGEMENT => array(
            "PermissionId"  => self::PERM__NAV__ADMIN__LOGIN_MANAGEMENT,
            "Route"         => "navigation/admin/Login Management",
        ),
        self::PERM__NAV__ADMIN__AREA_MANAGEMENT => array(
            "PermissionId"  => self::PERM__NAV__ADMIN__AREA_MANAGEMENT,
            "Route"         => "navigation/admin/Area Management",
        ),
        self::PERM__NAV__ADMIN__ROLE_MANAGEMENT => array(
            "PermissionId"  => self::PERM__NAV__ADMIN__ROLE_MANAGEMENT,
            "Route"         => "navigation/admin/Role Management",
        ),
        self::PERM__NAV__LSC__CONTROL_SCREEN => array(
            "PermissionId"  => self::PERM__NAV__LSC__CONTROL_SCREEN,
            "Route"         => "navigation/lsc/Control Screen",
            //"Assignable"    => 1, // default
        ),
        self::PERM__NAV__LSC__BASE_ROUTING => array(
            "PermissionId"  => self::PERM__NAV__LSC__BASE_ROUTING,
            "Route"         => "navigation/lsc/Base Routing",
        ),
        self::PERM__NAV__LSC__CONTRACTORS => array(
            "PermissionId"  => self::PERM__NAV__LSC__CONTRACTORS,
            "Route"         => "navigation/lsc/Contractors",
        ),
        self::PERM__NAV__EXPRESS__ROUTING__MENU => array(
            "PermissionId"  => self::PERM__NAV__EXPRESS__ROUTING__MENU,
            "Route"         => "navigation/Express/routing/Show Menu",
        ),
        self::PERM__NAV__NQ__SECONDARY__MENU => array(
            "PermissionId"  => self::PERM__NAV__NQ__SECONDARY__MENU,
            "Route"         => "navigation/NQ/secondary/Show Menu",
        ),
        self::PERM__NAV__NQ__PRIMARY__PALLETS__MENU => array(
            "PermissionId"  => self::PERM__NAV__NQ__PRIMARY__PALLETS__MENU,
            "Route"         => "navigation/NQ/primary/pallets/Show Menu",
        ),
        self::PERM__NAV__NQ__PRIMARY__MENU => array(
            "PermissionId"  => self::PERM__NAV__NQ__PRIMARY__MENU,
            "Route"         => "navigation/NQ/primary/Show Menu",
        ),
        self::PERM__NAV__POLESTAR__ROUTES => array(
            "PermissionId"  => self::PERM__NAV__POLESTAR__ROUTES,
            "Route"         => "navigation/Polestar/Routes",
        ),
        self::PERM__NAV__POLESTAR__SUPPLIERS => array(
            "PermissionId"  => self::PERM__NAV__POLESTAR__SUPPLIERS,
            "Route"         => "navigation/Polestar/Suppliers",
        ),
        self::PERM__NAV__POLESTAR__DELIVERY_POINTS => array(
            "PermissionId"  => self::PERM__NAV__POLESTAR__DELIVERY_POINTS,
            "Route"         => "navigation/Polestar/Delivery Points",
        ),
        self::PERM__FUN__LOGIN__PERMISSIONS => array(
            "PermissionId"  => self::PERM__FUN__LOGIN__PERMISSIONS,
            "Route"         => "functionality/admin/Allow Permissions edit on logins",
        ),
        self::PERM__FUN__LSC__DTR => array(
            "PermissionId"  => self::PERM__FUN__LSC__DTR,
            "Route"         => "functionality/lsc/Allow DTR content access",
        ),
        self::PERM__FUN__LSC__DTC => array(
            "PermissionId"  => self::PERM__FUN__LSC__DTC,
            "Route"         => "functionality/lsc/Allow DTC content access",
        ),
        self::PERM__FUN__LSC__ACKNOWLEDGE => array(
            "PermissionId"  => self::PERM__FUN__LSC__ACKNOWLEDGE,
            "Route"         => "functionality/lsc/Allow Acknowledging exceptions",
        ),
        self::PERM__FUN__LSC__OVERRIDE => array(
            "PermissionId"  => self::PERM__FUN__LSC__OVERRIDE,
            "Route"         => "functionality/lsc/Allow Overriding acknowledged exceptions",
        ),
        self::PERM__FUN__LSC__PO => array(
            "PermissionId"  => self::PERM__FUN__LSC__PO,
            "Route"         => "functionality/lsc/Allow generating POs",
        ),
        self::PERM__FUN__LSC__INVOICES => array(
            "PermissionId"  => self::PERM__FUN__LSC__INVOICES,
            "Route"         => "functionality/lsc/Allow generating Invoices",
        ),
        self::PERM__FUN__LSC__CONTRACTOR_EDIT => array(
            "PermissionId"  => self::PERM__FUN__LSC__CONTRACTOR_EDIT,
            "Route"         => "functionality/lsc/Allow editing contractor details",
        ),
        self::PERM__FUN__LSC__INITIALIZE => array(
            "PermissionId"  => self::PERM__FUN__LSC__INITIALIZE,
            "Route"         => "functionality/lsc/Allow initializing a week's routing",
        ),
        /*self::PERM__FUN__POLESTAR__ROUTE_EDIT => array(
            "PermissionId"  => self::PERM__FUN__POLESTAR__ROUTE_EDIT,
            "Route"         => "functionality/polestar/Allow editing route details",
        ),*/ // leave commented out as it is not required atm.
        self::PERM__FUN__POLESTAR__SUPPLIER_EDIT => array(
            "PermissionId"  => self::PERM__FUN__POLESTAR__SUPPLIER_EDIT,
            "Route"         => "functionality/polestar/Allow editing supplier details",
        ),
        self::PERM__FUN__POLESTAR__DEL_POINT_EDIT => array(
            "PermissionId"  => self::PERM__FUN__POLESTAR__DEL_POINT_EDIT,
            "Route"         => "functionality/polestar/Allow editing delivery point details",
        ),
        self::PERM__FUN__POLESTAR__COSTING => array(
            "PermissionId"  => self::PERM__FUN__POLESTAR__COSTING,
            "Route"         => "functionality/polestar/Allow accessing costing related information (eg Agreed Price)",
        ),
        self::PERM__FUN__POLESTAR__MILEAGE_EDIT => array(
            "PermissionId"  => self::PERM__FUN__POLESTAR__MILEAGE_EDIT,
            "Route"         => "functionality/polestar/Allow mileage manual editing",
        ), 
        self::PERM__FUN__POLESTAR__ROUTE_CLONE => array(
            "PermissionId"  => self::PERM__FUN__POLESTAR__ROUTE_CLONE,
            "Route"         => "functionality/polestar/Allow route cloning",
        ),
        self::PERM__FUN__POLESTAR__CLEAR_HIGHLIGHT => array(
            "PermissionId"  => self::PERM__FUN__POLESTAR__CLEAR_HIGHLIGHT,
            "Route"         => "functionality/polestar/Allow clearing change highlight",
        ),
    );

    public static function get($id){
        if (!empty(self::$PERMS[$id])) return self::$PERMS[$id];
        return self::model()->findByPk($id);
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'permission';
    }

    public static function getOptions(){
        $all = self::loadAll();
        $result = array();
        foreach ($all as $p){
            if (!isset($p['Assignable']) || $p['Assignable'] == 1)
                $result[$p['PermissionId']] = $p['PermissionId'];
        }
        return $result;
    }

    public static function getAllAsTree(){

        $mergeTree = function (&$tree, $utree, $p) use (&$mergeTree) {
            if (empty($utree)) return $tree;

            $u = array_shift($utree);

            if (empty($utree)) {
                if (!isset($p['Assignable']) || $p['Assignable'] == 1) {
                    $tree[$u] = array(
                            "Id" => $p['PermissionId'],
                            "Name" => $u,
                            "Description" => isset($p['description'])?$p['description']:$u,
                    );
                } else {
                    foreach ($tree as &$n) {
                        if ($n['Name'] == $u){
                            $n['Description'] = isset($p['description'])?$p['description']:$u;
                            $n['Children'] = array();
                            return $tree;
                        }
                    }
                    $a = array();
                    $tree[] = array(
                            'Name' => $u,
                            'Description' => isset($p['description'])?$p['description']:$u,
                            'Children' => array(),
                    );
                }
            } else {
                foreach ($tree as &$n) {
                    if ($n['Name'] == $u){
                        $n['Children'] = $mergeTree($n['Children'], $utree, $p);
                        return $tree;
                    }
                }
                $a = array();
                $tree[] = array(
                        'Name' => $u,
                        'Children' => $mergeTree($a,$utree,$p),
                );
            }
            usort($tree, function($a,$b){
                $an = $a['Name'];
                $bn = $b['Name'];
                if ($an < $bn) return -1;
                if ($an > $bn) return 1;
                return 0;
            });
            return $tree;
        };

        $all = self::loadAll();
        $tree = array();
        foreach ($all as $s) {
            $r = empty($s['Route'])? $s['Code'] : $s['Route'];
            $ct = explode("/",$r);
            $mergeTree($tree,$ct,$s);
        }
        return $tree;
    }

    public static function loadAll() {
        $result = self::$PERMS;
        $perms = self::model()->findAll();
        foreach ($perms as $perm) {
            $id = $perm->PermissionId;
            if (in_array($id, self::$OLD_PERMS)) continue;
            if (!isset($result[$id])) {
                $result[$id] = $perm;
            }
        }
        return $result;
    }
}
?>