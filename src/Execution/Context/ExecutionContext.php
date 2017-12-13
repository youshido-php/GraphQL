<?php

namespace Youshido\GraphQL\Execution\Context;

use Psr\Container\ContainerInterface;
use Youshido\GraphQL\ExceptionHandler\ExceptionHandlerInterface;
use Youshido\GraphQL\Execution\ExceptionConverter\ExceptionConverter;
use Youshido\GraphQL\Execution\ExceptionConverter\ExceptionConverterInterface;
use Youshido\GraphQL\Execution\PropertyAccessor\DefaultPropertyAccessor;
use Youshido\GraphQL\Execution\PropertyAccessor\PropertyAccessorInterface;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Introspection\Field\SchemaField;
use Youshido\GraphQL\Introspection\Field\TypeDefinitionField;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;
use Youshido\GraphQL\Validator\SchemaValidator\SchemaValidator;

/**
 * Class ExecutionContext
 */
class ExecutionContext implements ExecutionContextInterface
{
    use ErrorContainerTrait;

    /** @var AbstractSchema */
    private $schema;

    /** @var Request */
    private $request;

    /** @var ContainerInterface */
    private $container;

    /** @var ExceptionHandlerInterface[] */
    private $errorHandlers = [];

    /** @var  ExceptionConverterInterface */
    private $exceptionConverter;

    /** @var  PropertyAccessorInterface */
    private $propertyAccessor;

    /**
     * ExecutionContext constructor.
     *
     * @param AbstractSchema $schema
     * @param bool           $debug
     */
    public function __construct(AbstractSchema $schema, $debug = false)
    {
        $this->schema             = $schema;
        $this->exceptionConverter = new ExceptionConverter($debug);
        $this->propertyAccessor   = new DefaultPropertyAccessor();
        $this->validateSchema();

        $this->introduceIntrospectionFields();
    }

    /**
     * @return AbstractSchema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param AbstractSchema $schema
     *
     * @return $this
     */
    public function setSchema(AbstractSchema $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return ExceptionConverterInterface
     */
    public function getExceptionConverter()
    {
        return $this->exceptionConverter;
    }

    /**
     * @param ExceptionConverterInterface $exceptionConverter
     */
    public function setExceptionConverter(ExceptionConverterInterface $exceptionConverter)
    {
        $this->exceptionConverter = $exceptionConverter;
    }

    /**
     * @param ExceptionHandlerInterface $handler
     */
    public function addErrorHandler(ExceptionHandlerInterface $handler)
    {
        $this->errorHandlers[] = $handler;
    }

    /**
     * @param \Exception $exception
     */
    public function handleException(\Exception $exception)
    {
        foreach ($this->errorHandlers as $handler) {
            $handler->handle($exception, $this);

            if ($handler->isFinal($exception)) {
                return;
            }
        }

        $this->addError($exception);
    }

    /**
     * @return array
     */
    public function getErrorsData()
    {
        return $this->exceptionConverter->convert($this->getErrors());
    }

    /**
     * @return PropertyAccessorInterface
     */
    public function getPropertyAccessor()
    {
        return $this->propertyAccessor;
    }

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    protected function validateSchema()
    {
        try {
            (new SchemaValidator())->validate($this->schema);
        } catch (\Exception $e) {
            $this->addError($e);
        }
    }

    protected function introduceIntrospectionFields()
    {
        $schemaField = new SchemaField();
        $this->schema->addQueryField($schemaField);
        $this->schema->addQueryField(new TypeDefinitionField());
    }
}
