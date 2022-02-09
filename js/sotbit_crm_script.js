
function ready() {
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
    if(typeof BX.CrmProductSearchDialogWindow !== undefined){

    }
    if(BX.CrmProductSearchDialogWindow)
        BX.CrmProductSearchDialogWindow.method = function(methodName, f) {
            if (typeof f != "undefined")
                this.prototype[methodName] = f;
            return this.prototype[methodName];
        }
    if(BX.CrmProductSearchDialogWindow)
        BX.CrmProductSearchDialogWindow.method("show", function(){
            BX.ajax({
                method: "GET",
                dataType: 'html',
                url: '/bitrix/services/kitrest/catalog.php?lang=ru&LID=s1&caller=order_edit&func_name=BX.Sale.Admin.OrderBasketObj.getParamsByProductId&STORE_FROM_ID=0&bxsender=core_window_cdialog',//this._settings.content_url,
                data: {},
                skipAuthCheck: true,
                onsuccess: BX.delegate(function(data) {
                    this.setContent(data || "&nbsp;");
                    this.showWindow();
                    BX('tbl_product_search_order_edit_query').focus();
                }, this),
                onfailure: BX.delegate(function() {
                    if (typeof(this._settings.showWindowHandler) === "function")
                        this._settings.showWindowHandler();
                }, this)
            });
            BX.addCustomEvent('onAjaxSuccess', function(){
                var form = document.querySelector('form[name="tbl_product_search_order_edit_find_form"] table');
                if(form)
                    form.style.display = 'none';
                var select = document.querySelector('#tbl_product_search_order_edit_iblock_menu_opener');
                if(select)
                    select.style.cssText = "display: inline-block;z-index: 9999;margin-right: 11px;background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAAFBAMAAAB7tOvrAAAABGdBTUEAALGPC/xhBQAAAA9QTFRF////AAAAYnZnY3dnYnZm8gSibAAAAAJ0Uk5TvwAVzn9PAAAAH0lEQVQI12MwcVExYGAycWJgEHB2EGAQZGYEEgKCAgAkAgJP6GYAPgAAAABJRU5ErkJggg==') no-repeat center;margin-top: 9px;width: 10px;height: 10px;";
            });

        });
}
document.addEventListener("DOMContentLoaded", ready);
