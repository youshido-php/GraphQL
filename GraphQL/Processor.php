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
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ResolveException;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidatorInterface;

class Processor
{

    /** @var  array */
    protected $data;

    /** @var ResolveValidatorInterface */
    protected $resolveValidator;

    /** @var Schema */
    protected $schema;

    /** @var PropertyAccessor */
    protected $propertyAccessor;

    /** @var Request */
    protected $request;

    public function __construct(ResolveValidatorInterface $validator)
    {
        $this->resolveValidator = $validator;

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function processQuery($queryString, $variables = [])
    {
        $this->resolveValidator->clearErrors();
        $data = [];

        try {
            $this->parseAndCreateRequest($queryString, $variables);

            foreach ($this->request->getQueries() as $query) {
                if ($queryResult = $this->executeQuery($query, $this->getSchema()->getQueryType())) {
                    $data = array_merge($data, $queryResult);
                };
            }

            $this->data = $data;
        } catch (\Exception $e) {
            $this->resolveValidator->clearErrors(); //todo: rethink here (used this for critic exception)

            $this->resolveValidator->addError($e);
        }
    }

    protected function parseAndCreateRequest($query, $variables = [])
    {
        $parser = new Parser();

        $parser->setSource($query);
        $data = $parser->parse();

        $this->request = new Request($data);
        $this->request->setVariables($variables);
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
            $this->resolveValidator->addError(new ResolveException(sprintf('Field "%s" not found in schema', $query->getName())));

            return null;
        }

        /** @var ObjectType|Field $queryType */
        $queryType = $currentLevelSchema->getField($query->getName())->getType();
        if (get_class($query) == 'Youshido\GraphQL\Parser\Ast\Field') {
            $alias            = $query->getName();
            $preResolvedValue = $this->getPreResolvedValue($contextValue, $query);
            $value            = $queryType->serialize($preResolvedValue);
        } else {
            if (!$this->resolveValidator->validateArguments($queryType, $query, $this->request)) {
                return null;
            }

            $resolvedValue = $this->resolveValue($queryType, $contextValue, $query);
            $alias         = $query->hasAlias() ? $query->getAlias() : $query->getName();

            if (!$this->resolveValidator->validateResolvedValue($resolvedValue, $currentLevelSchema->getKind())) {
                $this->resolveValidator->addError(new ResolveException(sprintf('Not valid resolved value for schema "%s"', $queryType->getName())));

                return [$alias => null];
            }

            $value = [];
            if ($queryType->getKind() == TypeMap::KIND_LIST) {
                foreach ($resolvedValue as $resolvedValueItem) {
                    $value[] = [];
                    $index   = count($value) - 1;

                    $value[$index] = $this->processQueryFields($query, $queryType, $resolvedValueItem, $value[$index]);
                }
            } else {
                $value = $this->processQueryFields($query, $queryType, $resolvedValue, $value);
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
     * @param $queryType    ObjectType
     * @param $contextValue mixed
     * @param $query        Query
     *
     * @return mixed
     */
    protected function resolveValue($queryType, $contextValue, $query)
    {
        return $queryType->resolve($contextValue, $this->parseArgumentsValues($queryType, $query));
    }

    /**
     * @param $queryType ObjectType
     * @param $query     Query
     *
     * @return array
     */
    public function parseArgumentsValues($queryType, $query)
    {
        $args      = [];
        $arguments = $queryType->getArguments();

        foreach ($query->getArguments() as $argument) {
            $type = $arguments[$argument->getName()]->getConfig()->getType();

            $args[$argument->getName()] = $type->parseValue($argument->getValue()->getValue());
        }

        return $args;
    }

    /**
     * @param $query         Query
     * @param $queryType     ObjectType|Field
     * @param $resolvedValue mixed
     * @param $value         array
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function processQueryFields($query, $queryType, $resolvedValue, $value)
    {
        foreach ($query->getFields() as $field) {
            if ($field instanceof FragmentReference) {
                if (!$fragment = $this->request->getFragment($field->getName())) {
                    throw new \Exception(sprintf('Fragment reference "%s" not found', $field->getName()));
                }

                if ($fragment->getModel() !== $queryType->getName()) {
                    throw new \Exception(sprintf('Fragment reference "%s" not found on model "%s"', $field->getName(), $queryType->getName()));
                }

                foreach ($fragment->getFields() as $fragmentField) {
                    $value = $this->collectValue($value, $this->executeQuery($fragmentField, $queryType, $resolvedValue));
                }
            } else {
                $value = $this->collectValue($value, $this->executeQuery($field, $queryType, $resolvedValue));
            }
        }

        return $value;
    }

    protected function collectValue($value, $queryValue)
    {
        if ($queryValue && is_array($queryValue)) {
            $value = array_merge($value, $queryValue);
        } else {
            $value = $queryValue;
        }

        return $value;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return ResolveValidatorInterface
     */
    public function getResolveValidator()
    {
        return $this->resolveValidator;
    }

    public function getResponseData()
    {
        $result = [];

        if ($this->data) {
            $result['data'] = $this->data;
        }

        if ($this->resolveValidator->hasErrors()) {
            $result['errors'] = $this->resolveValidator->getErrorsArray();
        }

        return $result;
    }
}