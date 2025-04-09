<?php
declare(strict_types=1);

namespace Cocoon\Control\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Cocoon\Control\Exceptions\LanguageFileNotFoundException;

class LanguageFileNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_exception_with_file_path(): void
    {
        $file = '/path/to/language/file.php';
        $exception = new LanguageFileNotFoundException($file);
        
        $this->assertEquals("Language file '{$file}' not found", $exception->getMessage());
        $this->assertEquals(['file' => "Le fichier de langue '{$file}' n'existe pas"], $exception->getErrors());
    }

    /**
     * @test
     */
    public function it_creates_exception_with_code(): void
    {
        $file = '/path/to/language/file.php';
        $code = 404;
        $exception = new LanguageFileNotFoundException($file, $code);
        
        $this->assertEquals($code, $exception->getCode());
    }

    /**
     * @test
     */
    public function it_creates_exception_with_previous_exception(): void
    {
        $file = '/path/to/language/file.php';
        $previous = new \Exception('Previous error');
        $exception = new LanguageFileNotFoundException($file, 0, $previous);
        
        $this->assertSame($previous, $exception->getPrevious());
    }
} 