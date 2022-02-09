
BX.Catalog.ProductSearchDialog.method = function(methodName, f) {
    if (typeof f != "undefined")
        this.prototype[methodName] = f;
    return this.prototype[methodName];
}
BX.Catalog.ProductSearchDialog.method("SelEl", function(arParams, scope){
    BX.Crm.ProductSearchDialog.prototype.SelEl(arParams, scope);
    BX.removeClass(scope, 'row-sku-selected');
    if (typeof arParams['quantity'] === 'undefined')
        arParams['quantity'] = 1;
    var qtyElement = BX(this.tableId+'_qty_'+arParams['id']);
    if (!!qtyElement)
        arParams['quantity'] = qtyElement.value;

    if (!!this.event)
    {
        if (typeof(arParams.IBLOCK_ID) === 'undefined')
            arParams.IBLOCK_ID = this.getIblockId();
        BX.onCustomEvent(this.event, [arParams]);
    }
});
BX.CrmProductSearchDialogWindow.method = function(methodName, f) {
    if (typeof f != "undefined")
        this.prototype[methodName] = f;
    return this.prototype[methodName];
}
BX.CrmProductSearchDialogWindow.method("show", function(){
    BX.ajax({
        method: "GET",
        dataType: 'html',
        url: '/bitrix/tools/sale/product_search_dialog.php?lang=ru&LID=s1&caller=order_edit&func_name=BX.Sale.Admin.OrderBasketObj.getParamsByProductId&STORE_FROM_ID=0&bxsender=core_window_cdialog',//this._settings.content_url,
        data: {},
        skipAuthCheck: true,
        onsuccess: BX.delegate(function(data) {
            this.setContent(data || "&nbsp;");
            this.showWindow();
            var form = document.querySelector('form[name="tbl_product_search_order_edit_find_form"] table');
            form.style.display = 'none';
        }, this),
        onfailure: BX.delegate(function() {
            if (typeof(this._settings.showWindowHandler) === "function")
                this._settings.showWindowHandler();
        }, this)
    });


});
