<?php

namespace unapi\def\tele2;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use unapi\def\common\dto\OperatorDto;
use unapi\def\common\interfaces\DefServiceInterface;
use unapi\dto\PhoneDto;
use unapi\interfaces\ServiceInterface;

use function GuzzleHttp\json_decode;

class Tele2Service implements DefServiceInterface, ServiceInterface, LoggerAwareInterface
{
    /** @var Tele2Client */
    private $client;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(array $config = [])
    {
        if (!isset($config['client'])) {
            $this->client = new Tele2Client();
        } elseif ($config['client'] instanceof Tele2Client) {
            $this->client = $config['client'];
        } else {
            throw new \InvalidArgumentException('Client must be instance of Tele2Client');
        }

        if (!isset($config['logger'])) {
            $this->logger = new NullLogger();
        } elseif ($config['logger'] instanceof LoggerInterface) {
            $this->setLogger($config['logger']);
        } else {
            throw new \InvalidArgumentException('Logger must be instance of LoggerInterface');
        }
    }

    /**
     * @inheritdoc
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param PhoneDto $phone
     * @return PromiseInterface
     */
    public function detectOperator(PhoneDto $phone): PromiseInterface
    {
        return $this->initialPage($this->client)->then(function () use ($phone) {
            return $this->submitForm($this->client, $phone)->then(function (ResponseInterface $response) {
                return $this->processResult($response->getBody()->getContents());
            });
        });
    }

    /**
     * @param Tele2Client $client
     * @return PromiseInterface
     */
    protected function initialPage(Tele2Client $client): PromiseInterface
    {
        return $client->getAsync('/whois.html');
    }

    /**
     * @param Tele2Client $client
     * @param PhoneDto $phone
     * @return PromiseInterface
     */
    protected function submitForm(Tele2Client $client, PhoneDto $phone): PromiseInterface
    {
        return $client->postAsync(sprintf('/gateway.php?%s', $phone->getNumber()), [
            'form_params' => [
                'private' => null,
                'location' => 'whois.html',
            ]
        ]);
    }

    /**
     * @param string $body
     * @return OperatorDto
     * @throws \ErrorException
     */
    protected function processResult(string $body): OperatorDto
    {
        $this->logger->info($body);
        $result = json_decode($body);

        if (empty($result->response))
            throw new \ErrorException('Service error');

        return (new OperatorDto())
                ->setName($result->response->mnc->value)
                ->setMnc('250' . $result->response->mnc->code);
    }
}