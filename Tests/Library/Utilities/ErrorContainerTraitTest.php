<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5/12/16 9:39 PM
 */

namespace Youshido\Tests\Library\Utilities;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Exception\DatableResolveException;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;

class ErrorContainerTraitTest extends \PHPUnit_Framework_TestCase implements ErrorContainerInterface
{
    use ErrorContainerTrait;

    public function testAdding(): void
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

        $this->clearErrors();
        $this->addError(new DatableResolveException('Wrong data', 412, ['user_id' => '1']));
        $this->assertEquals([
            ['message' => 'Wrong data', 'code' => 412, 'user_id' => '1'],
        ], $this->getErrorsArray());

        $this->clearErrors();
        $this->addError(new ConfigurationException('Invalid name'));
        $this->assertEquals([
            ['message' => 'Invalid name'],
        ], $this->getErrorsArray());
    }
}
