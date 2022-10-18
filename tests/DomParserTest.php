<?php

use Sinnbeck\DomAssertions\DomParser;

it('can find a form', function () {
    $html = <<<'HTML'
<form id="form1"></form>
<form id="form2"></form>
HTML;

    $parser = DomParser::new($html);

    $form = $parser->query('form:nth-child(2)');
    $this->assertNotNull($form);
    $this->assertEquals($form->getAttribute('id'), 'form2');
});

it('can find a select inside a form', function () {
    $html = <<<'HTML'
<form id="form1"></form>
<form id="form2">
    <input value="foo">
    <select id="">
        <option>Foo</option>
    </select>
    <select id="">
        <option>Foo</option>
    </select>
</form>
HTML;

    $parser = DomParser::new($html);

    $parser->setRoot($parser->query('#form2'));

    $this->assertNotNull($parser->query('select:nth-of-type(2)'));
});

it('can get an attribute', function () {
    $html = <<<'HTML'
<input type="text" value="foo"/>
HTML;

    $parser = DomParser::new($html);

    $this->assertEquals($parser->getAttributeFor('input', 'type'), 'text');
    $this->assertEquals($parser->getAttributeFor('input', 'value'), 'foo');
});

it('can get an element by type', function () {
    $html = <<<'HTML'
<input type="text" value="foo"/>
HTML;

    $parser = DomParser::new($html);

    $this->assertEquals($parser->getElementOfType('input')->nodeName, 'input');
});
