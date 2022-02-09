<?php
global $DBType;
$_1133948902 = 'sotbit.crmtools';
CModule::AddAutoloadClasses($_1133948902, array(
        'CModuleOptions' => 'classes/general/CModuleOptions.php',
        'CrmTools' => 'classes/general/CrmTools.php',
        'CCrmToolsProductRestProxy' => 'classes/general/CCrmToolsProductRestProxy.php',
        'CCrmToolsProductPropertyRestProxy' => 'classes/general/CCrmToolsProductPropertyRestProxy.php',
        'CrmProductTools' => 'classes/general/CrmProductTools.php',
        'CrmServiceTools' => 'classes/general/CrmServiceTools.php',
        'CCrmProductTools' => 'classes/mysql/crm_product.php',
    )
);

class SotbitCrmTools
{
    static private $_784000741;
    static private $_703596709 = false;

    private function __construct()
    {
        self::$_784000741 = \Bitrix\Main\Loader::includeSharewareModule('sotbit.crmtools');
    }

    public function getDemo()
    {
        return self::$_784000741;
    }

    public function OnEpilogHandler()
    {
        return true;
        if (\SotbitCrmTools::getInstance()->isDemo()) return true;
        $_214793310 = COption::GetOptionString('sotbit.crmtools', 'CRM_DIALOG');
        if ($_214793310 == 'Y') CrmTools::epilogHandler();
    }

    public function isDemo()
    {
        if (self::$_784000741 == 0 || self::$_784000741 == 3) {
            return true;
        } else {
            return false;
        }
    }

    static function getInstance()
    {
        if (self::$_703596709 == null) {
            self::$_703596709 = new SotbitCrmTools();
        }
        return self::$_703596709;
    }
}