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
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Object\InputObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeInterface;
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
        $this->data = [];

        try {
            $this->parseAndCreateRequest($queryString, $variables);

            if($this->request->hasQueries()){
                foreach ($this->request->getQueries() as $query) {
                    if ($queryResult = $this->executeQuery($query, $this->getSchema()->getQueryType())) {
                        $this->data = array_merge($this->data, $queryResult);
                    };
                }
            }

            if($this->request->hasMutations()){
                foreach($this->request->getMutations() as $mutation){
                    if ($mutationResult = $this->executeMutation($mutation, $this->getSchema()->getMutationType())) {
                        $this->data = array_merge($this->data, $mutationResult);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->resolveValidator->clearErrors();

            $this->resolveValidator->addError($e);
        }
    }

    /**
     * @param Mutation        $mutation
     * @param InputObjectType $objectType
     *
     * @return array|bool|mixed
     */
    protected function executeMutation($mutation, $objectType)
    {
        if (!$objectType->getConfig()->hasField($mutation->getName())) {
            $this->resolveValidator->addError(new ResolveException(sprintf('Field "%s" not found in schema', $mutation->getName())));

            return null;
        }

        /** @var InputObjectType $inputType */
        $inputType = $objectType->getConfig()->getField($mutation->getName())->getType();

        if (!$this->resolveValidator->validateArguments($inputType, $mutation, $this->request)) {
            return null;
        }

        $alias         = $mutation->hasAlias() ? $mutation->getAlias() : $mutation->getName();
        $resolvedValue = $this->resolveValue($inputType, null, $mutation);

        if (!$this->resolveValidator->validateResolvedValue($resolvedValue, $inputType->getKind())) {
            $this->resolveValidator->addError(new ResolveException(sprintf('Not valid resolved value for mutation "%s"', $inputType->getName())));

            return [$alias => null];
        }

        $value = null;
        if ($mutation->hasFields()) {
            $outputType = $inputType->getConfig()->getOutputType();

            $value = $this->processQueryFields($mutation, $outputType, $resolvedValue, []);
        }

        return [$alias => $value];
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
        if (!$currentLevelSchema->getConfig()->hasField($query->getName())) {
            $this->resolveValidator->addError(new ResolveException(sprintf('Field "%s" not found in schema', $query->getName())));

            return null;
        }

        /** @var ObjectType|Field $queryType */
        $queryType = $currentLevelSchema->getConfig()->getField($query->getName())->getType();
        if (get_class($query) == 'Youshido\GraphQL\Parser\Ast\Field') {
            $alias            = $query->getName();
            $preResolvedValue = $this->getPreResolvedValue($contextValue, $query);
            $value            = $this->resolveValue($queryType, $preResolvedValue, $query);
        } else {
            if (!$this->resolveValidator->validateArguments($queryType, $query, $this->request)) {
                return null;
            }

            $resolvedValue = $this->resolveValue($queryType, $contextValue, $query);
            $alias         = $query->hasAlias() ? $query->getAlias() : $query->getName();

            if (!$this->resolveValidator->validateResolvedValue($resolvedValue, $currentLevelSchema->getKind())) {
                $this->resolveValidator->addError(new ResolveException(sprintf('Not valid resolved value for query "%s"', $queryType->getName())));

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
        if($query instanceof \Youshido\GraphQL\Parser\Ast\Field){
            return [];
        }

        $args      = [];
        $arguments = $queryType->getConfig()->getArguments();

        foreach ($query->getArguments() as $argument) {
            $type = $arguments[$argument->getName()]->getConfig()->getType();

            $args[$argument->getName()] = $type->parseValue($argument->getValue()->getValue());
        }

        return $args;
    }

    /**
     * @param $query         Query
     * @param $queryType     ObjectType|TypeInterface|Field
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