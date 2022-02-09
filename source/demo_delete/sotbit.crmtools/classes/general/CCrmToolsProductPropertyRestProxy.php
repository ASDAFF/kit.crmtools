<?
CModule::includeModule('sotbit.crmtools');
class CCrmToolsProductPropertyRestProxy extends CCrmProductPropertyRestProxy {
    protected function innerGetList($order, $filter, $select, $navigation, &$errors)
    {
        if(!CModule::IncludeModule('iblock'))
        {
            throw new RestException('Could not load iblock module.');
        }
        CModule::IncludeModule('catalog');
        /** @var CCrmPerms $userPerms */
        $userPerms = CCrmPerms::GetCurrentUserPermissions();
        if (!$userPerms->HavePerm('CONFIG', BX_CRM_PERM_CONFIG, 'READ'))
        {
            $errors[] = 'Access denied.';
            return false;
        }
        if(isset($filter['IBLOCK_ID']) && $filter['IBLOCK_ID'] == 'offer'){
            $catalog = CCatalogSKU::GetInfoByProductIBlock(CCrmCatalog::EnsureDefaultExists());
            if($catalog)
                 $filter['IBLOCK_ID'] = $catalog['IBLOCK_ID'];
        }
        $userTypes = CCrmProductPropsHelper::GetPropsTypesByOperations(false, 'rest');
        if(!isset($filter['IBLOCK_ID']))
            $filter['IBLOCK_ID'] = intval(CCrmCatalog::EnsureDefaultExists());
        $filter['CHECK_PERMISSIONS'] = 'N';
        $res = CIBlockProperty::GetList($order, $filter);
        $result = array();
        while ($row = $res->Fetch())
        {
            if ($row['PROPERTY_TYPE'] !== 'G'
                && ($row['USER_TYPE'] == '' || array_key_exists($row['USER_TYPE'], $userTypes)))
            {
                $values = null;
                if ($row['PROPERTY_TYPE'] === 'L')
                {
                    $values = array();
                    $resEnum = CIBlockProperty::GetPropertyEnum($row['ID'], array('SORT' => 'ASC','ID' => 'ASC'));
                    while($enumValue = $resEnum->Fetch())
                    {
                        $values[intval($enumValue['ID'])] = array(
                            'ID' => $enumValue['ID'],
                            'VALUE' => $enumValue['VALUE'],
                            'XML_ID' => $enumValue['XML_ID'],
                            'SORT' => $enumValue['SORT'],
                            'DEF' => $enumValue['DEF']
                        );
                    }
                }
                $row['VALUES'] = $values;
                $result[] = $row;
            }
        }
        return $result;
    }
}
?>