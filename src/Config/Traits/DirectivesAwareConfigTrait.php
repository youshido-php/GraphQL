<?php

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Directive\Directive;
use Youshido\GraphQL\Directive\DirectiveInterface;
use Youshido\GraphQL\Field\InputField;

/**
 * Trait DirectivesAwareConfigTrait
 */
trait DirectivesAwareConfigTrait
{
    protected $directives = [];
    protected $_isDirectivesBuilt;

    /**
     * Configure class properties
     */
    public function buildDirectives()
    {
        if ($this->_isDirectivesBuilt) {
            return;
        }

        if (!empty($this->data['directives'])) {
            $this->addDirectives($this->data['directives']);
        }
        $this->_isDirectivesBuilt = true;
    }

    /**
     * @param array $directives
     *
     * @return $this
     */
    public function addDirectives($directives)
    {
        foreach ($directives as $directiveName => $directiveInfo) {
            if ($directiveInfo instanceof Directive) {
                $this->directives[$directiveInfo->getName()] = $directiveInfo;
                continue;
            }

            $this->addDirective($directiveName, $this->buildConfig($directiveName, $directiveInfo));
        }

        return $this;
    }

    /**
     * @param string|DirectiveInterface $directive
     * @param array|null                $info
     *
     * @return $this
     */
    public function addDirective($directive, $info = null)
    {
        if (!($directive instanceof Directive)) {
            $directive = new Directive($this->buildConfig($directive, $info));
        }
        $this->directives[$directive->getName()] = $directive;

        return $this;
    }

    /**
     * @param $name
     *
     * @return InputField
     */
    public function getDirective($name)
    {
        return $this->hasDirective($name) ? $this->directives[$name] : null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasDirective($name)
    {
        return array_key_exists($name, $this->directives);
    }

    /**
     * @return bool
     */
    public function hasDirectives()
    {
        return !empty($this->directives);
    }

    /**
     * @return InputField[]
     */
    public function getDirectives()
    {
        return $this->directives;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeDirective($name)
    {
        if ($this->hasDirective($name)) {
            unset($this->directives[$name]);
        }

        return $this;
    }
}
