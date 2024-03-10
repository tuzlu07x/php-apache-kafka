<?php

namespace Fatihtuzlu\PHPKafkaCDC;

use RdKafka\Conf;

class Kafka
{
    public function __construct(private Conf $conf)
    {
    }

    /**
     * Set the bootstrap server for the Kafka connection.
     *
     * @param string $bootstrapServerName The hostname and port of the Kafka broker.
     * @return void
     */
    public function setBootstrapServer(string $bootstrapServerName): void
    {
        $this->conf->set('bootstrap.servers', $bootstrapServerName);
    }

    /**
     * Set the group ID and topic for the consumer.
     *
     * @param string $groupId The ID of the consumer group.
     * @return void
     */
    public function setGroupIdAndTopic(string $groupId): void
    {
        $this->conf->set('group.id', $groupId);
    }

    /**
     * Set the group ID for the consumer.
     *
     * @param string $groupId The ID of the consumer group.
     * @return void
     */
    public function setGroupId(string $groupId): void
    {
        $this->conf->set('group.id', $groupId);
    }

    /**
     * Get the internal configuration object.
     *
     * @return RdKafka\Conf The RdKafka configuration object.
     */
    public function getConfig(): Conf
    {
        return $this->conf;
    }

    /**
     * Enable debugging for the Kafka connection.
     *
     * @return void
     */
    public function debug(): void
    {
        $this->conf->set('log_level', (string) LOG_DEBUG);
        $this->conf->set('debug', 'all');
    }
}
