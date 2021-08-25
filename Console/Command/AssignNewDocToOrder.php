<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\DocsOrder\Console\Command;

use Exception;
use GhostUnicorns\Docs\Model\DocsManager;
use GhostUnicorns\Docs\Model\SetAreaCode;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Sales\Model\Order;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AssignNewDocToOrder extends Command
{
    const ARGUMENT_NAME = 'filename';

    const ARGUMENT_NAME1 = 'filepath';

    const ARGUMENT_NAME2 = 'increment_id';

    const ENTITY_TYPE = 'order';

    /**
     * @var DocsManager
     */
    private $docsManager;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var SetAreaCode
     */
    private $areaCode;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('order:assign:doc');
        $this->setDescription('Assign new doc to specific order, check it inside var/docs/order folder');
        $this->addArgument(
            self::ARGUMENT_NAME,
            null,
            'filename',
            InputOption::VALUE_REQUIRED
        );
        $this->addArgument(
            self::ARGUMENT_NAME1,
            null,
            'filepath',
            InputOption::VALUE_REQUIRED
        );
        $this->addArgument(
            self::ARGUMENT_NAME2,
            null,
            'increment_id',
            InputOption::VALUE_REQUIRED
        );

        parent::configure();
    }

    /**
     * @param Order $order
     * @param DocsManager $docsManager
     * @param DirectoryList $directoryList
     * @param SetAreaCode $areaCode
     * @param string|null $name
     */
    public function __construct(
        Order $order,
        DocsManager $docsManager,
        DirectoryList $directoryList,
        SetAreaCode $areaCode,
        string $name = null
    ) {
        parent::__construct($name);
        $this->docsManager = $docsManager;
        $this->directoryList = $directoryList;
        $this->order = $order;
        $this->areaCode = $areaCode;
    }

    /**
     * Command utile solo al debug
     * testbase è il nome del file sta sulla cartella var
     * @inheirtDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->areaCode->execute('adminhtml');
            $fileName = $input->getArgument(self::ARGUMENT_NAME);
            $filePath = $input->getArgument(self::ARGUMENT_NAME1);
            $incrementId = $input->getArgument(self::ARGUMENT_NAME2);
            $completePath = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . $filePath;
            $order = $this->order->loadByIncrementId($incrementId);
            $orderId = (string)$order->getId();
            $content = file_get_contents($completePath);

            $this->docsManager->setNewDoc($fileName, $content, self::ENTITY_TYPE, $orderId);
            $output->writeln('<info>Docs added!</info>');
        } catch (Exception $exception) {
            $output->writeln('<error>Something went wrong during docs add, check logs!</error>');
        }
    }
}
