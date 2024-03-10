<?php

namespace Fatihtuzlu\PHPKafkaCDC;

use Exception;
use RdKafka\Conf;
use RdKafka\Consumer;
use RdKafka\Message;

class ConsumerManager extends Kafka
{
    private Consumer $consumer;

    public function __construct(
        private string $topic,
        private string $groupId,
        private ?string $baseUrl,
        private ?string $port
    ) {
        $this->setBootstrapServer($baseUrl . ":" . $port);
        $this->setGroupId($groupId);
        $config = new Conf();
        parent::__construct($config);
        $this->consumer = new Consumer($config);
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * Fetch a single message from a specific topic, partition, and offset.
     *
     * @param string $topic The topic name.
     * @param int $partition The partition number.
     * @param int $offset The message offset.
     * @param int $timeMs The timeout in milliseconds for the consume operation.
     * @return RdKafka\Message|null The fetched message object or null if no message is available.
     * @throws Exception If an error occurs during message consumption.
     */
    public function fetchMessages(string $topic, int $partition, int $offset, int $timeMs = 1000): ?Message
    {
        try {
            $topic = $this->consumer->newTopic($this->topic);
            $topic->consumeStart($partition, $offset);
            $message = $topic->consume($partition, $timeMs);
            if (null === $message || $message->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
                throw new Exception($message->err);
            } elseif ($message->err) {
                throw new Exception($message->errstr());
            }
            return $message->payload;
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
        }
    }

    /**
     * Add additional broker addresses to the consumer configuration.
     *
     * @param string $brokerList A comma-separated list of broker addresses.
     * @return int The number of brokers added.
     */
    public function addBrokers(string $brokerList): int
    {
        return $this->addBrokers($brokerList);
    }

    /**
     * Consume messages from a list of topics using low-level API.
     *
     * @param array $topics List of topics to consume from.
     * @param string $partition The partition number.
     * @param int $timeMs The timeout in milliseconds for the consume operation.
     * @return RdKafka\Message|null The fetched message object or null if no message is available.
     * @throws Exception If an error occurs during message consumption.
     */
    public function lowLevelConsuming(array $topics, string $partition, int $timeMs): ?Message
    {
        try {
            $queue = $this->consumer->newQueue();
            foreach ($topics as $topic) {
                $topic->consumeQueueStart(0, RD_KAFKA_OFFSET_BEGINNING, $queue);
                $message = $queue->consume($partition, $timeMs);

                if (null === $message || $message->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
                    throw new Exception($message->err);
                } elseif ($message->err) {
                    throw new Exception($message->errstr());
                }
                return $message->payload;
            }
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
        }
    }
}
