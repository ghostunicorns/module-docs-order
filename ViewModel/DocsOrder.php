<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\ViewModel;

use GhostUnicorns\DocsOrder\Model\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class DocsOrder implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param RequestInterface $request
     * @param Config $config
     */
    public function __construct(
        RequestInterface $request,
        Config $config
    ) {
        $this->request = $request;
        $this->config = $config;
    }

    public function getOrderId(): string
    {
        return $this->request->getParam('order_id');
    }

    /**
     * @return bool
     */
    public function showFrontendSection(): bool
    {
        return $this->config->isEnabledOrderFeSection();
    }

    /**
     * @return bool
     */
    public function showUploadButton(): bool
    {
        return $this->config->isEnabledOrderUploadFileFeSection();
    }
}
