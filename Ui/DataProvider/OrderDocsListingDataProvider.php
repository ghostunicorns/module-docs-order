<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\Ui\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;

class OrderDocsListingDataProvider implements DataProviderInterface
{
    const ENTITY_TYPE_FILTER = 'order';

    /**
     * Data Provider name
     *
     * @var string
     */
    protected $name;

    /**
     * Data Provider Primary Identifier name
     *
     * @var string
     */
    protected $primaryFieldName;

    /**
     * Data Provider Request Parameter Identifier name
     *
     * @var string
     */
    protected $requestFieldName;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * Provider configuration data
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var ReportingInterface
     */
    protected $reporting;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var SearchCriteria
     */
    protected $searchCriteria;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->request = $request;
        $this->filterBuilder = $filterBuilder;
        $this->name = $name;
        $this->primaryFieldName = $primaryFieldName;
        $this->requestFieldName = $requestFieldName;
        $this->reporting = $reporting;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->meta = $meta;
        $this->data = $data;
        $this->prepareUpdateUrl();
    }

    protected function prepareUpdateUrl()
    {
        if (!isset($this->data['config']['filter_url_params'])) {
            return;
        }
        foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            if ('*' == $paramValue) {
                $paramValue = $this->request->getParam($paramName);
            }

            if ($paramValue) {
                $this->data['config']['update_url'] = sprintf(
                    '%s%s/%s/',
                    $this->data['config']['update_url'],
                    $paramName,
                    $paramValue
                );
                $this->addFilter(
                    $this->filterBuilder->setField($paramName)->setValue($paramValue)->setConditionType('eq')->create()
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        $this->searchCriteriaBuilder->addFilter($filter);
    }

    /**
     * Get Data Provider name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get primary field name
     *
     * @return string
     */
    public function getPrimaryFieldName(): string
    {
        return $this->primaryFieldName;
    }

    /**
     * Get field name in request
     *
     * @return string
     */
    public function getRequestFieldName(): string
    {
        return $this->requestFieldName;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Get field Set meta info
     *
     * @param string $fieldSetName
     * @return array
     */
    public function getFieldSetMetaInfo($fieldSetName): array
    {
        return $this->meta[$fieldSetName] ?? [];
    }

    /**
     * @param string $fieldSetName
     * @return array
     */
    public function getFieldsMetaInfo($fieldSetName): array
    {
        return $this->meta[$fieldSetName]['children'] ?? [];
    }

    /**
     * @param string $fieldSetName
     * @param string $fieldName
     * @return array
     */
    public function getFieldMetaInfo($fieldSetName, $fieldName): array
    {
        return $this->meta[$fieldSetName]['children'][$fieldName] ?? [];
    }

    /**
     * self::setOrder() alias
     *
     * @param string $field
     * @param string $direction
     * @return void
     */
    public function addOrder($field, $direction)
    {
        $this->searchCriteriaBuilder->addSortOrder($field, $direction);
    }

    /**
     * Set Query limit
     *
     * @param int $offset
     * @param int $size
     * @return void
     */
    public function setLimit($offset, $size)
    {
        $this->searchCriteriaBuilder->setPageSize($size);
        $this->searchCriteriaBuilder->setCurrentPage($offset);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->searchResultToOutput($this->getSearchResult());
    }

    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult): array
    {
        $arrItems = [];

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $itemData = $item->getData();
            $arrItems['items'][] = $itemData;
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();
        return $arrItems;
    }

    /**
     * Returns Search result
     *
     * @return SearchResultInterface
     */
    public function getSearchResult(): SearchResultInterface
    {
        return $this->reporting->search($this->getSearchCriteria());
    }

    /**
     * Returns search criteria
     *
     * @return SearchCriteria
     */
    public function getSearchCriteria(): SearchCriteria
    {
        $this->addFilter(
            $this->filterBuilder
                ->setField('entity_type')
                ->setValue('order')
                ->setConditionType('eq')
                ->create()
        );

        if (!isset($this->searchCriteria)) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName($this->name);
        }

        return $this->searchCriteria;
    }

    /**
     * Get config data
     *
     * @return array
     */
    public function getConfigData(): array
    {
        return $this->data['config'] ?? [];
    }

    /**
     * Set data
     *
     * @param mixed $config
     * @return void
     */
    public function setConfigData($config)
    {
        $this->data['config'] = $config;
    }
}
