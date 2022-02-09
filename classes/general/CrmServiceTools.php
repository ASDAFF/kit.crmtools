<?

final class CrmServiceTools extends IRestService
{
    const SCOPE_NAME = 'crm';
    private static $METHOD_NAMES = array(
        //region Status
        'crm.status.fields',
        'crm.status.add',
        'crm.status.get',
        'crm.status.list',
        'crm.status.update',
        'crm.status.delete',
        'crm.status.entity.types',
        'crm.status.entity.items',
        'crm.status.extra.fields',

        'crm.invoice.status.fields',
        'crm.invoice.status.add',
        'crm.invoice.status.get',
        'crm.invoice.status.list',
        'crm.invoice.status.update',
        'crm.invoice.status.delete',
        //endregion
        //region Enumeration
        'crm.enum.fields',
        'crm.enum.ownertype',
        'crm.enum.addresstype',
        'crm.enum.contenttype',
        'crm.enum.activitytype',
        'crm.enum.activitypriority',
        'crm.enum.activitydirection',
        'crm.enum.activitynotifytype',
        'crm.enum.activitystatus',
        //endregion
        //region Lead
        'crm.lead.fields',
        'crm.lead.add',
        'crm.lead.get',
        'crm.lead.list',
        'crm.lead.update',
        'crm.lead.delete',
        'crm.lead.productrows.set',
        'crm.lead.productrows.get',
        //endregion
        //region Deal
        'crm.deal.fields',
        'crm.deal.add',
        'crm.deal.get',
        'crm.deal.list',
        'crm.deal.update',
        'crm.deal.delete',
        'crm.deal.productrows.set',
        'crm.deal.productrows.get',
        'crm.deal.contact.fields',
        'crm.deal.contact.add',
        'crm.deal.contact.delete',
        'crm.deal.contact.items.get',
        'crm.deal.contact.items.set',
        'crm.deal.contact.items.delete',
        //endregion
        //region Deal Category
        'crm.dealcategory.fields',
        'crm.dealcategory.list',
        'crm.dealcategory.add',
        'crm.dealcategory.get',
        'crm.dealcategory.update',
        'crm.dealcategory.delete',
        'crm.dealcategory.status',
        'crm.dealcategory.stage.list',
        //endregion
        //region Company
        'crm.company.fields',
        'crm.company.add',
        'crm.company.get',
        'crm.company.list',
        'crm.company.update',
        'crm.company.delete',
        //endregion
        //region Contact
        'crm.contact.fields',
        'crm.contact.add',
        'crm.contact.get',
        'crm.contact.list',
        'crm.contact.update',
        'crm.contact.delete',
        'crm.contact.company.fields',
        'crm.contact.company.add',
        'crm.contact.company.delete',
        'crm.contact.company.items.get',
        'crm.contact.company.items.set',
        'crm.contact.company.items.delete',
        //endregion
        //region Currency
        'crm.currency.fields',
        'crm.currency.add',
        'crm.currency.get',
        'crm.currency.list',
        'crm.currency.update',
        'crm.currency.delete',
        'crm.currency.localizations.fields',
        'crm.currency.localizations.get',
        'crm.currency.localizations.set',
        'crm.currency.localizations.delete',
        //endregion
        //region Catalog
        'crm.catalog.fields',
        'crm.catalog.get',
        'crm.catalog.list',
        //endregion
        //region Product
        'crm.product.fields',
        'crm.product.add',
        'crm.product.get',
        'crm.product.list',
        'crm.product.update',
        'crm.product.delete',
        //endregion
        //region Product Property
        'crm.product.property.types',
        'crm.product.property.fields',
        'crm.product.property.settings.fields',
        'crm.product.property.enumeration.fields',
        'crm.product.property.add',
        'crm.product.property.get',
        'crm.product.property.list',
        'crm.product.property.update',
        'crm.product.property.delete',
        //endregion
        //region Product Section
        'crm.productsection.fields',
        'crm.productsection.add',
        'crm.productsection.get',
        'crm.productsection.list',
        'crm.productsection.update',
        'crm.productsection.delete',
        //endregion
        //region Product Row
        'crm.productrow.fields',
        'crm.productrow.add',
        'crm.productrow.get',
        'crm.productrow.list',
        'crm.productrow.update',
        'crm.productrow.delete',
        //endregion
        //region Activity
        'crm.activity.fields',
        'crm.activity.add',
        'crm.activity.get',
        'crm.activity.list',
        'crm.activity.update',
        'crm.activity.delete',
        'crm.activity.communication.fields',
        //endregion
        //region Quote
        'crm.quote.fields',
        'crm.quote.add',
        'crm.quote.get',
        'crm.quote.list',
        'crm.quote.update',
        'crm.quote.delete',
        'crm.quote.productrows.set',
        'crm.quote.productrows.get',
        'crm.quote.contact.fields',
        'crm.quote.contact.add',
        'crm.quote.contact.delete',
        'crm.quote.contact.items.get',
        'crm.quote.contact.items.set',
        'crm.quote.contact.items.delete',
        //endregion
        //region Requisite
        'crm.requisite.fields',
        'crm.requisite.add',
        'crm.requisite.get',
        'crm.requisite.list',
        'crm.requisite.update',
        'crm.requisite.delete',
        //
        'crm.requisite.userfield.add',
        'crm.requisite.userfield.get',
        'crm.requisite.userfield.list',
        'crm.requisite.userfield.update',
        'crm.requisite.userfield.delete',
        //
        'crm.requisite.preset.fields',
        'crm.requisite.preset.add',
        'crm.requisite.preset.get',
        'crm.requisite.preset.list',
        'crm.requisite.preset.update',
        'crm.requisite.preset.delete',
        'crm.requisite.preset.countries',
        //
        'crm.requisite.preset.field.fields',
        'crm.requisite.preset.field.availabletoadd',
        'crm.requisite.preset.field.add',
        'crm.requisite.preset.field.get',
        'crm.requisite.preset.field.list',
        'crm.requisite.preset.field.update',
        'crm.requisite.preset.field.delete',
        //
        'crm.requisite.bankdetail.fields',
        'crm.requisite.bankdetail.add',
        'crm.requisite.bankdetail.get',
        'crm.requisite.bankdetail.list',
        'crm.requisite.bankdetail.update',
        'crm.requisite.bankdetail.delete',
        //
        'crm.requisite.link.fields',
        'crm.requisite.link.list',
        'crm.requisite.link.get',
        'crm.requisite.link.register',
        'crm.requisite.link.unregister',
        //
        'crm.address.fields',
        'crm.address.add',
        'crm.address.update',
        'crm.address.list',
        'crm.address.delete',
        //endregion Requisite
        //region Measures
        'crm.measure.fields',
        'crm.measure.add',
        'crm.measure.get',
        'crm.measure.list',
        'crm.measure.update',
        'crm.measure.delete',
        //endregion Measures

        //region User Field
        'crm.lead.userfield.add',
        'crm.lead.userfield.get',
        'crm.lead.userfield.list',
        'crm.lead.userfield.update',
        'crm.lead.userfield.delete',

        'crm.deal.userfield.add',
        'crm.deal.userfield.get',
        'crm.deal.userfield.list',
        'crm.deal.userfield.update',
        'crm.deal.userfield.delete',

        'crm.company.userfield.add',
        'crm.company.userfield.get',
        'crm.company.userfield.list',
        'crm.company.userfield.update',
        'crm.company.userfield.delete',

        'crm.contact.userfield.add',
        'crm.contact.userfield.get',
        'crm.contact.userfield.list',
        'crm.contact.userfield.update',
        'crm.contact.userfield.delete',

        'crm.quote.userfield.add',
        'crm.quote.userfield.get',
        'crm.quote.userfield.list',
        'crm.quote.userfield.update',
        'crm.quote.userfield.delete',

        'crm.invoice.userfield.add',
        'crm.invoice.userfield.get',
        'crm.invoice.userfield.list',
        'crm.invoice.userfield.update',
        'crm.invoice.userfield.delete',

        'crm.userfield.fields',
        'crm.userfield.types',
        'crm.userfield.enumeration.fields',
        'crm.userfield.settings.fields',
        //endregion

        //region Externalchannel connector.
        'crm.externalchannel.connector.fields',
        'crm.externalchannel.connector.list',
        'crm.externalchannel.connector.register',
        'crm.externalchannel.connector.unregister',
        //endregion

        //region Misc.
        'crm.multifield.fields',
        'crm.duplicate.findbycomm',
        'crm.livefeedmessage.add',
        'crm.externalchannel.company',
        'crm.externalchannel.contact',
        'crm.externalchannel.activity.company',
        'crm.externalchannel.activity.contact',
        'crm.webform.configuration.get',
        'crm.sitebutton.configuration.get',
        'crm.persontype.fields',
        'crm.persontype.list',
        'crm.paysystem.fields',
        'crm.paysystem.list',
        //endregion
        //region Automation
        'crm.automation.trigger'
        //endregion
    );
    private static $PLACEMENT_NAMES = array(
        'CRM_LEAD_LIST_MENU',
        'CRM_DEAL_LIST_MENU',
        'CRM_INVOICE_LIST_MENU',
        'CRM_QUOTE_LIST_MENU',
        'CRM_CONTACT_LIST_MENU',
        'CRM_COMPANY_LIST_MENU',
        'CRM_ACTIVITY_LIST_MENU',
    );
    private static $DESCRIPTION = null;
    private static $PROXIES = array();

