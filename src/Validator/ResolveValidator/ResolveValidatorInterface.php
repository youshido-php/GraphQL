<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ResolveValidator;


use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Type\AbstractType;

interface ResolveValidatorInterface
{

    /**
     * @param $field     AbstractField
     * @param $query     Query
     * @param $request   Request
     *
     * @return bool
     */
    public function validateArguments(AbstractField $field, $query, Request $request);

    /**
     * @param mixed        $value
     * @param AbstractType $type
     *
     * @return bool
     */
    public function validateResolvedValueType($value, $type);
}
