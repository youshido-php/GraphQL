<?php

namespace Youshido\GraphQL\Execution\PropertyAccessor;

/**
 * Class DefaultPropertyAccessor
 */
class DefaultPropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @param mixed  $object
     * @param string $property
     *
     * @return mixed
     */
    public function getValue($object, $property)
    {
        if (is_object($object)) {
            $getter = $property;
            if (0 !== strpos($property, 'is')) {
                $getter = 'get' . $this->classify($property);

                if (!is_callable([$object, $getter])) {
                    $getter = 'is' . $this->classify($property);
                }

                if (!is_callable([$object, $getter])) {
                    $getter = $this->classify($property);
                }
            }

            if (is_callable([$object, $getter])) {
                return $object->$getter();
            }

            return isset($object->$property) ? $object->$property : null;
        }

        if (is_array($object)) {
            return array_key_exists($property, $object) ? $object[$property] : null;
        }

        return null;
    }

    private function classify($text)
    {
        $text       = explode(' ', str_replace(['_', '/', '-', '.'], ' ', $text));
        $textLength = count($text);

        for ($i = 0; $i < $textLength; $i++) {
            $text[$i] = ucfirst($text[$i]);
        }

        $text = ucfirst(implode('', $text));

        return $text;
    }
}
