# bitrix version 15.0.16

Свои условия в скидках и правилах работы с корзиной Битрикс bitrix

Делаем скидку по наличию на складах

Файл include.store.amont.php надо подключить в init.php

По событиям включается скидка

корзина - AddEventHandler("sale", "OnCondSaleControlBuildList", Array("CatalogCondCtrlStoreQuantity", "GetControlDescr"));

каталог - AddEventHandler("catalog", "OnCondCatControlBuildList", Array("CatalogCondCtrlStoreQuantity", "GetControlDescr"));

