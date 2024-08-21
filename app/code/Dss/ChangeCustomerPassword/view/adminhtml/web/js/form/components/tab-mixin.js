/**
 * Digit Software Solutions..
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category   Dss
 * @package    Dss_ChangeCustomerPassword
 * @author     Extension Team
 * @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
 */
define([
    'jquery'
], function (
    $
) {
    'use strict';

    return function (target) {
        return target.extend({
            /**
             * Toggle base on tab
             */
            activate: function () {
                let self = this;
                this._super();
                $('.change-customer-pwd').toggle(self.dataScope === 'customer_edit_tab_view_content');
            }
        });
    };
});
