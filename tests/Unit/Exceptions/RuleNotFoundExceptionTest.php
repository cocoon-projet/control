<?php
declare(strict_types=1);

namespace Cocoon\Control\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Cocoon\Control\Exceptions\RuleNotFoundException;

class RuleNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_exception_with_rule_name(): void
    {
        $rule = 'custom_rule';
        $exception = new RuleNotFoundException($rule);
        
        $this->assertEquals("Rule '{$rule}' not found", $exception->getMessage());
        $this->assertEquals(['rule' => "La règle de validation '{$rule}' n'existe pas"], $exception->getErrors());
    }

    /**
     * @test
     */
    public function it_creates_exception_with_code(): void
    {
        $rule = 'custom_rule';
        $code = 404;
        $exception = new RuleNotFoundException($rule, $code);
        
        $this->assertEquals($code, $exception->getCode());
    }

    /**
     * @test
     */
    public function it_creates_exception_with_previous_exception(): void
    {
        $rule = 'custom_rule';
        $previous = new \Exception('Previous error');
        $exception = new RuleNotFoundException($rule, 0, $previous);
        
        $this->assertSame($previous, $exception->getPrevious());
    }
} 