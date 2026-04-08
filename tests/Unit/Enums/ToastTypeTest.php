<?php

namespace Tests\Unit\Enums;

use App\Enums\ToastType;
use PHPUnit\Framework\TestCase;

class ToastTypeTest extends TestCase
{
    public function test_all_enum_cases_exist(): void
    {
        $cases = ToastType::cases();
        
        $this->assertCount(4, $cases);
        $this->assertContains(ToastType::SUCCESS, $cases);
        $this->assertContains(ToastType::ERROR, $cases);
        $this->assertContains(ToastType::WARNING, $cases);
        $this->assertContains(ToastType::INFO, $cases);
    }

    public function test_getIcon_returns_correct_icon_for_success(): void
    {
        $this->assertEquals('check_circle', ToastType::SUCCESS->getIcon());
    }

    public function test_getIcon_returns_correct_icon_for_error(): void
    {
        $this->assertEquals('error', ToastType::ERROR->getIcon());
    }

    public function test_getIcon_returns_correct_icon_for_warning(): void
    {
        $this->assertEquals('warning', ToastType::WARNING->getIcon());
    }

    public function test_getIcon_returns_correct_icon_for_info(): void
    {
        $this->assertEquals('info', ToastType::INFO->getIcon());
    }

    public function test_getColor_returns_correct_hex_for_success(): void
    {
        $this->assertEquals('#10b981', ToastType::SUCCESS->getColor());
    }

    public function test_getColor_returns_correct_hex_for_error(): void
    {
        $this->assertEquals('#ef4444', ToastType::ERROR->getColor());
    }

    public function test_getColor_returns_correct_hex_for_warning(): void
    {
        $this->assertEquals('#f59e0b', ToastType::WARNING->getColor());
    }

    public function test_getColor_returns_correct_hex_for_info(): void
    {
        $this->assertEquals('#3b82f6', ToastType::INFO->getColor());
    }

    public function test_getDefaultTitle_returns_non_empty_string(): void
    {
        $this->assertNotEmpty(ToastType::SUCCESS->getDefaultTitle());
        $this->assertNotEmpty(ToastType::ERROR->getDefaultTitle());
        $this->assertNotEmpty(ToastType::WARNING->getDefaultTitle());
        $this->assertNotEmpty(ToastType::INFO->getDefaultTitle());
    }

    public function test_values_returns_array_of_string_values(): void
    {
        $values = ToastType::values();
        
        $this->assertCount(4, $values);
        $this->assertContains('success', $values);
        $this->assertContains('error', $values);
        $this->assertContains('warning', $values);
        $this->assertContains('info', $values);
    }
}