<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Sinnbeck\DomAssertions\DomParser;

trait InteractsWithParser
{
    protected function makeScopedParser($root = null): DomParser
    {
        $clone = $this->parser->cloneFromRoot();

        if (is_string($root)) {
            $clone->setRootFromString($root);
        } elseif ($root instanceof \DOMElement) {
            $clone->setRoot($root);
        }

        return $clone;
    }

    protected function getContent()
    {
        return $this->parser->getContent();
    }
}