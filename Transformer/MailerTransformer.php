<?php

namespace Socloz\MonitoringBundle\Transformer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MailerTransformer
{
    /**
     * Transform a value into its array value
     *
     * @param mixed $value
     * @return array|string
     */
    public function transform($value)
    {
        if ($value instanceof \Traversable || is_array($value)) {
            return $this->transformIterable($value);
        }

        if (is_bool($value)) {
            return $this->transformBoolean($value);
        }

        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            return $value;
        }

        if ($value instanceof Cache) {
            return $this->transformCacheAnnotation($value);
        }

        if ($value instanceof Method) {
            return $this->transformMethodAnnotation($value);
        }

        if ($value instanceof ParamConverter) {
            return $this->transformParamConverter($value);
        }

        if ($value instanceof Security) {
            return $this->transformSecurity($value);
        }

        if ($value instanceof Template) {
            return $this->transformTemplate($value);
        }

        return get_class($value);
    }

    /**
     * Transform iterable value into array
     *
     * @param \Traversable|array $value
     * @return array
     */
    protected function transformIterable($value)
    {
        $params = array();

        foreach ($value as $key => $val) {
            if (!empty($val)) {
                $params[$key] = $this->transform($val);
            }
        }

        return $params;
    }

    /**
     * Transform boolean value into string
     *
     * @param bool $value
     * @return string
     */
    protected function transformBoolean($value)
    {
        return $value ? 'Yes' : 'No';
    }

    /**
     * Transform Cache Symfony annotation into equivalent array
     *
     * @param Cache $cache
     * @return array
     */
    protected function transformCacheAnnotation(Cache $cache)
    {
        return array(
            'expires' => $cache->getExpires(),
            'maxage' => $cache->getMaxAge(),
            'smaxage' => $cache->getSMaxAge(),
            'public' => $this->transformBoolean($cache->isPublic()),
            'vary' => $cache->getVary(),
            'lastModified' => $cache->getLastModified(),
            'etag' => $cache->getETag(),
        );
    }

    /**
     * Transform Method Symfony annotation into string equivalent
     *
     * @param Method $method
     * @return string
     */
    protected function transformMethodAnnotation(Method $method)
    {
        return implode(', ', $method->getMethods());
    }

    protected function transformParamConverter(ParamConverter $paramConverter)
    {
        return array(
            'name' => $paramConverter->getName(),
            'class' => $paramConverter->getClass(),
            'options' => $this->transform($paramConverter->getOptions()),
            'optional' => $this->transformBoolean($paramConverter->isOptional()),
            'converter' => $paramConverter->getConverter(),
        );
    }

    /**
     * Transform Security Symfony annotation into string equivalent
     *
     * @param Security $security
     * @return mixed
     */
    protected function transformSecurity(Security $security)
    {
        return $security->getExpression();
    }

    /**
     * Transform Template Symfony annotation into array equivalent
     *
     * @param Template $template
     * @return array
     */
    protected function transformTemplate(Template $template)
    {
        return array(
            'template' => $template->getTemplate(),
            'engine' => $template->getEngine(),
            'vars' => $this->transform($template->getVars()),
            'streamable' => $this->transformBoolean($template->isStreamable()),
        );
    }
}
