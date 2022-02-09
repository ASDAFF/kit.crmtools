<?
CModule::includeModule('kit.crmtools');
class CCrmToolsProductRestProxy extends CCrmProductRestProxy {
    private $userTypes = null;
    private $properties = null;
    protected function innerGet($ID, &$errors)
    {

        if(!CModule::IncludeModule('iblock'))
        {
            throw new RestException('Could not load iblock module.');
        }

        if(!CCrmProductTools::CheckReadPermission($ID))
        {
            $errors[] = 'Access denied.';
            return false;
        }

        $catalogID = CCrmCatalog::GetDefaultID();


        $elem = \Bitrix\Iblock\ElementTable::getList(
        		array(
        				'filter' => array('ID' => $ID),
        				'select' => array('IBLOCK_ID'),
        				'limit' => 1
        		)
        )->fetch();
        
        if($elem['IBLOCK_ID'] > 0)
        {
        	$catalogID = $elem['IBLOCK_ID'];
        }
        
        /*
        $mxResult = CCatalogSku::GetProductInfo(
            $ID
        );
        if($mxResult){
            $catalogID = $mxResult['OFFER_IBLOCK_ID'];
        }*/
        
        $filter = array('ID' => $ID, 'CATALOG_ID'=> $catalogID);
        //$catalogID = 29;

        if($catalogID <= 0)
        {
            $errors[] = 'Product is not found.';
            return null;
        }


        $dbResult = CCrmProductTools::GetList(array(), $filter, array('*'), array('nTopCount' => 1));
        $result = is_object($dbResult) ? $dbResult->Fetch() : null;
        if(!is_array($result))
        {
            $errors[] = 'Product is not found.';
            return null;
        }

        $this->initializePropertiesInfo($catalogID);
        $this->getProperties($catalogID, $result, array('PROPERTY_*'));

        return $result;
    }
    protected function innerUpdate($ID, &$fields, &$errors, array $params = null)
    {
        if(!is_array($fields))
        {
            throw new RestException("The parameter 'fields' must be array.");
        }

        if(!CModule::IncludeModule('iblock'))
        {
            throw new RestException('Could not load iblock module.');
        }

//        if(!(CCrmProductTools::CheckUpdatePermission($ID) && CCrmProductTools::EnsureDefaultCatalogScope($ID)))
//        {
//            $errors[] = 'Access denied.';
//            return false;
//        }



        $catalogID = CCrmCatalog::GetDefaultID();

        $elem = \Bitrix\Iblock\ElementTable::getList(
        		array(
        				'filter' => array('ID' => $ID),
        				'select' => array('IBLOCK_ID'),
        				'limit' => 1
        		)
        )->fetch();
        		
        if($elem['IBLOCK_ID'] > 0)
        {
        	$catalogID = $elem['IBLOCK_ID'];
        }
       
        /*$mxResult = CCatalogSku::GetProductInfo(
            $ID
        );
        if($mxResult){
            $catalogID = $mxResult['OFFER_IBLOCK_ID'];
        }*/
        if($catalogID <= 0)
        {
            $errors[] = 'Product catalog is not found.';
            return false;
        }

//        if(!CCrmProductTools::Exists($ID))
//        {
//            $errors[] = 'Product is not found';
//            return false;
//        }

        // Product properties

        $this->initializePropertiesInfo($catalogID);
        $propertyValues = array();

        foreach ($this->properties as $propId => $property)
        {
            if (isset($fields[$propId]))
                $propertyValues[$property['ID']] = $fields[$propId];
            unset($fields[$propId]);
        }

        if(count($propertyValues) > 0)
        {
            $fields['PROPERTY_VALUES'] = $propertyValues;
            $rsProperties = CIBlockElement::GetProperty(
                $catalogID,
                $ID,
                'sort', 'asc',
                array('ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
            );
            while($property = $rsProperties->Fetch())
            {
                if (isset($property['USER_TYPE']) && !empty($property['USER_TYPE'])
                    && !array_key_exists($property['USER_TYPE'], $this->userTypes))
                    continue;

                if($property['PROPERTY_TYPE'] !== 'F' && !array_key_exists($property['ID'], $propertyValues))
                {
                    if(!array_key_exists($property['ID'], $fields['PROPERTY_VALUES']))
                        $fields['PROPERTY_VALUES'][$property['ID']] = array();

                    $fields['PROPERTY_VALUES'][$property['ID']][$property['PROPERTY_VALUE_ID']] = array(
                        'VALUE' => $property['VALUE'],
                        'DESCRIPTION' => $property['DESCRIPTION']
                    );
                }
            }
        }
        $result = CCrmProductTools::Update($ID, $fields);
        if($result !== true)
        {
            $errors[] = CCrmProductTools::GetLastError();
        }
        return $result;
    }
    public function getList($order, $filter, $select, $start)
    {
        if(!CModule::IncludeModule('iblock'))
        {
            throw new RestException('Could not load iblock module.');
        }

        if(!CCrmProductTools::CheckReadPermission(0))
        {
            throw new RestException('Access denied.');
        }

        $catalogID = CCrmCatalog::GetDefaultID();
        if($catalogID <= 0)
        {
            $result = array();
            $dbResult = new CDBResult();
            $dbResult->InitFromArray($result);
            return CCrmRestService::setNavData($result, $dbResult);
        }

        $catalog = CCatalogSKU::GetInfoByProductIBlock($catalogID);
        if($catalog)
          $catalogID = array($catalogID, $catalog['IBLOCK_ID']);

        $navigation = CCrmRestService::getNavData($start);

        if(!is_array($order) || empty($order))
        {
            $order = array('sort' => 'asc');
        }

        if(!isset($navigation['bShowAll']))
        {
            $navigation['bShowAll'] = false;
        }

        $enableCatalogData = false;
        $catalogSelect = null;
        $priceSelect = null;
        $vatSelect = null;
        $propertiesSelect = array();

        $selectAll = false;
        if(is_array($select))
        {
            if(!empty($select))
            {
                // Remove '*' for get rid of inefficient construction of price data
                foreach($select as $k => $v)
                {
                    if($v === '*')
                    {
                        $selectAll = true;
                        unset($select[$k]);
                    }
                    else if (preg_match('/^PROPERTY_(\d+|\*)$/', $v))
                    {
                        $propertiesSelect[] = $v;
                        unset($select[$k]);
                    }
                }
            }

            if (!empty($propertiesSelect) && empty($select) && !$selectAll)
                $select = array('ID');

            if(empty($select))
            {
                $priceSelect = array('PRICE', 'CURRENCY_ID');
                $vatSelect = array('VAT_ID', 'VAT_INCLUDED', 'MEASURE');
            }
            else
            {
                $priceSelect = array();
                $vatSelect = array();

                $select = CCrmProductTools::DistributeProductSelect($select, $priceSelect, $vatSelect);
            }

            $catalogSelect = array_merge($priceSelect, $vatSelect);
            $enableCatalogData = !empty($catalogSelect);
        }
        $filter['CATALOG_ID'] = $catalogID;
        $dbResult = CCrmProductTools::GetList($order, $filter, $select, $navigation);
        if(!$enableCatalogData)
        {
            $result = array();
            $fieldsInfo = $this->getFieldsInfo();
            while($fields = $dbResult->Fetch())
            {
                $selectedFields = array();
                if (!empty($select))
                {
                    $selectedFields['ID'] = $fields['ID'];
                    foreach ($select as $k)
                        $selectedFields[$k] = &$fields[$k];
                    $fields = &$selectedFields;
                }
                unset($selectedFields);

                $this->getProperties($catalogID, $fields, $propertiesSelect);
                $this->externalizeFields($fields, $fieldsInfo);
                $result[] = $fields;
            }
        }
        else
        {
            $itemMap = array();
            $itemIDs = array();
            while($fields = $dbResult->Fetch())
            {
                $selectedFields = array();
                if (!empty($select))
                {
                    $selectedFields['ID'] = $fields['ID'];
                    foreach ($select as $k)
                        $selectedFields[$k] = &$fields[$k];
                    $fields = &$selectedFields;
                }
                unset($selectedFields);

                foreach ($catalogSelect as $fieldName)
                {
                    $fields[$fieldName] = null;
                }

                $itemID = isset($fields['ID']) ? intval($fields['ID']) : 0;
                if($itemID > 0)
                {
                    $itemIDs[] = $itemID;
                    $itemMap[$itemID] = $fields;
                }

            }
            CCrmProductTools::ObtainPricesVats($itemMap, $itemIDs, $priceSelect, $vatSelect, true);

            $result = array_values($itemMap);
            $fieldsInfo = $this->getFieldsInfo();
            foreach($result as &$fields)
            {
                $this->getProperties($catalogID, $fields, $propertiesSelect);
                $this->externalizeFields($fields, $fieldsInfo);
            }
            unset($fields);
        }

        return CCrmRestService::setNavData($result, $dbResult);
    }
    public static function processEvent(array $arParams, array $arHandler)
    {
        $eventName = $arHandler['EVENT_NAME'];
        switch (strtolower($eventName))
        {
            case 'oncrmproductadd':
            case 'oncrmproductupdate':
            	{
                $ID = isset($arParams[0]) ? (int)$arParams[0] : 0;

                if($ID <= 0)
                {
                    throw new RestException("Could not find entity ID in fields of event \"{$eventName}\"");
                }

                $fields = CCrmProductTools::GetByID($ID);
//                $mxResult = CCatalogSku::GetProductInfo(
//                    $ID
//                );
//                if($mxResult){
//                    throw new RestException("offer");
//                }
//                $catalogID = is_array($fields) && isset($fields['CATALOG_ID']) ? (int)$fields['CATALOG_ID'] : 0;
//                if($catalogID !== CCrmCatalog::GetDefaultID())
//                {
//                    throw new RestException("Outside CRM product event is detected");
//                }
                return array('FIELDS' => array('ID' => $ID));
            }
                break;
            case 'oncrmproductdelete':
            {
                $fields = isset($arParams[0]) && is_array($arParams[0]) ? $arParams[0] : array();
                $ID = isset($fields['ID']) ? (int)$fields['ID'] : 0;

                if($ID <= 0)
                {
                    throw new RestException("Could not find entity ID in fields of event \"{$eventName}\"");
                }

//                $catalogID = isset($fields['IBLOCK_ID']) ? (int)$fields['IBLOCK_ID'] : 0;
//                if($catalogID !== CCrmCatalog::GetDefaultID())
//                {
//                    throw new RestException("Outside CRM product event is detected");
//                }
                return array('FIELDS' => array('ID' => $ID));
            }
                break;
            default:
                throw new RestException("The Event \"{$eventName}\" is not supported in current context");
        }
    }
    protected function innerAdd(&$fields, &$errors, array $params = null)
    {
        if(!is_array($fields))
        {
            throw new RestException("The parameter 'fields' must be array.");
        }

        if(!CModule::IncludeModule('iblock'))
        {
            throw new RestException('Could not load iblock module.');
        }

        if(!CCrmProduct::CheckCreatePermission())
        {
            $errors[] = 'Access denied.';
            return false;
        }

        if(isset($fields['CATALOG_ID'])){
            $catalogID = $fields['CATALOG_ID'];
        }else{
            $catalogID = intval(CCrmCatalog::EnsureDefaultExists());
        }


        if($catalogID <= 0)
        {
            $errors[] = 'Default catalog is not exists.';
            return false;
        }


        // Product properties
        $this->initializePropertiesInfo($catalogID);
        $propertyValues = array();
        foreach ($this->properties as $propId => $property)
        {
            if (isset($fields[$propId]))
                $propertyValues[$property['ID']] = $fields[$propId];
            unset($fields[$propId]);
        }
        if(count($propertyValues) > 0)
            $fields['PROPERTY_VALUES'] = $propertyValues;

        $result = CCrmProduct::Add($fields);
        if(!is_int($result))
        {
            $errors[] = CCrmProduct::GetLastError();
        }
        return $result;
    }
    protected function internalizeFields(&$fields, &$fieldsInfo, $options = array())
    {  
        if(isset($fields['CATALOG_ID']))
        	$fieldsInfo['CATALOG_ID'] = $fields['CATALOG_ID'];
        if(isset($fields['CATALOG_ID']))
        	$fieldsInfo['CATALOG_ID'] = $fields['CATALOG_ID'];
        if(!is_array($fields))
        {
            return;
        }

        if(!is_array($options))
        {
            $options = array();
        }

        $ignoredAttrs = isset($options['IGNORED_ATTRS']) ? $options['IGNORED_ATTRS'] : array();
        if(!in_array(CCrmFieldInfoAttr::Hidden, $ignoredAttrs, true))
        {
            $ignoredAttrs[] = CCrmFieldInfoAttr::Hidden;
        }
        if(!in_array(CCrmFieldInfoAttr::ReadOnly, $ignoredAttrs, true))
        {
            $ignoredAttrs[] = CCrmFieldInfoAttr::ReadOnly;
        }

        $multifields = array();
        foreach($fields as $k => $v)
        {
            $info = isset($fieldsInfo[$k]) ? $fieldsInfo[$k] : null;
            if(!$info)
            {
                unset($fields[$k]);
                continue;
            }
            
            $attrs = isset($info['ATTRIBUTES']) ? $info['ATTRIBUTES'] : array();
            $isMultiple = in_array(CCrmFieldInfoAttr::Multiple, $attrs, true);

            $ary = array_intersect($ignoredAttrs, $attrs);
            if(!empty($ary))
            {
                unset($fields[$k]);
                continue;
            }

            $fieldType = isset($info['TYPE']) ? $info['TYPE'] : '';
            if($fieldType === 'date' || $fieldType === 'datetime')
            {
                if($v === '')
                {
                    $date = '';
                }
                else
                {
                    $date = $fieldType === 'date'
                        ? CRestUtil::unConvertDate($v) : CRestUtil::unConvertDateTime($v, true);
                }

                if($isMultiple)
                {
                    if(!is_array($date))
                    {
                        $date = array($date);
                    }

                    $dates = array();
                    foreach($date as $item)
                    {
                        if(is_string($item))
                        {
                            $dates[] = $item;
                        }
                    }

                    if(!empty($dates))
                    {
                        $fields[$k] = $dates;
                    }
                    else
                    {
                        unset($fields[$k]);
                    }
                }
                elseif(is_string($date))
                {
                    $fields[$k] = $date;
                }
                else
                {
                    unset($fields[$k]);
                }
            }
            elseif($fieldType === 'file')
            {
                $this->tryInternalizeFileField($fields, $k, $isMultiple);
            }
            elseif($fieldType === 'webdav')
            {
                $this->tryInternalizeWebDavElementField($fields, $k, $isMultiple);
            }
            elseif($fieldType === 'diskfile')
            {
                $this->tryInternalizeDiskFileField($fields, $k, $isMultiple);
            }
            elseif($fieldType === 'crm_multifield')
            {
                $this->tryInternalizeMultiFields($fields, $k, $multifields);
            }
            elseif($fieldType === 'product_file')
            {
                $this->tryInternalizeProductFileField($fields, $k);
            }
            elseif($fieldType === 'product_property')
            {
                $this->tryInternalizeProductPropertyField($fields, $fieldsInfo, $k);
            }
        }

        if(!empty($multifields))
        {
            $fields['FM'] = $multifields;
        }
//        var_dump($fields);die;
    }
    private function  GetPropsTypesByOperationsTools($userType = false, $arOperations = array())
    {
        if (!is_array($arOperations))
            $arOperations = array(strval($arOperations));

        $methodByOperation = array(
            'view' => 'GetPublicViewHTML',
            'edit' => 'GetPublicEditHTML',
            'filter' => 'GetPublicFilterHTML',
            'import' => 'GetPublicEditHTML',
            'rest' => 'GetPublicEditHTML',
        );

        $whiteListByOperation = array(
            'view' => array(),
            'edit' => array(),
            'filter' => array(),
            'import' => array(
                'S:HTML',
                'S:Date',
                'S:DateTime',
                'S:employee',
                'S:map_yandex',
                'S:ECrm',
                'S:Money',
                'N:Sequence',
                'E:SKU'
            ),
            'rest' => array(
                'S:HTML',
                'S:Date',
                'S:DateTime',
                'S:employee',
                'S:map_yandex',
                'S:ECrm',
                'S:Money',
                'E:EList',
                'N:Sequence'
            )
        );

        $blackList = array(
            'S:DiskFile',
            'G:SectionAuto',
            'E:EAutocomplete'
        );

        $arUserTypeList = CIBlockProperty::GetUserType($userType);

//        if (!empty($arOperations))
//        {
//            foreach ($arUserTypeList as $key => $item)
//            {
//                $skipNumber = count($arOperations);
//                $skipCount = 0;
//                foreach ($arOperations as $operation)
//                {
//                    if (!isset($methodByOperation[$operation])
//                        || !array_key_exists($methodByOperation[$operation], $item)
//                        || (
//                            in_array($item['PROPERTY_TYPE'].':'.$key, $blackList, true)
//                            || is_array($whiteListByOperation[$operation])
//                            && count($whiteListByOperation[$operation]) > 0
//                            && !in_array($item['PROPERTY_TYPE'].':'.$key, $whiteListByOperation[$operation], true)
//                        ))
//                    {
//                        $skipCount++;
//                    }
//                }
//                if ($skipNumber <= $skipCount)
//                    unset($arUserTypeList[$key]);
//            }
//        }

        return $arUserTypeList;
    }
    private function GetPropsTools($catalogID, $arPropUserTypeList = array(), $arOperations = array())
    {
        if (!is_array($arOperations))
            $arOperations = array(strval($arOperations));

        $arProps = array();
        $catalogID = intval($catalogID);
        
        // validate operations list
        $validOperations = array(
            'view',
            'edit',
            'filter',
            'import',
            'rest'
        );
        $validatedOperations = array();
        foreach ($arOperations as $operationName)
        {
            if (in_array(strval($operationName), $validOperations, true))
                $validatedOperations[] = $operationName;
        }
        $arOperations = $validatedOperations;
        unset($validatedOperations, $operationName);

        if ($catalogID > 0)
        {
            $propsFilter = array(
                'IBLOCK_ID' => $catalogID,
                'ACTIVE' => 'Y',
                'CHECK_PERMISSIONS' => 'N',
                '!PROPERTY_TYPE' => 'G'
            );

            $bImport = false;
            foreach ($arOperations as $operationName)
            {
                if ($operationName === 'import')
                {
                    $bImport = true;
                }
                else
                {
                    $bImport = false;
                    break;
                }
            }

            $dbRes = CIBlockProperty::GetList(
                array('SORT' => 'ASC', 'ID' => 'ASC'),
                $propsFilter
            );
            while ($arProp = $dbRes->Fetch())
            {
//				if (
//					(isset($arProp['USER_TYPE']) && !empty($arProp['USER_TYPE'])
//						&& !array_key_exists($arProp['USER_TYPE'], $arPropUserTypeList))
//					|| (
//						$bImport
//						&& (
//							($arProp['PROPERTY_TYPE'] === 'E'
//								&& (!isset($arProp['USER_TYPE']) || empty($arProp['USER_TYPE'])))
//							|| ($arProp['PROPERTY_TYPE'] === 'E'
//								&& isset($arProp['USER_TYPE']) && $arProp['USER_TYPE'] === 'EList')
//						)
//					)
//				)
//				{
//					continue;
//				}
                $propID = 'PROPERTY_' . $arProp['ID'];
                $arProps[$propID] = $arProp;
            }
        }

        return $arProps;
    }
    public function initializePropertiesInfo($catalogID)
    {
	    $query = $this->getServer()->getQuery();
	    if($query['id'] > 0)
	    {
	    	$elem = \Bitrix\Iblock\ElementTable::getList(
	    			array(
	    					'filter' => array('ID' => $query['id']),
	    					'select' => array('IBLOCK_ID'),
	    					'limit' => 1
	    			)
	    			)->fetch();
	    			
	    	if($elem['IBLOCK_ID'] > 0)
	    	{
	    		$catalogID = $elem['IBLOCK_ID'];
	    	}
	    }

        if ($this->userTypes === null)
            $this->userTypes = $this->GetPropsTypesByOperationsTools(false, 'rest');
        if ($this->properties === null)
            $this->properties = $this->GetPropsTools($catalogID, $this->userTypes);
    }
    protected function getFieldsInfo()
    {
        if(!CModule::IncludeModule('iblock'))
        {
            throw new RestException('Could not load iblock module.');
        }

        if(!$this->FIELDS_INFO)
        {
            $this->FIELDS_INFO = CCrmProduct::GetFieldsInfo();
            $this->preparePropertyFieldsInfo($this->FIELDS_INFO);
        }
        return $this->FIELDS_INFO;
    }
    protected function preparePropertyFieldsInfo(&$fieldsInfo)
    {
    	
        $catalogID = CCrmCatalog::GetDefaultID();
        if($catalogID <= 0)
            return;
            
        $this->initializePropertiesInfo($catalogID);
        foreach($this->properties as $propertyName => $propertyInfo)
        {
            $propertyType = $propertyInfo['PROPERTY_TYPE'];
            $info = array(
                'TYPE' => 'product_property',
                'PROPERTY_TYPE' => $propertyType,
                'USER_TYPE' => $propertyInfo['USER_TYPE'],
                'ATTRIBUTES' => array(CCrmFieldInfoAttr::Dynamic),
                'NAME' => $propertyInfo['NAME']
            );

            $isMultuple = isset($propertyInfo['MULTIPLE']) && $propertyInfo['MULTIPLE'] === 'Y';
            $isRequired = isset($propertyInfo['IS_REQUIRED']) && $propertyInfo['IS_REQUIRED'] === 'Y';
            if($isMultuple || $isRequired)
            {
                if($isMultuple)
                    $info['ATTRIBUTES'][] = CCrmFieldInfoAttr::Multiple;
                if($isRequired)
                    $info['ATTRIBUTES'][] = CCrmFieldInfoAttr::Required;
            }

            if ($propertyInfo['PROPERTY_TYPE'] === 'L')
            {
                $values = array();
                $resEnum = CIBlockProperty::GetPropertyEnum($propertyInfo['ID'], array('SORT' => 'ASC','ID' => 'ASC'));
                while($enumValue = $resEnum->Fetch())
                {
                    $values[intval($enumValue['ID'])] = array(
                        'ID' => $enumValue['ID'],
                        'VALUE' => $enumValue['VALUE']
                    );
                }
                $info['VALUES'] = $values;
            }

            $fieldsInfo[$propertyName] = $info;
        }
    }
    public function getProperties($catalogID, &$fields, $propertiesSelect)
    {
    	
        if ($catalogID <= 0)
            return;

        if(!is_array($fields))
        {
            throw new RestException("The parameter 'fields' must be array.");
        }

        $productID = isset($fields['ID']) ? intval($fields['ID']) : 0;

        if ($productID <= 0)
            return;

        $this->initializePropertiesInfo($catalogID);

        $selectAll = false;
        foreach($propertiesSelect as $k => $v)
        {
            if($v === 'PROPERTY_*')
            {
                $selectAll = true;
                break;
            }
        }
        $propertyValues = array();
        if ($productID > 0 && count($this->properties) > 0)
        {
            $rsProperties = CIBlockElement::GetProperty(
                $catalogID,
                $productID,
                array(
                    'sort' => 'asc',
                    'id' => 'asc',
                    'enum_sort' => 'asc',
                    'value_id' => 'asc',
                ),
                array(
                    'ACTIVE' => 'Y',
                    'EMPTY' => 'N',
                    'CHECK_PERMISSIONS' => 'N'
                )
            );

            while ($property = $rsProperties->Fetch())
            {

                if (isset($property['USER_TYPE']) && !empty($property['USER_TYPE'])
                    && !array_key_exists($property['USER_TYPE'], $this->userTypes))
                    continue;

                $propId = 'PROPERTY_' . $property['ID'];
                if(!isset($propertyValues[$propId]))
                    $propertyValues[$propId] = array();
                $propertyValues[$propId][] =
                    array('VALUE_ID' => $property['PROPERTY_VALUE_ID'], 'VALUE' => $property['VALUE']);
            }
            unset($rsProperties, $property, $propId);
        }
        foreach ($this->properties as $propId => $prop)
        {
            if ($selectAll || in_array($propId, $propertiesSelect, true))
            {
                $value = null;
                if (isset($propertyValues[$propId]))
                {
                    if ($prop['MULTIPLE'] === 'Y')
                        $value = $propertyValues[$propId];
                    else if (count($propertyValues[$propId]) > 0)
                        $value = end($propertyValues[$propId]);
                }
                $fields[$propId] = $value;
            }
        }
    }
}


?>