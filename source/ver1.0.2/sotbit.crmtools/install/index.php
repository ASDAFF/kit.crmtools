<?php
IncludeModuleLangFile(__FILE__);

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class sotbit_crmtools extends CModule
{
    const MODULE_ID = 'sotbit.crmtools';
    var $MODULE_ID = 'sotbit.crmtools';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $PARTNER_NAME = '';
    var $PARTNER_URI = '';
    var $_181607965 = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = GetMessage('sotbit.crmtools_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('sotbit.crmtools_MODULE_DESC');
        $this->PARTNER_NAME = Loc::getMessage('sotbit.crmtools_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('sotbit.crmtools_PARTNER_URI');
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;
        $this->InstallFiles();
        $this->InstallDB();
        RegisterModule(self::MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage(self::MODULE_ID . '_MODULE_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/step.php');
    }

    function InstallFiles($_2088736790 = array())
    {
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/js', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . self::MODULE_ID, true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/services/sotbitrest', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/services/sotbitrest', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/components', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);
        return true;
    }

    function InstallDB($_2088736790 = array())
    {
        $_1650829134 = \Bitrix\Main\EventManager::getInstance();
        $_1650829134->registerEventHandler('main', 'OnEpilog', self::MODULE_ID, 'SotbitCrmTools', 'OnEpilogHandler');
        $_1650829134->registerEventHandler('rest', 'OnRestServiceBuildDescription', 'sotbit.crmtools', '\Sotbit\CrmTools\Event\Rest', 'onRestServiceBuildDescription');
        $_1650829134->registerEventHandler('crm', 'OnAfterCrmProductUpdate', 'rest', '\Bitrix\Rest\Event\Callback', 'sotbit.crmtools__ONAFTERCRMPRODUCTUPDATE');
        CUrlRewriter::Add(array('CONDITION' => '#^/sotbitrest/#', 'RULE' => '', 'ID' => '', 'PATH' => '/bitrix/services/sotbitrest/index.php',));
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION, $step;
        $this->UnInstallDB();
        $this->UnInstallFiles();
        $GLOBALS['errors'] = $this->_1849668037;
        $APPLICATION->IncludeAdminFile(GetMessage('_MODULE_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/unstep2.php');
    }

    function UnInstallDB($_2088736790 = array())
    {
        $_1650829134 = \Bitrix\Main\EventManager::getInstance();
        $_1650829134->unRegisterEventHandler('main', 'OnEpilog', self::MODULE_ID, 'SotbitCrmTools', 'OnEpilogHandler');
        $_1650829134->unRegisterEventHandler('rest', 'OnRestServiceBuildDescription', 'sotbit.crmtools', '\Sotbit\CrmTools\Event\Rest', 'onRestServiceBuildDescription');
        $_1650829134->unRegisterEventHandler('crm', 'OnAfterCrmProductUpdate', 'rest', '\Bitrix\Rest\Event\Callback', 'sotbit.crmtools__ONAFTERCRMPRODUCTUPDATE');
        UnRegisterModule(self::MODULE_ID);
        return true;
    }

    function UnInstallFiles($_2088736790 = array())
    {
        DeleteDirFilesEx('/bitrix/js/' . self::MODULE_ID . '/');
        DeleteDirFilesEx('/bitrix/services/sotbitrest/');
        return true;
    }
}