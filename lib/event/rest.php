<?php
namespace Kit\CrmTools\Event;
use Bitrix\Rest\EventTable;

/**
 * Class Callback
 *
 * Callback for Bitrix events transferred to REST events
 *
 * @package Bitrix\Rest
 **/
class Rest
{
	const SCOPE_NAME = 'kit.crmtools';
	
	public static function onRestServiceBuildDescription()
	{
		$methods = array(
			"_events" => array('ONCRMPRODUCTUPDATE'=> array('kit.crmtools', "ONAFTERCRMPRODUCTUPDATE",array('CCrmToolsProductRestProxy','processEvent'),array('category' => 'crm'))),
		);
		
		return array(self::SCOPE_NAME => $methods);
	}
}