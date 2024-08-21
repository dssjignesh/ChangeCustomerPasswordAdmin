<?php

declare(strict_types=1);

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

namespace Dss\ChangeCustomerPassword\Block\Adminhtml\Customer;

use Magento\Backend\Block\Template;

class PasswordChange extends Template
{
    /**
     * Get current customer id
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return (int)$this->getRequest()->getParam('id');
    }
}
