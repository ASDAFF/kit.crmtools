<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
    die();
}
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

if(!CModule::IncludeModule('rest') && !CModule::IncludeModule('kit.crmtools'))
{
    return;
}
CModule::IncludeModule('kit.crmtools');
$query = \CRestUtil::getRequestData();

$arDefaultUrlTemplates404 = array(
    "method" => "#method#",
    "method1" => "#method#/",
    "webhook" => "#aplogin#/#ap#/#method#",
    "webhook1" => "#aplogin#/#ap#/#method#/",
);

$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();

$arComponentVariables = array(
    "method", "aplogin", "ap"
);

$arVariables = array();
$arParams["SEF_MODE"]= "Y" ;
$arParams["SEF_FOLDER"]="/kitrest/" ;
$arParams["SEF_URL_TEMPLATES"]= array("path"=>"#method#") ;
$arParams["CACHE_TYPE"]= "A" ;

if($arParams["SEF_MODE"] == "Y")
{
    $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

    $componentPage = CComponentEngine::ParseComponentPath(
        $arParams["SEF_FOLDER"],
        $arUrlTemplates,
        $arVariables
    );

    CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

    $query = array_merge($query, $arVariables);
    unset($query['method']);
}
else
{
    ShowError('Non-SEF mode is not supported by bitrix:rest.server component');
}

$transport = 'json';
$method = ToLower($arVariables['method']);
$point = strrpos($method, '.');


if($point > 0)
{
    $check = substr($method, $point+1);
    if(CRestServer::transportSupported($check))
    {
        $transport = $check;
        $method = substr($method, 0, $point);
    }
}


$server = new CrmProductTools(array(
    "CLASS" => 'CRestProvider',
    "METHOD" => $method,
    "TRANSPORT" => $transport,
    "QUERY" => $query,
));

$result = $server->process();

//file_put_contents(dirname(__FILE__).'/log.log', print_r(array(
//	"CLASS" => $arParams["CLASS"],
//	"METHOD" => $method,
//	"TRANSPORT" => $transport,
//	"QUERY" => $query,
//), true), FILE_APPEND);

$APPLICATION->RestartBuffer();

$server->sendHeaders();

if(\KitCrmTools::getInstance()->isDemo())
    $result = array
        (
            'error' => 'DEMO_EXPIRED',
            'error_description' => Loc::getMessage('DEMO_EXPIRED')
        );

echo $server->output($result);

CMain::FinalActions();
die();
