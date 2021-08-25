<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\Block\Adminhtml\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Text\ListText;

class OrderDocs extends ListText implements TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Documents');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Documents');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
