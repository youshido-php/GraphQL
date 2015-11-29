<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator;


interface ValidatorInterface
{

    public function validate($data, $rules = []);
    public function hasErrors();
    public function getErrors();
    public function clearErrors();

}