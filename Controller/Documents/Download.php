<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\Controller\Documents;

use GhostUnicorns\Docs\Model\DocsManager;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;

class Download extends Action
{
    /**
     * @var DocsManager
     */
    private $docsManager;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param DocsManager $docsManager
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        DocsManager $docsManager,
        RequestInterface $request
    ) {
        parent::__construct($context);
        $this->docsManager = $docsManager;
        $this->fileFactory = $fileFactory;
        $this->request = $request;
    }

    /**
     * @inheirtDoc
     */
    public function execute()
    {
        $docId = $this->request->getParam('doc_id');
        $entityId = $this->request->getParam('entity_id');
        $doc = $this->docsManager->getDocById($docId);

        if (empty($doc)) {
            $this->messageManager->addErrorMessage(__('Something went wrong downloading docs, please checks logs!'));
            return $this->_redirect('sales/order/view/order_id/' . $entityId, ['_current' => true]);
        }

        if (!array_key_exists('file_name', $doc) || $doc['file_name'] === '') {
            $this->messageManager->addErrorMessage(__('Something went wrong downloading docs, please checks logs!'));
            return $this->_redirect('sales/order/view/order_id/' . $entityId, ['_current' => true]);
        }

        if (!array_key_exists('content', $doc) || $doc['content'] === '') {
            $this->messageManager->addErrorMessage(__('Something went wrong downloading docs, please checks logs!'));
            return $this->_redirect('sales/order/view/order_id/' . $entityId, ['_current' => true]);
        }

        try {
            return $this->fileFactory->create(
                $doc['file_name'],
                [
                    'type' => 'string',
                    'value' => $doc['content'],
                    'rm' => true
                ],
                DirectoryList::VAR_DIR
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong downloading docs, please checks logs!'));
            return $this->_redirect('sales/order/view/order_id/' . $entityId, ['_current' => true]);
        }
    }
}
