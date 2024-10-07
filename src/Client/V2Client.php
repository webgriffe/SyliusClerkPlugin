<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Client;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Log\LoggerInterface;
use Webgriffe\SyliusClerkPlugin\Client\Exception\ClientException;
use Webgriffe\SyliusClerkPlugin\Client\Response\Verify;

final readonly class V2Client implements ClientInterface
{
    public function __construct(
        private GuzzleClientInterface $httpClient,
        private LoggerInterface $logger,
    ) {
    }

    public function verify(
        string $storePublicKey,
        string $token,
    ): Verify {
        $request = new ServerRequest(
            'GET',
            $this->getVerifyUrl($storePublicKey, $token),
            [
                'accept' => 'application/json',
            ],
        );

        try {
            $response = $this->httpClient->send($request);
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            throw new ClientException($e->getMessage(), $e->getCode(), $e);
        }
        /** @var array{status: 'ok'|'error', token_payload?: string, type?: string, message?: string} $responseBody */
        $responseBody = json_decode($response->getBody()->getContents(), true);

        $this->logger->error($responseBody['status'], ['response' => $responseBody]);

        return new Verify(
            $responseBody['status'],
            $responseBody,
        );
    }

    private function getVerifyUrl(string $storePublicKey, string $token): string
    {
        return 'https://api.clerk.io/v2/token/verify?' .
            http_build_query([
                'key' => $storePublicKey,
                'token' => $token,
            ]);
    }
}
