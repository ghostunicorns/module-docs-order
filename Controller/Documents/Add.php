<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\Controller\Documents;

use GhostUnicorns\Docs\Model\SetDocWithTmpFile;
use GhostUnicorns\DocsOrder\Model\Config;
use GhostUnicorns\DocsOrder\Model\ResourceModel\IsCustomerAssociatedToOrder;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Result\PageFactory;

class Add extends Action
{
    const ENTITYTYPE = 'order';

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var UiComponentFactory
     */
    protected $factory;

    /**
     * @var IsCustomerAssociatedToOrder
     */
    private $isCustomerAssociatedToOrder;

    /**
     * @var SetDocWithTmpFile
     */
    private $setDocWithTmpFile;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param Config $config
     * @param SetDocWithTmpFile $setDocWithTmpFile
     * @param IsCustomerAssociatedToOrder $isCustomerAssociatedToOrder
     * @param PageFactory $pageFactory
     * @param UiComponentFactory $factory
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Config $config,
        SetDocWithTmpFile $setDocWithTmpFile,
        IsCustomerAssociatedToOrder $isCustomerAssociatedToOrder,
        PageFactory $pageFactory,
        UiComponentFactory $factory,
        Session $customerSession
    ) {
        $this->pageFactory = $pageFactory;
        $this->isCustomerAssociatedToOrder = $isCustomerAssociatedToOrder;
        $this->factory = $factory;
        $this->customerSession = $customerSession;
        $this->setDocWithTmpFile = $setDocWithTmpFile;
        $this->config = $config;
        return parent::__construct($context);
    }

    /**
     * @inheirtDoc
     */
    public function execute()
    {
        try {
            if ($this->customerSession->isLoggedIn() && $this->config->isEnabledOrderFeSection()) {
                $customerId = (string)$this->customerSession->getCustomerId();
                $orderId = (string)$this->getRequest()->getParam('entity_id');

                //check order relation customer->orderid
                if (!$this->isCustomerAssociatedToOrder->execute($customerId, $orderId)) {
                    return $this->errorRedirect($orderId);
                }

                $file = $this->getRequest()->getFiles('upload_file');

                if (!array_key_exists('name', $file) || $file['name'] === '') {
                    $this->messageManager->addErrorMessage(__('Something went wrong during document upload!'));
                    return $this->errorRedirect($orderId);
                }

                $this->setDocWithTmpFile->execute(self::ENTITYTYPE, $orderId, $file);

                $this->messageManager->addSuccessMessage(__('Document added!'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('sales/order/view/order_id/' . $orderId);
                return $resultRedirect;
            }
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong during document upload!'));
            return $this->errorRedirect();
        }
    }

    /**
     * @param string|null $orderId
     * @return ResultInterface
     */
    private function errorRedirect(string $orderId = null): ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (empty($orderId)) {
            $resultRedirect->setPath('sales/order/history');
        } else {
            $resultRedirect->setPath('sales/order/view/order_id/' . $orderId);
        }

        return $resultRedirect;
    }
}
