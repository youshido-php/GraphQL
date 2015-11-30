<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 1:05 AM
*/

namespace Youshido\GraphQL;

use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Type\Object\ObjectType;
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

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
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

            return false;
        }

        $queryType = $currentLevelSchema->getField($query->getName());
        if ($queryType instanceof Field) {
            $value = $queryType->getType()->serialize($contextValue[$queryType->getName()]);
        } else {
            /** @var ObjectType $queryType */
            $resolvedValue = $queryType->resolve();
            $value         = [];
            foreach ($query->getFields() as $field) {
                $value = array_merge($value, $this->executeQuery($field, $queryType, $resolvedValue));
            }
        }


        return [$queryType->getName() => $value];
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