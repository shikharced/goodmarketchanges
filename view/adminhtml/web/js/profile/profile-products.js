/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            profileProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            gridJsMassObject = window.goodmarket_profile_products_massactionJsObject,
            tabIndex = 1000;

        if (config.hasOwnProperty('filters') && config.filters !== '') {
            // Setting profile filters in grid
            var filters = config.filters.split("&");
            for (var i = 0; i < filters.length; i++) {
                var index = filters[i].split("=")[0];
                var value = filters[i].split("=")[1];
                var attribute = $("goodmarket_profile_products_filter_"+index);
                if (attribute !== null && attribute !== undefined) {
                    $("goodmarket_profile_products_filter_"+index).value = value;
                }
            }

            // Initialising filters from profile
            gridJsObject.doFilter();
        }


        $('in_profile_products').value = Object.toJSON(profileProducts);

        /**
         * Register Profile Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerProfileProduct(grid, element, checked) {
            if (checked && element.value != 'on') {
                profileProducts.set(element.value, element.value);
            } else {
                profileProducts.unset(element.value);
            }
            $('in_profile_products').value = Object.toJSON(profileProducts);
            grid.reloadParams = {
                'selected_products[]': JSON.stringify(profileProducts.keys())
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function profileProductRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change product position
         *
         * @param {String} event
         */
        function positionChange(event) {
            var element = Event.element(event);
            if (element && element.checkboxElement && element.checkboxElement.checked) {
                profileProducts.set(element.checkboxElement.value, element.value);
                $('in_profile_products').value = Object.toJSON(profileProducts);
            }
        }

        /**
         * Initialize profile product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function profileProductRowInit(grid, row) {
            let checkbox = $(row).getElementsByClassName('checkbox')[0];
            let position = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
            }
        }

        /**
         * @param {Function} callback
         */
        function saveFilters (callback) {

            var filters = $$(
                    '#' + this.containerId + ' [data-role="filter-form"] input',
                    '#' + this.containerId + ' [data-role="filter-form"] select'
                ),
                elements = [],
                i;

            for (i in filters) {
                if (filters[i].value && filters[i].value.length) {
                    elements.push(filters[i]);
                }
            }
            $('profile_products_filters').value = Form.serializeElements(elements);

            this.reload(
                this.addVarToUrl(this.filterVar, Base64.encode(Form.serializeElements(elements))),
                callback
            );

        }

        function customSelectAll() {
            gridJsMassObject.setCheckedValues((gridJsMassObject.useSelectAll ? gridJsMassObject.getGridIds() : gridJsMassObject.getCheckboxesValuesAsString()));
            gridJsMassObject.checkCheckboxes();
            gridJsMassObject.updateCount();
            gridJsMassObject.clearLastChecked();
            setIdsInField();
            return false;
        }

        function customUnselectAll() {
            gridJsMassObject.setCheckedValues('');
            gridJsMassObject.checkCheckboxes();
            gridJsMassObject.updateCount();
            gridJsMassObject.clearLastChecked();
            setIdsInField();
            return false;
        }

        function customSelectVisible() {
            gridJsMassObject.setCheckedValues(gridJsMassObject.getCheckboxesValuesAsString());
            gridJsMassObject.checkCheckboxes();
            gridJsMassObject.updateCount();
            gridJsMassObject.clearLastChecked();
            setIdsInField();
            return false;
        }

        function customUnselectVisible() {
            gridJsMassObject.getCheckboxesValues().each(function(key){
                gridJsMassObject.checkedString = varienStringArray.remove(key, gridJsMassObject.checkedString);
            }.bind(gridJsMassObject));
            gridJsMassObject.checkCheckboxes();
            gridJsMassObject.updateCount();
            gridJsMassObject.clearLastChecked();
            setIdsInField();
            return false;
        }

        function array_flip( trans )
        {
            var key, tmp_ar = {};
            for ( key in trans )
            {
                if ( trans.hasOwnProperty( key ) )
                {
                    if(trans[key] != '') {
                        tmp_ar[trans[key]] = key;
                    }
                }
            }

            return tmp_ar;
        }

        function setIdsInField()
        {
            var prodIds = gridJsMassObject.getCheckedValues();
            prodIds = array_flip(prodIds.split(","));
            $('in_profile_products').value = Object.toJSON(prodIds);
        }

        gridJsObject.doFilterCallback = saveFilters;
        gridJsObject.rowClickCallback = profileProductRowClick;
        gridJsObject.initRowCallback = profileProductRowInit;
        gridJsObject.checkboxCheckCallback = registerProfileProduct;
        gridJsMassObject.selectAll = customSelectAll;
        gridJsMassObject.unselectAll = customUnselectAll;
        gridJsMassObject.selectVisible = customSelectVisible;
        gridJsMassObject.unselectVisible = customUnselectVisible;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                profileProductRowInit(gridJsObject, row);
            });
        }

    };
});
