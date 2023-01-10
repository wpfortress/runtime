<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\Feature;

use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use WPFortress\Runtime\Console\Application;
use WPFortress\Runtime\DependencyInjection\ContainerFactory;

final class WarmTest extends TestCase
{
    private MockWebServer $mockWebServer;

    protected function setUp(): void
    {
        $this->mockWebServer = new MockWebServer(port: 8080);
        $this->mockWebServer->start();
    }

    protected function tearDown(): void
    {
        $this->mockWebServer->stop();
    }

    /** @test */
    public function it_processes_warm_invocation_when_concurrency_is_one(): void
    {
        $response = new Response(
            body: json_encode(['warm' => 1]),
            headers: [
                'Lambda-Runtime-Aws-Request-Id' => '8476a536-e9f4-11e8-9739-2dfe598c3fcd',
                'Lambda-Runtime-Deadline-Ms' => 1542409706888,
                'Lambda-Runtime-Invoked-Function-Arn' => 'arn:aws:lambda:us-east-2:1234567890:function:custom-runtime',
                'Lambda-Runtime-Trace-Id' => 'Root=1-5bef4de7-ad49b0e87f6ef6c87fc2e700;Parent=9a9197af755a64;Sampled=1',
            ],
        );

        $this->mockWebServer->setResponseOfPath(
            path: '/2018-06-01/runtime/invocation/next',
            response: $response,
        );

        $container = ContainerFactory::makeFromConfig(path: __DIR__ . '/../../config/services.yaml');

        /** @var Application $application */
        $application = $container->get(id: Application::class);

        $command = $application->find(name: 'process');
        $commandTester = new CommandTester(command: $command);
        $commandTester->execute([
            '--max-invocations' => 1,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $lastRequest = $this->mockWebServer->getLastRequest();

        self::assertNotNull(actual: $lastRequest);
        self::assertStringContainsString(
            needle: '8476a536-e9f4-11e8-9739-2dfe598c3fcd',
            haystack: $lastRequest->getRequestUri(),
        );
        self::assertSame(
            expected: '["Lambda is warm"]',
            actual: $lastRequest->getInput(),
        );
    }
}
