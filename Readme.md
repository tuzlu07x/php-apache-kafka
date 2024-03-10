# PHP Apache Kafka Package

PHP Kafka CDC is a package developed by Fatih Tuzlu for managing multidata with Apache Kafka in PHP applications. It provides functionalities for both consuming and producing messages using Kafka.

## Remember

Do not forget to install rdkafka library

### Macos

```
brew install librdkafka
```

### Debian

```
$ apt install librdkafka-dev
```

### RedHat, CentOS, Fedora

```
$ yum install librdkafka-devel
```

### Windows

```
# Install vcpkg if not already installed
$ git clone https://github.com/Microsoft/vcpkg.git
$ cd vcpkg
$ ./bootstrap-vcpkg.sh
$ ./vcpkg integrate install

# Install librdkafka
$ vcpkg install librdkafka
```

### For Detail

```
You can visit https://github.com/confluentinc/librdkafka
```

## Installation

You can install PHP Kafka CDC via Composer. Run the following command in your terminal:

```
composer require fatihtuzlu/php-apache-kafka
```

## Requirements

PHP Kafka CDC requires the following dependencies:

```
idealo/php-rdkafka-ffi: ^0.5.0
php: ^8.0.0
```

## Usage

### Consumer

To consume messages from Kafka topics, you can use the ConsumerManager class. Here's a basic example of how to set up a consumer:

```php
use Fatihtuzlu\PHPKafkaCDC\ConsumerManager;

// Initialize consumer manager
$consumer = new ConsumerManager(
    $topic,       // Kafka topic to consume from
    $groupId,     // Consumer group ID
    $bootstrapServers // Kafka bootstrap servers
);

// Fetch messages from Kafka
$message = $consumer->fetchMessages($topic, $partition, $offset, $timeMs);
```

### Producer

To produce messages to Kafka topics, you can use the ProducerManager class. Here's a basic example of how to set up a producer:

```php
use Fatihtuzlu\PHPKafkaCDC\ProducerManager;

// Initialize producer manager
$producer = new ProducerManager(
    $topic,             // Kafka topic to produce to
    $bootstrapServers   // Kafka bootstrap servers
);

// Send a message to Kafka
$producer->sendMessages($message, $flush = 1000, $debug = false);
```

## Configuration

### Debugging

You can enable debugging for your Kafka connection by calling the debug() method:

```php
$producer->debug();
```

### Additional Brokers

```php
$producer->addBrokers($brokerList);
```

## Examples

### Sample Consumer

```php
use Fatihtuzlu\PHPKafkaCDC\ConsumerManager;

// Kafka broker and topic information
$bootstrapServers = "localhost:9092";
$topic = "my_topic";
$groupId = "my_group";

// Initialize the consumer
$consumer = new ConsumerManager($topic, $groupId, $bootstrapServers);

// Fetch messages
$message = $consumer->fetchMessages($topic, 0, RD_KAFKA_OFFSET_BEGINNING);

// Process the received message
if ($message !== null) {
    echo "Received message: " . $message->payload . "\n";
} else {
    echo "No messages available.\n";
}
```

### Sample Producer

```php
use Fatihtuzlu\PHPKafkaCDC\ProducerManager;

// Kafka broker and topic information
$bootstrapServers = "localhost:9092";
$topic = "my_topic";

// Initialize the producer
$producer = new ProducerManager($topic, $bootstrapServers);

// Send a message
$messageToSend = "Hello, Kafka!";
$producer->sendMessages($messageToSend);

echo "Message sent to Kafka: " . $messageToSend . "\n";

```

In these examples, the consumer retrieves messages from Kafka, while the producer sends messages to Kafka. my_topic represents the target topic in Kafka. Make sure to update the configuration settings according to your own Kafka cluster.
