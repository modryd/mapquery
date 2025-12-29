<?php

namespace modryd\MapQuery\Tests;

use PHPUnit\Framework\TestCase;
use modryd\MapQuery\Exception\POISearchException;

class POISearchExceptionTest extends TestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new POISearchException('Test message');

        $this->assertInstanceOf(POISearchException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testExceptionWithCode(): void
    {
        $exception = new POISearchException('Test message', 500);

        $this->assertEquals(500, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previous = new \RuntimeException('Previous error');
        $exception = new POISearchException('Test message', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
