<?php
/**
 * Date: 03/17/2017
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
 */

namespace Youshido\GraphQL\Config\Traits;


use Youshido\GraphQL\Directive\Directive;
use Youshido\GraphQL\Field\InputField;

trait DirectivesAwareConfigTrait
{
    protected $directives = [];
    protected $_isDirectivesBuilt;

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

    public function addDirectives($directiveList)
    {
        foreach ($directiveList as $directiveName => $directiveInfo) {
            if ($directiveInfo instanceof Directive) {
                $this->directives[$directiveInfo->getName()] = $directiveInfo;
                continue;
            } else {
                $this->addDirective($directiveName, $this->buildConfig($directiveName, $directiveInfo));
            }
        }

        return $this;
    }

    public function addDirective($directive, $directiveInfo = null)
    {
        if (!($directive instanceof Directive)) {
            $directive = new Directive($this->buildConfig($directive, $directiveInfo));
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
     * @param $name
     *
     * @return bool
     */
    public function hasDirective($name)
    {
        return array_key_exists($name, $this->directives);
    }

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

    public function removeDirective($name)
    {
        if ($this->hasDirective($name)) {
            unset($this->directives[$name]);
        }

        return $this;
    }

}
