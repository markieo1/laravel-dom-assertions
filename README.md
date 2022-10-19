# Laravel Dom Assertions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sinnbeck/laravel-dom-assertions.svg?style=flat-square)](https://packagist.org/packages/sinnbeck/laravel-dom-assertions)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/sinnbeck/laravel-dom-assertions/run-tests?label=tests)](https://github.com/sinnbeck/laravel-dom-assertions/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/sinnbeck/laravel-dom-assertions/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/sinnbeck/laravel-dom-assertions/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sinnbeck/laravel-dom-assertions.svg?style=flat-square)](https://packagist.org/packages/sinnbeck/laravel-dom-assertions)

This package provides some extra assertion helpers to use in HTTP Tests. If you have ever needed more control over your view assertions than `assertSee`, `assertSeeInOrder`, `assertSeeText`, `assertSeeTextInOrder`, `assertDontSee`, and `assertDontSeeText`, then this is the package for you.

## Installation

You can install the package via composer:

```bash
composer require sinnbeck/laravel-dom-assertions --dev
```

## Usage

### Testing forms
When calling a route in a test you might want to make sure that the view contains a form. To test this you can use the `->assertForm()` method on the test response.
```php
$this->get('/some-route')
    ->assertForm();
```
The `->assertForm()` method will check the first form it finds. In case you have more than one form, and want to use a different form that the first, you can supply a css selector as the first argument to get a specific one.
```php
$this->get('/some-route')
    ->assertForm('#users-form');
```
If there is more than one hit, it will return the first matching form.
```php
$this->get('/some-route')
    ->assertForm(null, 'nav .logout-form');
```
The second argument of `->assertForm()` is a closure that receives an instance of `\Sinnbeck\DomAssertions\Asserts\FormAssert`. This allows you to assert things about the form itself. Here we are asserting that it has a certain action and method
```php
$this->get('/some-route')
    ->assertForm('#form1', function (FormAssert $form) {
        $form->hasAction('/logout')
            ->hasMethod('post');
    });
```
If you leave out the css selector, it will automatically default to finding the first form on the page
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->hasAction('/logout')
            ->hasMethod('post');
    });
```

You can also check for csrf and method spoofing
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->hasAction('/update-user')
            ->hasMethod('post')
            ->hasCSRF()
            ->hasSpoofMethod('PUT');
    });
```
Checking for methods other than GET and POST will automatically forward the call to `->hasSpoofMethod()`
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->hasMethod('PUT');
    });
```
Or even arbitrary attributes
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->has('x-data', 'foo')
        $form->hasEnctype('multipart/form-data'); //it also works with magic methods
    });
```

You can also easily test for inputs or text areas 
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->containsInput([
            'name' => 'first_name',
            'value' => 'Gunnar',
        ])
        ->containsTextarea([
            'name' => 'comment',
            'value' => '...',
        ]);
    });
```
Or arbitrary children
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->contains('label', [
            'for' => 'username',
        ])
        ->containsButton([ //or use a magic method
            'type' => 'submit',
        ]);
    });
```
Testing for selects is also easy and works a bit like the `assertForm()`. It takes a selector as the first argument, and closure as the second argument. The second argument returns an instance of `\Sinnbeck\DomAssertions\Asserts\SelectAssert`. This can be used to assert that the select has certain attributes.
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->containsSelect('select:nth-of-type(2)', function (SelectAssert $selectAssert) {
            $selectAssert->has('name', 'country')
        });
    });
```
You can also assert that it has certain options. You can either check for one specific or an array of options
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->containsSelect(function (SelectAssert $selectAssert) {
            $selectAssert->containsOption([
                [
                    'x-data' => 'none',
                    'value'  => 'none',
                    'text'   => 'None',
                ]
            ])
            ->containsOptions(
                [
                    'value' => 'dk',
                    'text'  => 'Denmark',
                ],
                [
                    'value' => 'us',
                    'text'  => 'USA',
                ],
            );
        }, 'select:nth-of-type(2)');
    });
```
It also works with closures if you prefer that syntax. The closure retuns an instance of `\Sinnbeck\DomAssertions\Asserts\OptionAssert`
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->containsSelect('select:nth-of-type(2)', function (SelectAssert $selectAssert) {
            $selectAssert->containsOption(function (OptionAssert $optionAssert) {
                $optionAssert->hasValue('none');
                $optionAssert->hasText('None');
                $optionAssert->hasXData('none');
            })
            ->containsOptions(
                function (OptionAssert $optionAssert) {
                    $optionAssert->hasValue('dk');
                    $optionAssert->hasText('Denmark');
                },
                function (OptionAssert $optionAssert) {
                    $optionAssert->hasValue('us')
                        ->hasText('USA');
                },
            );
        });
    });
```
### Testing regular dom
The testing of generic html elements works a lot like forms.
When calling a route in a test you might want to make sure that the view contains certain elements. To test this you can use the `->assertElement()` method on the test response.
The following will ensure that there is a body tag in the parsed response. Be aware that this package assumes a proper html structure and will wrap your html in a html and body tag if one is missing!
```php
$this->get('/some-route')
    ->assertElement();
```
In case you want to get a specific element on the page, you can supply a css selector as the first argument to get a specific one.
```php
$this->get('/some-route')
    ->assertElement('#nav');
```
The second argument of `->assertElement()` is a closure that receives an instance of \Sinnbeck\DomAssertions\Asserts\ElementAssert. This allows you to assert things about the elementitself. Here we are asserting that the element is an `div`.

```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->is('div');
    });
```
Just like with forms you can assert that certain attributes are present
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->has('x-data', '{foo: 1}');
    });
```
You can also ensure that certain children exist.
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->contains('div');
    });
```
Be aware that this will only check on the first child of that type. If you need to be more specific you can use a css selector.
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->contains('div:nth-of-type(3)');
    });
```
You can also check that the child element has certain attributes
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->contains('div:nth-of-type(3)', [
            'x-data' => 'foobar'
        ]);
    });
```
For even more power you are allowed to use a closure as the second argument. This lets you traverse the dom as deep as you need to.
```php
$this->get('/some-route')
    ->assertElement(function (ElementAssert $element) {
        $element->contains('div', function (ElementAssert $element) {
            $element->is('div');
            $element->contains('p', function (ElementAssert $element) {
                $element->is('p');
                $element->contains('#label', function (ElementAssert $element) {
                    $element->is('span');
                });
            });
        });
    });
```
## Testing this package

```bash
vendor/bin/pest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [René Sinnbeck](https://github.com/sinnbeck)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.