define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function (){
            var status = this._super().initialValue;
            this.fieldDepend(status);
            return this;
        },


        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            this.fieldDepend(value);
            return this._super();
        },

        /**
         * Update field dependency
         *
         * @param {String} value
         */
        fieldDepend: function (value) {
            setTimeout(function () {
                console.log(value);
                var selectType = uiRegistry.get('index = price_percentage');
                var textInput = uiRegistry.get('index = price_fix');
                var attributeType= uiRegistry.get('index = price_attribute');

                // for home page silder
                if(value=='final_price')
                {
                    selectType.hide();
                    textInput.hide();
                    attributeType.hide()
                } else if (value == "plus_fixed") {
                    selectType.hide();
                    textInput.show();
                    attributeType.hide()
                } else if (value == "plus_per") {
                    selectType.show();
                    textInput.hide();
                    attributeType.hide()
                }else if (value == "min_fixed") {
                    selectType.hide();
                    textInput.show();
                    attributeType.hide()
                } else if (value == "min_per") {
                    selectType.show();
                    textInput.hide();
                    attributeType.hide()
                }else if (value == "differ") {
                    selectType.hide();
                    textInput.hide();
                    attributeType.show()
                }
            }, 500);
            return this;
        }
    });
});