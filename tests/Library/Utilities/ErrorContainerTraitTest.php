<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 9:39 PM
*/

namespace Youshido\Tests\Library\Utilities;

use Youshido\GraphQL\Exception\Interfaces\ExtendedExceptionInterface;
use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Youshido\GraphQL\Exception\Parser\SyntaxErrorException;
use Youshido\GraphQL\Parser\Location;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;

class ErrorContainerTraitTest extends \PHPUnit_Framework_TestCase implements ErrorContainerInterface
{

    use ErrorContainerTrait;

    protected function setUp()
    {
        $this->clearErrors();
    }

    public function testAddHasClearMergeErrors()
    {
        $error = new \Exception('Error');
        $this->addError($error);
        $this->assertTrue($this->hasErrors());

        $this->clearErrors();
        $this->assertFalse($this->hasErrors());

        $this->addError($error);
        $this->assertEquals([$error], $this->getErrors());

        $this->mergeErrors($this);
        $this->assertEquals([$error, $error], $this->getErrors());
    }

    public function testGetErrorsAsArrayGenericExceptionWithoutCode()
    {
        // Code is zero by default
        $this->addError(new \Exception('Generic exception'));
        $this->assertEquals([
            [
                'message' => 'Generic exception',
            ],
        ], $this->getErrorsArray());
    }

    public function testGetErrorsAsArrayGenericExceptionWithCode()
    {
        $this->addError(new \Exception('Generic exception with code', 4));
        $this->assertEquals([
            [
                'message' => 'Generic exception with code',
                'code'    => 4,
            ],
        ], $this->getErrorsArray());
    }

    public function testGetErrorsAsArrayLocationableException()
    {
        $this->addError(new SyntaxErrorException('Syntax error', new Location(5, 88)));
        $this->assertEquals([
            [
                'message'   => 'Syntax error',
                'locations' => [
                    [
                        'line'   => 5,
                        'column' => 88,
                    ],
                ],
            ],
        ], $this->getErrorsArray());
    }

    public function testGetErrorsAsArrayExtendedException()
    {
        $this->addError(new ExtendedException('Extended exception'));
        $this->assertEquals([
            [
                'message'    => 'Extended exception',
                'extensions' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                ],
            ],
        ], $this->getErrorsArray());
    }

    public function testGetErrorsAsArrayExceptionWithEverything()
    {
        $this->addError(new SuperException('Super exception', 3));
        $this->assertEquals([
            [
                'message'    => 'Super exception',
                'code'       => 3,
                'locations'  => [
                    [
                        'line'   => 6,
                        'column' => 10,
                    ],
                ],
                'extensions' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                ],
            ],
        ], $this->getErrorsArray());
    }
}

class ExtendedException extends \Exception implements ExtendedExceptionInterface
{
    public function getExtensions()
    {
        return [
            'foo' => 'foo',
            'bar' => 'bar',
        ];
    }
}

class SuperException extends \Exception implements LocationableExceptionInterface, ExtendedExceptionInterface
{
    public function getExtensions()
    {
        return [
            'foo' => 'foo',
            'bar' => 'bar',
        ];
    }

    public function getLocation()
    {
        return new Location(6, 10);
    }
}
