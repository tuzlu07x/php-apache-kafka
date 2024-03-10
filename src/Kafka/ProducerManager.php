<?php

namespace Fatihtuzlu\PHPKafkaCDC;

use Exception;
use RdKafka\Conf;
use RdKafka\Producer;

class ProducerManager extends Kafka
{
    private Producer $producer;

    public function __construct(
        private string $topic,
        private string $bootstrapServers = "kafka:9092",

    ) {
        $config = new Conf();
        parent::__construct($config);
        $this->setBootstrapServer($bootstrapServers);
        $this->producer = new Producer($config);
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * Set the topic to produce messages to.
     *
     * @param string $topic The topic name.
     * @return self This object for chaining.
     */
    public function setTopic(string $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * Send a message to the configured topic.
     *
     * @param string $message The message to send.
     * @param int $flush The number of messages to buffer before flushing.
     * @param bool $debug Enable debugging for the current request.
     * @throws Exception If an error occurs during message sending.
     * @return void
     */
    public function sendMessages(string $message, int $flush = 1000, bool $debug = false): void
    {
        try {
            if ($debug) $this->debug();
            $this->addBrokers($this->bootstrapServers);
            $topic = $this->producer->newTopic($this->topic);
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
            $this->flush($flush);
            $this->purge(RD_KAFKA_PURGE_F_QUEUE);
            $this->flush($flush);
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
        }
    }

    /**
     * Flush any buffered messages to the Kafka broker.
     *
     * @param int $timeoutMs The timeout in milliseconds for the flush operation.
     * @return bool True if successful, false otherwise.
     */
    public function flush(int $timeoutMs)
    {
        return $this->producer->flush($timeoutMs);
    }

    /**
     * Purge any buffered messages from the internal producer queue.
     *
     * @return void
     */
    public function purge(): void
    {
        $this->producer->purge(RD_KAFKA_PURGE_F_QUEUE);
    }

    /**
     * Add additional broker addresses to the producer configuration.
     *
     * @param string $brokerList A comma-separated list of broker addresses.
     * @return int The number of brokers added.
     */
    public function addBrokers(string $brokerList): int
    {
        return $this->producer->addBrokers($brokerList);
    }
}
