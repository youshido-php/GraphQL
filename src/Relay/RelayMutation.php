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
/**
 * Date: 17.05.16.
 */

namespace Youshido\GraphQL\Relay;

use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Type\InputObject\InputObjectType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class RelayMutation
{
    /**
     * @param string                   $name
     * @param array                    $args
     * @param AbstractObjectType|array $output
     * @param callable                 $resolveFunction
     *
     * @throws \Exception
     *
     * @return Field
     */
    public static function buildMutation($name, array $args, $output, callable $resolveFunction)
    {
        if (!\is_array($output) || (\is_object($output) && !($output instanceof AbstractObjectType))) {
            throw new \Exception('Output can be instance of AbstractObjectType or array of fields');
        }

        return new Field([
            'name' => $name,
            'args' => [
                new InputField([
                    'name' => 'input',
                    'type' => new NonNullType(new InputObjectType([
                        'name'   => \ucfirst($name) . 'Input',
                        'fields' => \array_merge(
                            $args,
                            [new InputField(['name' => 'clientMutationId', 'type' => new NonNullType(new StringType())])]
                        ),
                    ])),
                ]),
            ],
            'type' => new ObjectType([
                'fields' => \is_object($output) ? $output : \array_merge(
                    $output,
                    [new Field(['name' => 'clientMutationId', 'type' => new NonNullType(new StringType())])]
                ),
                'name' => \ucfirst($name) . 'Payload',
            ]),
            'resolve' => static function ($value, $args, ResolveInfo $info) use ($resolveFunction) {
                $resolveValue = $resolveFunction($value, $args['input'], $args, $info);

                if (\is_object($resolveValue)) {
                    $resolveValue->clientMutationId = $args['input']['clientMutationId'];
                } elseif (\is_array($resolveValue)) {
                    $resolveValue['clientMutationId'] = $args['input']['clientMutationId'];
                }

                return $resolveValue;
            },
        ]);
    }
}
