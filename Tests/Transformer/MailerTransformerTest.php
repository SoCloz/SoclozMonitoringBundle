<?php

namespace Socloz\MonitoringBundle\Tests\Transformer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Socloz\MonitoringBundle\Transformer\MailerTransformer;
use Symfony\Component\HttpFoundation\ParameterBag;

class MailerTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransformIterableWithTraversableObject()
    {
        $transformer = new MailerTransformer();

        $traversableObject = new ParameterBag(array('foo' => 'bar', 'john' => 'doe'));
        $expected = array('foo' => 'bar', 'john' => 'doe');

        $this->assertEquals($transformer->transform($traversableObject), $expected);
    }

    public function testTransformIterableWithArray()
    {
        $transformer = new MailerTransformer();

        $array = array('foo' => 'bar', 'john' => 'doe');

        $this->assertEquals($transformer->transform($array), $array);
    }

    public function testTransformBoolean()
    {
        $transformer = new MailerTransformer();

        $this->assertEquals($transformer->transform(true), 'Yes');
        $this->assertEquals($transformer->transform(false), 'No');
    }

    public function testTransformCacheAnnotation()
    {
        $transformer = new MailerTransformer();

        $cache = new Cache(array(
            'etag' => $hash = md5('etag'),
            'expires' => '+1 day',
            'maxage' => 3600,
            'smaxage' => 1800,
            'public' => true,
            'vary' => array('foo' => 'bar')
        ));

        $expected = array(
            'expires' => '+1 day',
            'maxage' => 3600,
            'smaxage' => 1800,
            'public' => 'Yes',
            'vary' => array('foo' => 'bar'),
            'etag' => $hash,
            'lastModified' => null,
        );

        $this->assertEquals($transformer->transform($cache), $expected);
    }

    public function testTransformMethodAnnotation()
    {
        $transformer = new MailerTransformer();

        $method = new Method(array('methods' => 'GET'));
        $this->assertEquals($transformer->transform($method), 'GET');

        $method = new Method(array('methods' => array('GET')));
        $this->assertEquals($transformer->transform($method), 'GET');

        $method = new Method(array('methods' => array('GET', 'POST')));
        $this->assertEquals($transformer->transform($method), 'GET, POST');
    }

    public function testTransformParamConverter()
    {
        $transformer = new MailerTransformer();

        $paramConverter = new ParamConverter(array(
            'name' => 'post',
            'class' => 'AcmeBlogBundle:Post',
        ));

        $expected = array(
            'name' => 'post',
            'class' => 'AcmeBlogBundle:Post',
            'options' => array(),
            'optional' => 'No',
            'converter' => null,
        );

        $this->assertEquals($transformer->transform($paramConverter), $expected);
    }

    public function testTransformSecurity()
    {
        $transformer = new MailerTransformer();

        $security = new Security(array('expression' => 'symfony_expression'));

        $this->assertEquals($transformer->transform($security), 'symfony_expression');
    }

    public function testTransformTemplate()
    {
        $transformer = new MailerTransformer();

        $template = new Template(array(
            'template' => 'AcmeBlogBundle:Default:template.html.twig',
        ));

        $expected = array(
            'template' => 'AcmeBlogBundle:Default:template.html.twig',
            'engine' => 'twig',
            'vars' => array(),
            'streamable' => 'No',
        );

        $this->assertEquals($transformer->transform($template), $expected);
    }

    public function testTransformWithObject()
    {
        $transformer = new MailerTransformer();

        $object = new \StdClass;
        $object->test = 'hello';

        $this->assertEquals($transformer->transform($object), 'stdClass');
    }
}
