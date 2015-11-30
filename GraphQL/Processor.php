<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 1:05 AM
*/

namespace Youshido\GraphQL;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeKind;
use Youshido\GraphQL\Validator\Exception\ResolveException;
use Youshido\GraphQL\Validator\ValidatorInterface;

class Processor
{

    /** @var  array */
    private $data;

    /** @var ValidatorInterface */
    private $validator;

    /** @var Schema */
    private $schema;

    /** @var PropertyAccessor */
    private $propertyAccessor;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function processQuery($queryString, $variables = [])
    {
        $this->validator->clearErrors();

        try {
            $request = $this->createRequestFromString($queryString);

            $data = [];

            // @todo invoke variables

            foreach ($request->getQueries() as $query) {
                if ($queryResult = $this->executeQuery($query, $this->getSchema()->getQueryType())) {
                    $data = array_merge($data, $queryResult);
                };
            }

            $this->data = $data;

        } catch (\Exception $e) {
            $this->validator->addError($e);
        }
    }

    protected function createRequestFromString($query)
    {
        $parser = new Parser($query);

        /** @todo: Parser parse and then getRequest or Request::createFrom... */
        return $parser->parse();
    }

    /**
     * @param Query|Field $query
     * @param ObjectType  $currentLevelSchema
     * @param null        $contextValue
     * @return array|bool|mixed
     */
    protected function executeQuery($query, $currentLevelSchema, $contextValue = null)
    {
        if (!$currentLevelSchema->hasField($query->getName())) {
            $this->validator->addError(new ResolveException(sprintf('Field "%s" not found in schema', $query->getName())));

            return null;
        }

        $queryType = $currentLevelSchema->getField($query->getName());
        if ($queryType instanceof Field) {
            $alias            = $query->getName();
            $preResolvedValue = $this->getPreResolvedValue($contextValue, $query);
            $value            = $queryType->getType()->serialize($preResolvedValue);
        } else {
            if (!$this->validateArguments($queryType, $query)) {
                return null;
            }

            /** @var ObjectType $queryType */
            $resolvedValue = $queryType->resolve($contextValue, $this->parseArgumentsValues($queryType, $query));

            $alias = $query->hasAlias() ? $query->getAlias() : $query->getName();
            $value = [];
            if ($currentLevelSchema->getKind() == TypeKind::ListKind) {
                foreach ($resolvedValue as $resolvedValueItem) {
                    $value[$alias][] = [];
                    $index           = count($value[$alias]) - 1;
                    foreach ($query->getFields() as $field) {
                        $value[$alias][$index] = array_merge($value[$alias][$index], $this->executeQuery($field, $queryType, $resolvedValueItem));
                    }
                }
            } else {
                foreach ($query->getFields() as $field) {
                    $value = array_merge($value, $this->executeQuery($field, $queryType, $resolvedValue));
                }
            }
        }

        return [$alias => $value];
    }

    /**
     * @param $value
     * @param $query Field
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function getPreResolvedValue($value, $query)
    {
        if (is_array($value)) {
            if (array_key_exists($query->getName(), $value)) {
                return $value[$query->getName()];
            } else {
                throw new \Exception('Not found in resolve result', $query->getName());
            }
        } elseif (is_object($value)) {
            return $this->propertyAccessor->getValue($value, $query->getName());
        }

        return $value;
    }

    /**
     * @param $queryType ObjectType
    *  @param $query Query
     *
     * @return array
     */
    public function parseArgumentsValues($queryType, $query)
    {
        $args = [];
        $arguments = $queryType->getArguments();

        foreach ($query->getArguments() as $argument) {
            $type = $arguments[$argument->getName()]->getConfig()->getType();

            $args[$argument->getName()] = $type->parseValue($argument->getValue()->getValue());
        }

        return $args;
    }

    /**
     * @param $queryType ObjectType
     * @param $query     Query
     *
     * @return bool
     */
    protected function validateArguments($queryType, $query)
    {
        $requiredArguments = array_filter($queryType->getArguments(), function ($argument) {
            /** @var $argument Field */
            return $argument->getConfig()->isRequired();
        });

        foreach ($query->getArguments() as $argument) {
            if (!array_key_exists($argument->getName(), $queryType->getArguments())) {
                $this->validator->addError(new ResolveException(sprintf('Unknown argument "%s" on field "%s".', $argument->getName(), $queryType->getName())));

                return false;
            }

            if(array_key_exists($argument->getName(), $requiredArguments)){
                unset($requiredArguments[$argument->getName()]);
            }
        }

        if (count($requiredArguments)) {
            $this->validator->addError(new ResolveException(sprintf('Require "%s" arguments to query "%s"', implode(', ', array_keys($requiredArguments)), $query->getName())));

            return false;
        }

        return true;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function getErrors()
    {
        return $this->validator->getErrors();
    }

    public function getResponseData()
    {
        $result = [];

        if ($this->data) {
            $result['data'] = $this->data;
        }

        if ($this->validator->hasErrors()) {
            $result['errors'] = $this->validator->getErrorsArray();
        }

        return $result;
    }
}