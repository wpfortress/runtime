<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Tests\FastCGI;

use hollodotme\FastCGI\Interfaces\ProvidesRequestData;
use PHPUnit\Framework\TestCase;
use WPFortress\Runtime\FastCGI\Request;

final class RequestTest extends TestCase
{
    /** @test */
    public function it_implements_provides_request_data_contract(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertInstanceOf(ProvidesRequestData::class, $request);
    }

    /** @test */
    public function it_forms_correct_request_with_custom_content(): void
    {
        $request = new Request(
            content: 'foo',
            parameters: [],
        );

        self::assertSame('foo', $request->getContent());
    }

    /** @test */
    public function it_forms_correct_request_with_default_gateway_interface(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_GATEWAY_INTERFACE, $request->getGatewayInterface());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_gateway_interface(): void
    {
        $request = new Request(
            content: '',
            parameters: ['GATEWAY_INTERFACE' => 'foo'],
        );

        self::assertSame('foo', $request->getGatewayInterface());
    }

    /** @test */
    public function it_forms_correct_request_with_default_request_method(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_REQUEST_METHOD, $request->getRequestMethod());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_request_method(): void
    {
        $request = new Request(
            content: '',
            parameters: ['REQUEST_METHOD' => 'foo'],
        );

        self::assertSame('foo', $request->getRequestMethod());
    }

    /** @test */
    public function it_forms_correct_request_with_default_script_filename(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame('', $request->getScriptFilename());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_script_filename(): void
    {
        $request = new Request(
            content: '',
            parameters: ['SCRIPT_FILENAME' => 'foo'],
        );

        self::assertSame('foo', $request->getScriptFilename());
    }

    /** @test */
    public function it_forms_correct_request_with_default_server_software(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_SERVER_SOFTWARE, $request->getServerSoftware());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_server_software(): void
    {
        $request = new Request(
            content: '',
            parameters: ['SERVER_SOFTWARE' => 'foo'],
        );

        self::assertSame('foo', $request->getServerSoftware());
    }

    /** @test */
    public function it_forms_correct_request_with_default_remote_address(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_REMOTE_ADDR, $request->getRemoteAddress());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_remote_address(): void
    {
        $request = new Request(
            content: '',
            parameters: ['REMOTE_ADDR' => 'foo'],
        );

        self::assertSame('foo', $request->getRemoteAddress());
    }

    /** @test */
    public function it_forms_correct_request_with_default_remote_port(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_REMOTE_PORT, $request->getRemotePort());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_remote_port(): void
    {
        $request = new Request(
            content: '',
            parameters: ['REMOTE_PORT' => 8080],
        );

        self::assertSame(8080, $request->getRemotePort());
    }

    /** @test */
    public function it_forms_correct_request_with_default_server_address(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_SERVER_ADDR, $request->getServerAddress());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_server_address(): void
    {
        $request = new Request(
            content: '',
            parameters: ['SERVER_ADDR' => 'foo'],
        );

        self::assertSame('foo', $request->getServerAddress());
    }

    /** @test */
    public function it_forms_correct_request_with_default_server_port(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_SERVER_PORT, $request->getServerPort());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_server_port(): void
    {
        $request = new Request(
            content: '',
            parameters: ['SERVER_PORT' => 8080],
        );

        self::assertSame(8080, $request->getServerPort());
    }

    /** @test */
    public function it_forms_correct_request_with_default_server_name(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_SERVER_NAME, $request->getServerName());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_server_name(): void
    {
        $request = new Request(
            content: '',
            parameters: ['SERVER_NAME' => 'foo'],
        );

        self::assertSame('foo', $request->getServerName());
    }

    /** @test */
    public function it_forms_correct_request_with_default_server_protocol(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_SERVER_PROTOCOL, $request->getServerProtocol());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_server_protocol(): void
    {
        $request = new Request(
            content: '',
            parameters: ['SERVER_PROTOCOL' => 'foo'],
        );

        self::assertSame('foo', $request->getServerProtocol());
    }

    /** @test */
    public function it_forms_correct_request_with_default_content_type(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_CONTENT_TYPE, $request->getContentType());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_content_type(): void
    {
        $request = new Request(
            content: '',
            parameters: ['CONTENT_TYPE' => 'foo'],
        );

        self::assertSame('foo', $request->getContentType());
    }

    /** @test */
    public function it_forms_correct_request_with_default_content_length(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame(Request::DEFAULT_CONTENT_LENGTH, $request->getContentLength());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_content_length(): void
    {
        $request = new Request(
            content: '',
            parameters: ['CONTENT_LENGTH' => 150],
        );

        self::assertSame(150, $request->getContentLength());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_variables(): void
    {
        $request = new Request(
            content: '',
            parameters: ['FOO' => 'bar'],
        );

        self::assertSame(['FOO' => 'bar'], $request->getCustomVars());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_parameters(): void
    {
        $request = new Request(
            content: '',
            parameters: ['FOO' => 'bar'],
        );

        self::assertSame(['FOO' => 'bar'], $request->getParams());
    }

    /** @test */
    public function it_forms_correct_request_with_default_request_uri(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame('', $request->getRequestUri());
    }

    /** @test */
    public function it_forms_correct_request_with_custom_request_uri(): void
    {
        $request = new Request(
            content: '',
            parameters: ['REQUEST_URI' => '/foo'],
        );

        self::assertSame('/foo', $request->getRequestUri());
    }

    /** @test */
    public function it_forms_correct_request_with_response_callbacks(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame([], $request->getResponseCallbacks());
    }

    /** @test */
    public function it_forms_correct_request_with_failure_callbacks(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame([], $request->getFailureCallbacks());
    }

    /** @test */
    public function it_forms_correct_request_with_pass_through_callbacks(): void
    {
        $request = new Request(content: '', parameters: []);

        self::assertSame([], $request->getPassThroughCallbacks());
    }
}
