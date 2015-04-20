<?php

namespace Smart\TextMessageQueue\Doctrine;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Smart\TextMessageQueue\Config;
use Smart\TextMessageQueue\ConfigInterface;
use Smart\TextMessageQueue\TextMessageQueueEntity;

class MappingListener
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config = null)
    {
        if (null !== $config) {
            $this->config = $config;
        }
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config = new Config();
        }

        return $this->config;
    }

    /**
     * @param ConfigInterface $config
     *
     * @return $this
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */

        $classMetadata = $eventArgs->getClassMetadata();
        if ($classMetadata->getName() === TextMessageQueueEntity::class) {
            $tableName = $this->getConfig()->getTableName();
            if (null !== $tableName && isset($classMetadata->table)) {
                $table = $classMetadata->table;
                $table['name'] = $tableName;
                $classMetadata->setPrimaryTable($table);
            }
        }
    }
}
