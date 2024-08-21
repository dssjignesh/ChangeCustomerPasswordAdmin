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

namespace Dss\ChangeCustomerPassword\Controller\Adminhtml\Password;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

class ChangePwdPost extends Action implements HttpPostActionInterface
{
    /**
     * ChangePwdPost constructor.
     *
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerRegistry $customerRegistry
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        protected CustomerRepositoryInterface $customerRepository,
        protected CustomerRegistry $customerRegistry,
        protected EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
    }

    /**
     * Change pwd action
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = (int)$this->getRequest()->getPost('customer_id');
        $password = trim($this->getRequest()->getPost('new_customer_pwd'));

        if ($customerId) {
            try {
                if (empty($password)) {
                    $this->messageManager->addErrorMessage(__('Password can not be empty'));
                } else {
                    $customer = $this->customerRepository->getById($customerId);
                    $customerSecureRegistry = $this->customerRegistry->retrieveSecureData($customerId);
                    $customerSecureRegistry->setRpToken(null);
                    $customerSecureRegistry->setRpTokenCreatedAt(null);
                    $customerSecureRegistry->setPasswordHash($this->createPasswordHash($password));
                    $this->customerRepository->save($customer, $this->createPasswordHash($password));
                    $this->messageManager->addSuccessMessage(__('Password has been updated successfully.'));
                }

            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Error: %1', $e->getMessage()));
            }

            return $resultRedirect->setPath('*/index/edit', ['id' => $customerId]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Create password hash
     *
     * @param string $password
     * @return string
     */
    protected function createPasswordHash(string $password): string
    {
        return $this->encryptor->getHash($password, true);
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }
}
