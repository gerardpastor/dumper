<?php

/*
 * This file is part of the Deg package.
 *
 * (c) Gerard Pastor <gerardpastor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deg\Dumper\Parser\Tokenizer;

use Deg\Dumper\Parser\VarParser;
use Deg\Dumper\Parser\TokenStream;
use Deg\Dumper\Parser\TokenStreamBuilder;

/**
 * ObjectTokanizer
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class ObjectTokanizer implements TokenizerInterface
{
    /**
     *
     * @param mixed $var
     * @param integer $deep
     * @param VarParser $parser
     * @return TokenStream
     */
    public function tokenize($var, $deep, VarParser $parser)
    {
        $builder = new TokenStreamBuilder();

        $props = $this->getProps($var);
        $reflect = new \ReflectionClass($var);

        $builder->addKeyword($reflect->getShortName(), get_class($var));

        if (false !== $asString = $this->getAsString($var, $deep)) {
            $builder
                ->addBrace('(')
                ->addStream($parser->parse($asString, -1))
                ->addBrace(')')
            ;
        }

        if ($deep < 0) {
            return $builder->getStream();
        }

        $builder
            ->addWhitespece()
            ->addBrace('{');

        if ($deep > 0) {
            $this->tokenizeProps($builder, $var, $props, $deep, $parser);
        } elseif (count($props)) {
            $builder->addKeyword('…');
        }

        $builder->addBrace('}');

        return $builder->getStream();
    }

    protected function tokenizeProps(TokenStreamBuilder $builder, $var, $props, $deep, VarParser $parser)
    {
        $keyLen = array_reduce($props, function($maxLen, $prop) {
            $len = strlen($prop->getName());
            return $len > $maxLen ? $len : $maxLen;
        });

        $builder->addNewLine();

        foreach ($props as $prop) {

            $propName = $prop->getName();
            $propValue = $prop->getValue($var);
            $modifiers = $this->getPropModifiers($prop);

            $diff = $keyLen - strlen($propName . '');

            $builder
                ->addIndention()
                ->addType(implode('', $modifiers))
                ->addWhitespece()
                ->addBrace('[')
                ->addString('"' . $propName . '"')
                ->addBrace(']')
                ->addWhitespece($diff + 1)
                ->addPunctuation('=>')
                ->addWhitespece()
            ;

            $stream = $parser->parse($propValue, $deep - 1);
            $builder->addStream($stream);

            $builder->addNewLine();
        }
    }

    protected function getAsString($obj)
    {
        if ($obj instanceof \DateTime) {
            return $obj->format('Y-m-d H:i:s');
        } elseif (method_exists($obj, '__toString')) {
            try {
                return $obj->__toString();
            } catch (\Exception $exc) {
            }
        }

        return false;
    }

    protected function getProps($var)
    {
        $reflect = new \ReflectionClass($var);
        $props = array();

        while ($reflect) {
            foreach ($reflect->getProperties() as $prop) {
                $prop->setAccessible(true);
                $key = $prop->getName();
                if (!isset($props[$key])) {
                    $props[$key] = $prop;
                }
            }

            $reflect = $reflect->getParentClass();
        }

        return $props;
    }

    protected function getPropModifiers($prop)
    {
        $propModifiers = \Reflection::getModifierNames($prop->getModifiers());
        $modifiers = array_intersect_key(array(
            'public' => '+',
            'protected' => '*',
            'private' => '-',
            'static' => 's',
            ), array_flip($propModifiers));

        return $modifiers;
    }

    /**
     *
     * @param mixed $var
     * @return boolean
     */
    public function accept($var)
    {
        return is_object($var);
    }

    /**
     *
     * @return int
     */
    public function getConfidence()
    {
        return 10;
    }

}