    public static function onRestServiceBuildDescription()
    {
        if(!self::$DESCRIPTION)
        {
            $bindings = array();
            // There is one entry point
            $callback = array('CCrmRestService', 'onRestServiceMethod');
            foreach(self::$METHOD_NAMES as $name)
            {
                $bindings[$name] = $callback;
            }

            $bindings[\CRestUtil::PLACEMENTS] = array();
            foreach(self::$PLACEMENT_NAMES as $name)
            {
                $bindings[\CRestUtil::PLACEMENTS][$name] = array();
            }

            CCrmLeadRestProxy::registerEventBindings($bindings);
            CCrmDealRestProxy::registerEventBindings($bindings);
            CCrmCompanyRestProxy::registerEventBindings($bindings);
            CCrmContactRestProxy::registerEventBindings($bindings);
            CCrmQuoteRestProxy::registerEventBindings($bindings);
            CCrmCurrencyRestProxy::registerEventBindings($bindings);
            CCrmProductRestProxy::registerEventBindings($bindings);
            CCrmActivityRestProxy::registerEventBindings($bindings);

            self::$DESCRIPTION = array('crm' => $bindings);
        }

        return self::$DESCRIPTION;
    }
    public static function onRestServiceMethod($arParams, $nav, CRestServer $server)
    {
        if(!CCrmPerms::IsAccessEnabled())
        {
            throw new RestException('Access denied.');
        }

        $methodName = $server->getMethod();

        $parts = explode('.', $methodName);
        $partCount = count($parts);
        if($partCount < 3 || $parts[0] !== 'crm')
        {
            throw new RestException("Method '{$methodName}' is not supported in current context.");
        }

        $typeName = strtoupper($parts[1]);
        $proxy = null;

        if(isset(self::$PROXIES[$typeName]))
        {
            $proxy = self::$PROXIES[$typeName];
        }

        
        if(!$proxy)
        {
            if($typeName === 'ENUM')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmEnumerationRestProxy();
            }
            elseif($typeName === 'MULTIFIELD')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmMultiFieldRestProxy();
            }
            elseif($typeName === 'CURRENCY')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmCurrencyRestProxy();
            }
            elseif($typeName === 'CATALOG')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmCatalogRestProxy();
            }
            elseif($typeName === 'PRODUCT' && strtoupper($parts[2]) === 'PROPERTY')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmToolsProductPropertyRestProxy(); //new CCrmProductPropertyRestProxy();
            }
            elseif($typeName === 'PRODUCT')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmToolsProductRestProxy(); //new CCrmProductRestProxy();
            }
            elseif($typeName === 'PRODUCTSECTION')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmProductSectionRestProxy();
            }
            elseif($typeName === 'PRODUCTROW')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmProductRowRestProxy();
            }
            elseif($typeName === 'STATUS')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmStatusRestProxy();
            }
            elseif($typeName === 'LEAD')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmLeadRestProxy();
            }
            elseif($typeName === 'DEAL')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmDealRestProxy();
            }
            elseif($typeName === 'DEALCATEGORY')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmDealCategoryProxy();
            }
            elseif($typeName === 'COMPANY')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmCompanyRestProxy();
            }
            elseif($typeName === 'CONTACT')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmContactRestProxy();
            }
            elseif($typeName === 'QUOTE')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmQuoteRestProxy();
            }
            elseif($typeName === 'INVOICE' && strtoupper($parts[2]) === 'STATUS')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmStatusInvoiceRestProxy();
            }
            elseif($typeName === 'INVOICE')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmInvoiceRestProxy();
            }
            elseif($typeName === 'REQUISITE')
            {
                if(strtoupper($parts[2]) === 'LINK')
                {
                    $proxy = self::$PROXIES[$typeName] = new CCrmRequisiteLinkRestProxy();
                }
                else
                {
                    $proxy = self::$PROXIES[$typeName] = new CCrmRequisiteRestProxy();
                }
            }
            elseif($typeName === 'ADDRESS')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmAddressRestProxy();
            }
            elseif($typeName === 'ACTIVITY')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmActivityRestProxy();
            }
            elseif($typeName === 'DUPLICATE')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmDuplicateRestProxy();
            }
            elseif($typeName === 'LIVEFEEDMESSAGE')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmLiveFeedMessageRestProxy();
            }
            elseif($typeName === 'USERFIELD')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmUserFieldRestProxy(CCrmOwnerType::Undefined);
            }
            elseif($typeName === 'EXTERNALCHANNEL')
            {
                if(strtoupper($parts[2]) === 'CONNECTOR')
                {
                    $proxy = self::$PROXIES[$typeName] = new CCrmExternalChannelConnectorRestProxy();
                }
                else
                {
                    $proxy = self::$PROXIES[$typeName] = new CCrmExternalChannelRestProxy();
                }
            }
            elseif($typeName === 'WEBFORM')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmWebformRestProxy();
            }
            elseif($typeName === 'SITEBUTTON')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmSiteButtonRestProxy();
            }
            elseif($typeName === 'PERSONTYPE')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmPersonTypeRestProxy();
            }
            elseif($typeName === 'PAYSYSTEM')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmPaySystemRestProxy();
            }
            elseif($typeName === 'MEASURE')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmMeasureRestProxy();
            }
            elseif($typeName === 'AUTOMATION')
            {
                $proxy = self::$PROXIES[$typeName] = new CCrmAutomationRestProxy();
            }
            else
            {
                throw new RestException("Could not find proxy for method '{$methodName}'.");
            }
            
            $proxy->setServer($server);
        }

        return $proxy->processMethodRequest(
            $parts[2],
            $partCount > 3 ? array_slice($parts, 3) : array(),
            $arParams,
            $nav,
            $server
        );
    }
    public static function getNavData($start, $isOrm = false)
    {
        return parent::getNavData($start, $isOrm);
    }
    public static function setNavData($result, $dbRes)
    {
        return parent::setNavData($result, $dbRes);
    }
}
?>