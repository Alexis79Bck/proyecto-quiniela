<?php

namespace Tests\Unit\Services;

use App\Enums\ToastType;
use App\Services\ToastService;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery;

class ToastServiceTest extends TestCase
{
    protected ToastService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ToastService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_formatToast_returns_correct_structure(): void
    {
        $result = $this->service->formatToast(
            ToastType::SUCCESS,
            'Operation completed',
            'Success Title'
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('icon', $result);
        $this->assertArrayHasKey('color', $result);
        $this->assertArrayHasKey('duration', $result);
        $this->assertArrayHasKey('dismissible', $result);
        $this->assertArrayHasKey('timestamp', $result);
    }

    public function test_formatToast_uses_type_value(): void
    {
        $result = $this->service->formatToast(ToastType::ERROR, 'Test message');

        $this->assertEquals('error', $result['type']);
    }

    public function test_formatToast_uses_custom_title_when_provided(): void
    {
        $result = $this->service->formatToast(
            ToastType::SUCCESS,
            'Test message',
            'Custom Title'
        );

        $this->assertEquals('Custom Title', $result['title']);
    }

    public function test_formatToast_uses_default_title_when_null(): void
    {
        $result = $this->service->formatToast(ToastType::SUCCESS, 'Test message');

        $this->assertEquals('Éxito', $result['title']);
    }

    public function test_formatToast_uses_custom_duration(): void
    {
        $result = $this->service->formatToast(
            ToastType::INFO,
            'Test message',
            null,
            ['duration' => 10000]
        );

        $this->assertEquals(10000, $result['duration']);
    }

    public function test_formatToast_uses_default_duration_when_not_provided(): void
    {
        $result = $this->service->formatToast(ToastType::INFO, 'Test message');

        $this->assertEquals(ToastService::DEFAULT_DURATION, $result['duration']);
    }

    public function test_formatToast_respects_dismissible_option(): void
    {
        $result = $this->service->formatToast(
            ToastType::WARNING,
            'Test message',
            null,
            ['dismissible' => false]
        );

        $this->assertFalse($result['dismissible']);
    }

    public function test_formatToast_uses_default_dismissible_when_not_provided(): void
    {
        $result = $this->service->formatToast(ToastType::INFO, 'Test message');

        $this->assertTrue($result['dismissible']);
    }

    public function test_getTypes_returns_all_available_types(): void
    {
        $result = $this->service->getTypes();

        $this->assertCount(4, $result);
        
        $types = array_column($result, 'type');
        $this->assertContains('success', $types);
        $this->assertContains('error', $types);
        $this->assertContains('warning', $types);
        $this->assertContains('info', $types);
    }

    public function test_getTypes_returns_icon_for_each_type(): void
    {
        $result = $this->service->getTypes();

        foreach ($result as $type) {
            $this->assertArrayHasKey('icon', $type);
            $this->assertArrayHasKey('color', $type);
            $this->assertArrayHasKey('default_title', $type);
        }
    }
}