<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class IsCustomerAssociatedToOrder
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $customerId
     * @param string $orderId
     * @return bool
     */
    public function execute(string $customerId, string $orderId): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTablePrefix() .
            $this->resourceConnection->getTableName('sales_order');

        $where = $connection->quoteInto('entity_id = ?', $orderId) .
            $connection->quoteInto(' and customer_id = ?', $customerId);

        $qry = $connection->select()
            ->from($tableName, ['increment_id'])
            ->where($where);

        return (bool)$connection->fetchOne($qry);
    }
}
