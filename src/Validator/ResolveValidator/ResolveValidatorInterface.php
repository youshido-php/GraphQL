<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ResolveValidator;


use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Query;

interface ResolveValidatorInterface
{

    /**
     * @param $field     AbstractField
     * @param $query     Query|Field
     * @param $request   Request
     *
     * @return bool
     */
    public function validateArguments(AbstractField $field, $query, Request $request);

}
