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

namespace Dss\ChangeCustomerPassword\Command;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerChangePasswordCommand extends Command
{
    /**
     * CustomerChangePasswordCommand constructor
     *
     * @param CustomerFactory $customerFactory
     * @param StoreManagerInterface $storeManager
     * @param CustomerResource $resource
     * @param AppState $state
     */
    public function __construct(
        private CustomerFactory $customerFactory,
        private StoreManagerInterface $storeManager,
        private CustomerResource $resource,
        private AppState $state
    ) {
    }

    /**
     * Configure password
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('customer:change-password');
        $this->setDescription('Set a customers password');
        $this->addOption(
            'website',
            'w',
            InputOption::VALUE_OPTIONAL,
            'Website code if customer accounts are website scope'
        );
        $this->addArgument('email', InputArgument::REQUIRED, 'Customer Email');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password to set');
    }

    /**
     * Get Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        try {
            $this->appState->setAreaCode(Area::AREA_ADMINHTML);
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
        } catch (LocalizedException $exception) {
        }

        $customer = $this->getCustomerByEmail($this->getEmail());
        $customer->setPassword($this->getPassword());
        $this->customerResource->save($customer);
        $output->writeln(sprintf('Updated password for customer "%s".', $this->getEmail()));
    }

    /**
     * Get input email
     *
     * @return string
     */
    private function getEmail(): string
    {
        return $this->input->getArgument('email') ?? '';
    }

    /**
     * Get input password
     *
     * @return string
     */
    private function getPassword(): string
    {
        return $this->input->getArgument('password') ?? '';
    }

    /**
     * Get input website code
     *
     * @return string
     */
    private function getWebsiteCode(): string
    {
        return $this->input->getOption('website') ?? '';
    }

    /**
     * Get website Id by code
     *
     * @param string $code
     * @return int
     * @throws LocalizedException
     */
    private function getWebsiteIdByCode(string $code): int
    {
        $website = $this->storeManager->getWebsite($code);
        if (!$website->getId()) {
            throw new \InvalidArgumentException(sprintf('No website with ID "%s" found.', $code));
        }

        return (int)$website->getId();
    }

    /**
     * Get customer by provided email
     *
     * @param string $email
     * @return Customer
     * @throws LocalizedException
     */
    private function getCustomerByEmail(string $email): Customer
    {
        $customer = $this->customerFactory->create();
        if ($this->getWebsiteCode()) {
            $websiteId = $this->getWebsiteIdByCode($this->getWebsiteCode());
            $customer->setWebsiteId($websiteId);
        }
        $this->customerResource->loadByEmail($customer, $email);
        if (!$customer->getId()) {
            throw new \InvalidArgumentException(sprintf('No customer with email "%s" found.', $this->getEmail()));
        }

        return $customer;
    }
}
