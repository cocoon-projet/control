<?php
declare(strict_types=1);

namespace Cocoon\Control\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Cocoon\Control\Validator;
use Cocoon\Control\Exceptions\ValidationException;
use Cocoon\Control\Exceptions\RuleNotFoundException;
use Cocoon\Control\Exceptions\LanguageFileNotFoundException;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator('fr');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_language_file_not_found(): void
    {
        $this->expectException(LanguageFileNotFoundException::class);
        new Validator('invalid_lang');
    }

    /**
     * @test
     */
    public function it_validates_required_field(): void
    {
        $this->expectException(ValidationException::class);
        
        $this->validator->validate([
            'name' => 'required'
        ]);
    }

    /**
     * @test
     */
    public function it_validates_email_field(): void
    {
        $this->expectException(ValidationException::class);
        
        $this->validator->data([
            'email' => 'invalid-email'
        ])->validate([
            'email' => 'required|email'
        ]);
    }

    /**
     * @test
     */
    public function it_validates_numeric_field(): void
    {
        $this->expectException(ValidationException::class);
        
        $this->validator->data([
            'age' => 'not-a-number'
        ])->validate([
            'age' => 'required|int'
        ]);
    }

    /**
     * @test
     */
    public function it_validates_with_custom_messages(): void
    {
        $this->expectException(ValidationException::class);
        
        $this->validator->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Le nom est obligatoire'
        ]);
    }

    /**
     * @test
     */
    public function it_validates_with_field_alias(): void
    {
        $this->expectException(ValidationException::class);
        
        $this->validator->validate([
            'user_name as nom d\'utilisateur' => 'required'
        ]);
    }

    /**
     * @test
     */
    public function it_validates_with_multiple_rules(): void
    {
        $this->expectException(ValidationException::class);
        
        $this->validator->data([
            'password' => '123',
            'password_confirm' => '456'
        ])->validate([
            'password' => 'required|same:password_confirm',
            'password_confirm' => 'required'
        ]);
    }

    /**
     * @test
     */
    public function it_validates_with_custom_rule(): void
    {
        $this->validator->addRule('custom', function($value) {
            return $value === 'valid';
        }, 'La valeur doit être "valid"');

        $this->expectException(ValidationException::class);
        
        $this->validator->data([
            'field' => 'invalid'
        ])->validate([
            'field' => 'required|custom'
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_for_unknown_rule(): void
    {
        $this->expectException(RuleNotFoundException::class);
        
        $this->validator->validate([
            'field' => 'unknown_rule'
        ]);
    }

    /**
     * @test
     */
    public function it_validates_successfully(): void
    {
        $this->validator->data([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => '25',
            'password' => '123456',
            'password_confirm' => '123456'
        ])->validate([
            'name' => 'required',
            'email' => 'required|email',
            'age' => 'required|int',
            'password' => 'required|same:password_confirm',
            'password_confirm' => 'required'
        ]);

        $this->assertFalse($this->validator->fails());
    }
} 