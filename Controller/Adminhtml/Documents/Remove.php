<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\Controller\Adminhtml\Documents;

use GhostUnicorns\Docs\Model\DocsManager;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class Remove extends Action
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DocsManager
     */
    private $docsManager;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param DocsManager $docsManager
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        DocsManager $docsManager,
        ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->docsManager = $docsManager;
        $this->messageManager = $messageManager;

        parent::__construct($context);
    }

    /**
     * @inheirtDoc
     */
    public function execute()
    {
        try {
            $docId = $this->request->getParam('doc_id');
            $entityType = $this->request->getParam('entity_type');
            $entityId = $this->request->getParam('entity_id');
            $this->docsManager->removeDoc($docId, $entityType, $entityId);
            $this->_redirect('sales/order/view/order_id/' . $entityId, ['_current' => true]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong deleting docs, please checks logs!'));
            $this->_redirect('sales/order/view/order_id/' . $entityId, ['_current' => true]);
        }
    }
}
