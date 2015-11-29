<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 1:05 AM
*/

namespace Youshido\GraphQL;

use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Validator\ValidatorInterface;

class Processor
{

    /** @var  array */
    private $data;

    /** @var ValidatorInterface */
    private $validator;

    /** @var Schema */
    private $schema;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function processQuery($queryString, $variables = [])
    {
        $this->preProcess();

        try {
            $request = $this->createRequestFromString($queryString);

            $querySchema = $this->getSchema();

//        $fieldListBuilder = new FieldListBuilder();
//        $querySchema->getFields($fieldListBuilder);

            $data = [];

            foreach ($request->getQueries() as $query) {
                $data = array_merge($data, $this->executeQuery($query, $variables));
            }

            $this->data = $data;

        } catch (\Exception $e) {
            $this->errorList->addError($e);
        }
    }

    protected function executeQuery($query, $variables = []) {
        return [$query->getName() => []];
    }

    protected function preProcess()
    {
        $this->validator->clearErrors();
    }

    protected function createRequestFromString($query)
    {
        $parser = new Parser($query);

        /** @todo: Parser parse and then getRequest or Request::createFrom... */
        return $parser->parse();
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
     * TODO: this code not end result, just test
     *
     * @param ListBuilderInterface $listBuilder
     * @param Query|Field $query
     * @param null $value
     * @param array $data
     */
    protected function executeQueryOld(ListBuilderInterface $listBuilder, $query, $value = null, &$data)
    {
        if ($listBuilder->has($query->getName())) {
            $querySchema = $listBuilder->get($query->getName());

            $fieldListBuilder = new FieldListBuilder();
            $querySchema->getType()->getFields($fieldListBuilder);

            if ($query instanceof Field) {
                $preResolvedValue = $this->getPreResolvedValue($value, $query);
                $resolvedValue    = $querySchema->getType()->resolve($preResolvedValue, []);

                $data[$query->getName()] = $resolvedValue;
            } else {
                //todo: replace variables with arguments
                //todo: here check arguments

                $resolvedValue = $querySchema->getType()->resolve($value, $query->getArguments());

                //todo: check result is equal to type

                $valueProperty        = $query->hasAlias() ? $query->getAlias() : $query->getName();
                $data[$valueProperty] = [];

                if ($querySchema->getType() instanceof ListType) {
                    foreach ($resolvedValue as $resolvedValueItem) {
                        $data[$valueProperty][] = [];
                        foreach ($query->getFields() as $field) {
                            $this->executeQuery($fieldListBuilder, $field, $resolvedValueItem, $data[$valueProperty][count($data[$valueProperty]) - 1]);
                        }
                    }
                } else {
                    foreach ($query->getFields() as $field) {
                        $this->executeQuery($fieldListBuilder, $field, $resolvedValue, $data[$valueProperty]);
                    }
                }
            }
        } else {
            $this->errorList->addError(new \Exception(
                                           sprintf('Field "%s" not found in schema', $query->getName())
                                       ));
        }
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
            $result['errors'] = $this->validator->getErrors();
        }

        return $result;
    }
}