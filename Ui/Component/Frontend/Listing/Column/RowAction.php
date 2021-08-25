<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\Ui\Component\Frontend\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class RowAction extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['download'] = [
                    'href' =>
                        $this->urlBuilder->getUrl(
                            'docsorder/documents/download',
                            [
                                'doc_id' => $item['id'],
                                'entity_id' => $item['entity_id'],
                                'entity_type' => $item['entity_type']
                            ]
                        ),
                    'label' => __('Download'),
                    'hidden' => false
                ];
                $item[$this->getData('name')]['remove'] = [
                    'href' =>
                        $this->urlBuilder->getUrl(
                            'docsorder/documents/remove',
                            [
                                'doc_id' => $item['id'],
                                'entity_id' => $item['entity_id'],
                                'entity_type' => $item['entity_type']
                            ]
                        ),
                    'label' => __('Remove'),
                    'hidden' => false,
                    'confirm' => [
                        'title' => __('Remove docs procedure'),
                        'message' => __('Are you sure you want to delete this document?')
                    ]
                ];
            }
        }

        return $dataSource;
    }
}
