<?php

namespace Clue\Tests\React\Docker;

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use React\Promise\Deferred;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function expectCallableOnce()
    {
        $mock = $this->createCallableMock();

        $mock
            ->expects($this->once())
            ->method('__invoke');

        return $mock;
    }

    protected function expectCallableOnceWith($value)
    {
        $mock = $this->createCallableMock();

        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($value);

        return $mock;
    }

    protected function expectCallableNever()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->never())
            ->method('__invoke');

        return $mock;
    }

    protected function expectCallableOnceParameter($type)
    {
        return $this->expectCallableOnceWith($this->isInstanceOf($type));
    }

    protected function createCallableMock()
    {
        if (method_exists('PHPUnit\Framework\MockObject\MockBuilder', 'addMethods')) {
            // PHPUnit 8.5+
            return $this->getMockBuilder('stdClass')->addMethods(array('__invoke'))->getMock();
        } else {
            // legacy PHPUnit 4 - PHPUnit 8.4
            return $this->getMockBuilder('stdClass')->setMethods(array('__invoke'))->getMock();
        }
    }

    protected function expectPromiseResolve($promise)
    {
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $promise->then($this->expectCallableOnce(), $this->expectCallableNever());

        return $promise;
    }

    protected function expectPromiseResolveWith($expected, $promise)
    {
        $promise->then($this->expectCallableOnceWith($expected), $this->expectCallableNever());
    }

    protected function expectPromiseReject($promise)
    {
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $promise->then($this->expectCallableNever(), $this->expectCallableOnce());

        return $promise;
    }

    public function setExpectedException($exception, $exceptionMessage = '', $exceptionCode = null)
    {
        if (method_exists($this, 'expectException')) {
            // PHPUnit 5.2+
            $this->expectException($exception);
            if ($exceptionMessage !== '') {
                $this->expectExceptionMessage($exceptionMessage);
            }
            if ($exceptionCode !== null) {
                $this->expectExceptionCode($exceptionCode);
            }
        } else {
            // legacy PHPUnit 4 - PHPUnit 5.1
            parent::setExpectedException($exception, $exceptionMessage, $exceptionCode);
        }
    }
}
