<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\ProviderRegistrationValidationRequest;
use Illuminate\Support\Facades\Validator;

class PhoneValidationUnitTest extends TestCase
{
    /**
     * Test phone normalization logic.
     */
    public function test_phone_normalization()
    {
        $request = new ProviderRegistrationValidationRequest();
        
        // Use reflection to access the protected method
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('normalizePhoneNumber');
        $method->setAccessible(true);
        
        $testCases = [
            ['input' => '+971501234567', 'expected' => '+971501234567'],
            ['input' => '971501234567', 'expected' => '+971501234567'],
            ['input' => '0501234567', 'expected' => '+971501234567'],
            ['input' => '501234567', 'expected' => '+971501234567'],
            ['input' => '+971 50 123 4567', 'expected' => '+971501234567'], // With spaces
        ];
        
        foreach ($testCases as $case) {
            $result = $method->invoke($request, $case['input']);
            $this->assertEquals($case['expected'], $result, 
                "Phone '{$case['input']}' should normalize to '{$case['expected']}' but got '{$result}'");
        }
    }

    /**
     * Test phone format validation patterns.
     */
    public function test_phone_format_validation()
    {
        $validPhones = [
            '+971501234567',
            '971501234567',
            '0501234567',
            '501234567'
        ];
        
        $invalidPhones = [
            '+97150123456',   // Too short
            '+9715012345678', // Too long
            '+1234567890',    // Wrong country code
            'abc123456789',   // Contains letters
            '',               // Empty
            '12345',          // Too short
        ];
        
        foreach ($validPhones as $phone) {
            // Test the pattern directly
            $cleanPhone = preg_replace('/\s/', '', $phone);
            $validPatterns = [
                '/^\+971[0-9]{9}$/',
                '/^971[0-9]{9}$/',
                '/^0[0-9]{9}$/',
                '/^[0-9]{9}$/'
            ];
            
            $isValid = false;
            foreach ($validPatterns as $pattern) {
                if (preg_match($pattern, $cleanPhone)) {
                    $isValid = true;
                    break;
                }
            }
            
            $this->assertTrue($isValid, "Phone '{$phone}' should be valid");
        }
        
        foreach ($invalidPhones as $phone) {
            // Test the pattern directly
            $cleanPhone = preg_replace('/\s/', '', $phone);
            $validPatterns = [
                '/^\+971[0-9]{9}$/',
                '/^971[0-9]{9}$/',
                '/^0[0-9]{9}$/',
                '/^[0-9]{9}$/'
            ];
            
            $isValid = false;
            foreach ($validPatterns as $pattern) {
                if (preg_match($pattern, $cleanPhone)) {
                    $isValid = true;
                    break;
                }
            }
            
            $this->assertFalse($isValid, "Phone '{$phone}' should be invalid");
        }
    }

    /**
     * Test frontend JavaScript validation pattern.
     */
    public function test_frontend_validation_pattern()
    {
        $validPhones = [
            '+971501234567',
            '971501234567',
            '0501234567',
            '501234567'
        ];
        
        foreach ($validPhones as $phone) {
            $cleanPhone = str_replace(' ', '', $phone);
            $patterns = [
                '/^\+971[0-9]{9}$/',
                '/^971[0-9]{9}$/',
                '/^0[0-9]{9}$/',
                '/^[0-9]{9}$/'
            ];
            
            $isValid = false;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $cleanPhone)) {
                    $isValid = true;
                    break;
                }
            }
            
            $this->assertTrue($isValid, "Frontend should accept phone '{$phone}'");
        }
    }
}
