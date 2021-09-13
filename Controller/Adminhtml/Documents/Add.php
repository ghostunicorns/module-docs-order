<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\Controller\Adminhtml\Documents;

use GhostUnicorns\Docs\Model\DocsManager;
use GhostUnicorns\Docs\Model\SetDocWithTmpFile;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;

class Add extends Action
{
    const ENTITYTYPE = 'order';

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
     * @var SetDocWithTmpFile
     */
    private $setDocWithTmpFile;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param DocsManager $docsManager
     * @param RequestInterface $request
     * @param SetDocWithTmpFile $setDocWithTmpFile
     */
    public function __construct (
        Action\Context $context,
        FileFactory $fileFactory,
        DocsManager $docsManager,
        RequestInterface $request,
        SetDocWithTmpFile $setDocWithTmpFile
    )
    {
        parent::__construct($context);
        $this->docsManager = $docsManager;
        $this->fileFactory = $fileFactory;
        $this->request = $request;
        $this->setDocWithTmpFile = $setDocWithTmpFile;
    }

    /**
     * @inheirtDoc
     */
    public function execute ()
    {
        $orderId = (string)$this->getRequest()->getParam('entity_id');
        try {
            $file = $this->getRequest()->getFiles('upload_file');

            if (!array_key_exists('name', $file) || $file['name'] === '') {
                $this->messageManager->addErrorMessage(__('Something went wrong during document upload!'));
                return $this->_redirect('sales/order/view/order_id/' . $orderId, ['_current' => true]);
            }

            $this->setDocWithTmpFile->execute(self::ENTITYTYPE, $orderId, $file);

            $this->messageManager->addSuccessMessage(__('Document added!'));

            $this->_redirect(
                'sales/order/view/order_id/' . $orderId,
                ['active_tab' => 'order_docs']
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong downloading docs, please checks logs!'));
            return $this->_redirect('sales/order/view/order_id/' . $orderId, ['active_tab' => 'order_docs']);
        }
    }
}
