<?php
declare(strict_types=1);

namespace Cocoon\Control\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Cocoon\Control\Exceptions\ValidationException;

class ValidationExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_exception_with_errors(): void
    {
        $errors = [
            'name' => 'Le nom est requis',
            'email' => 'L\'email est invalide'
        ];

        $exception = new ValidationException($errors);
        
        $this->assertEquals('Validation failed', $exception->getMessage());
        $this->assertEquals($errors, $exception->getErrors());
    }

    /**
     * @test
     */
    public function it_creates_exception_with_custom_message(): void
    {
        $errors = ['field' => 'Error message'];
        $message = 'Custom validation error';
        
        $exception = new ValidationException($errors, $message);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($errors, $exception->getErrors());
    }

    /**
     * @test
     */
    public function it_creates_exception_with_code(): void
    {
        $errors = ['field' => 'Error message'];
        $code = 400;
        
        $exception = new ValidationException($errors, 'Validation failed', $code);
        
        $this->assertEquals($code, $exception->getCode());
    }

    /**
     * @test
     */
    public function it_creates_exception_with_previous_exception(): void
    {
        $errors = ['field' => 'Error message'];
        $previous = new \Exception('Previous error');
        
        $exception = new ValidationException($errors, 'Validation failed', 0, $previous);
        
        $this->assertSame($previous, $exception->getPrevious());
    }
} 