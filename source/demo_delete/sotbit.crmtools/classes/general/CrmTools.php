<?

IncludeModuleLangFile(__FILE__);
class CrmTools
{

    const MODULE_ID = 'sotbit.crmtools';
    const CACHE_TIME_TOOLS = 1800;

    public function getDemo()
    {
        $module_id = "sotbit.crmtools";
        $sotbit_DEMO = CModule::IncludeModuleEx($module_id);
        if($sotbit_DEMO==3)
        {
            return false;
        }
        else return true;
    }
     public static function epilogHandler(){
        global $APPLICATION;
         if(!strstr($APPLICATION->GetCurPage(), '/bitrix/admin/')){
             $APPLICATION->AddHeadScript('/bitrix/js/main/core/core_admin_interface.js');
             $APPLICATION->AddHeadScript('/bitrix/js/main/hot_keys.js');
             $APPLICATION->AddHeadScript('/bitrix/components/bitrix/crm.product.search.dialog/templates/.default/bitrix/catalog.product.search/.default/script.js');
             $APPLICATION->AddHeadScript('/bitrix/components/bitrix/catalog.product.search/templates/.default/script.js');
             $APPLICATION->AddHeadScript('/bitrix/js/main/popup_menu.js');
             $APPLICATION->AddHeadScript('/bitrix/js/fileman/core_file_input.js');
             $APPLICATION->AddHeadScript('/bitrix/js/sotbit.crmtools/sotbit_crm_script.js');

         }
     }
}
?>