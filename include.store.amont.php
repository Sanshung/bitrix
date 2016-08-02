<?php

use Bitrix\Main\Loader;
Loader::includeModule('catalog');

AddEventHandler("sale", "OnCondSaleControlBuildList", Array("CatalogCondCtrlStoreQuantity", "GetControlDescr"));//корзина
AddEventHandler("catalog", "OnCondCatControlBuildList", Array("CatalogCondCtrlStoreQuantity", "GetControlDescr"));//каталог

class CatalogCondCtrlStoreQuantity extends CGlobalCondCtrlComplex
{
    public static function GetClassName()
    {
        return __CLASS__;
    }
    /**
     * @return string|array
     */
    public static function GetControlID()
    {
        return array('CondSkladType');
    }

    public static function GetControlShow($arParams)
    {
        $arControls = static::GetControls();
        $arResult = array(
            'controlgroup' => true,
            'group' =>  false,
            'label' => 'Склады',
            'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'children' => array()
        );
        foreach ($arControls as &$arOneControl)
        {
            $arResult['children'][] = array(
                'controlId' => $arOneControl['ID'],
                'group' => false,
                'label' => $arOneControl['LABEL'],
                'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
                'control' => array(
                    array(
                        'id' => 'prefix',
                        'type' => 'prefix',
                        'text' => $arOneControl['PREFIX']
                    ),
                    static::GetLogicAtom($arOneControl['LOGIC']),
                    static::GetValueAtom($arOneControl['JS_VALUE'])
                )
            );
        }
        if (isset($arOneControl))
            unset($arOneControl);

        return $arResult;
    }

    /**
     * @param bool|string $strControlID
     * @return bool|array
     */
    public static function GetControls($strControlID = false)
    {
		
		$resStore = CCatalogStore::GetList(Array('ID'=>'ASC'),Array());
		while($resStores = $resStore->Fetch())
		{
			$resStoresArr[$resStores['ID']]=$resStores['TITLE'].' ('.$resStores['SITE_ID'].')';
		}
		
        $arControlList = array(
			'CondSkladType' => array(
				'ID' => 'CondSkladType',
				'FIELD' => 'SKLAD_ID',
				'FIELD_TYPE' => 'int',
				'LABEL' => 'Склады (присутствие)',
				'PREFIX' => 'поле Склады (присутствие)',
				'LOGIC' => static::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'select',
					'multiple' => 'Y',
					'values' => $resStoresArr,
					'show_value' => 'Y'
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'list'
				)
			),
        );

        foreach ($arControlList as &$control)
        {
            if (!isset($control['PARENT']))
                $control['PARENT'] = true;

            //$control['EXIST_HANDLER'] = 'Y';

            //$control['MODULE_ID'] = 'mymodule';

            $control['MULTIPLE'] = 'N';
            //$control['GROUP'] = 'N';
        }
        unset($control);

        if ($strControlID === false)
        {
            return $arControlList;
        }
        elseif (isset($arControlList[$strControlID]))
        {
            return $arControlList[$strControlID];
        }
        else
        {
            return false;
        }
    }
    
    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        $strResult = '';
        $resultValues = array();
        $arValues = false;
		
		if($arOneCondition['logic']=='Equal')
		{
			$logic='true';
		}
		else
		{
			$logic='false';
		}
		
		$stoteArrstr = 'Array(';
		foreach($arOneCondition["value"] as $k => $v){
			$stoteArrstr .= "$k=>$v,";
		}
		$stoteArrstr .= ')';
		
		$strResult  = '(CatalogCondCtrlStoreQuantity::qStoreC($arProduct,'.$stoteArrstr.'))=='.$logic;
		//prn_($strResult2,true);
	
        return (!$boolError ? $strResult : false);		
		
    }	
	public static function qStoreC($arProduct,$arrStore=array())
	{
		$rsStore=CCatalogStoreProduct::GetList(array(),array("PRODUCT_ID"=>$arProduct["ID"],"STORE_ID"=>$arrStore),false,false,array("AMOUNT")); 
		while($arStore = $rsStore->Fetch())
		{
			$quantity = $quantity + $arStore["AMOUNT"];
		}
		return($quantity?true:false);
	}
}
?>
