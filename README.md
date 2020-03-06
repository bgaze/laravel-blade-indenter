# Basic indenter for Laravel 5.8+ Blade templates

This is a very simple indenter for Laravel Blade template, mainly designed to indent generated files in my [CRUD generator for Laravel](https://github.com/bgaze/laravel-crud).

It won't format or validate code: it just indent existing lines following very simple rules.  
It expects a valid and well formed code, and won't deal well with instructions on multiple lines.

Any contribution or feedback is highly welcomed, please feel free to create a pull request or [submit a new issue](https://github.com/bgaze/laravel-blade-indenter/issues/new).

### Example

##### Input:

```html
@extends('layout')

@section('title', $title)

@section('content')
<h1>
@if($article->exists)
Edit Article #{{ $article->id }}
@php($url = route('articles.update', $article->id))
@else
Create a new Article
@php($url = route('articles.store'))
@endif
</h1>

{!! Form::model($article, ['url' => $url]) !!}

<div id='title-group'>
{!! Form::label('title', 'Title') !!}
{!! Form::text('title') !!}
@error('title')
<p>{{ $message }}</p>
@enderror
</div>

{!! Form::submit('Save') !!}

{!! Form::close() !!}
@endsection
```

##### Output:

```html
@extends('layout')

@section('title', $title)

@section('content')
    <h1>
        @if($article->exists)
            Edit Article #{{ $article->id }}
            @php($url = route('articles.update', $article->id))
        @else
            Create a new Article
            @php($url = route('articles.store'))
        @endif
    </h1>
    
    {!! Form::model($article, ['url' => $url]) !!}
    
    <div id='title-group'>
        {!! Form::label('title', 'Title') !!}
        {!! Form::text('title') !!}
        @error('title')
            <p>{{ $message }}</p>
        @enderror
    </div>
    
    {!! Form::submit('Save') !!}
    
    {!! Form::close() !!}
@endsection
```

### Installation

Simply import the package with composer:

```
composer require bgaze/laravel-blade-indenter
```

Configuration can be published to `/config/blade-indenter.php`:

```
php artisan vendor:publish --tag=blade-indenter-config
```

### Usage

The package exposes a single service which indents Blade string :

```php
use Bgaze\BladeIndenter\BladeIndenter;

$indentedString = resolve(BladeIndenter::class)->indent($stringToIndent);
```

Two helpers are also provided for convenience :

```php
// Indent a string
$indentedString = indent_blade_string($stringToIndent);

// Indent a blade file, overwrite it and return formatted content.
$indentedFileContent = indent_blade_file($filePath);

// Indent a blade file and return formatted content without overwriting.
$indentedFileContent = indent_blade_file($filePath, false);
```

### Configuration

The indenter supports Blade directives described in [official documentation](https://laravel.com/docs/5.8/blade).  
However, if needed, you can customize supported tags and directives (check [package configuration](src/config/blade-indenter.php) for defaults).

Two way to do that:
 
* publish and edit package configuration.
* configure BladeIndenter from the boot section of a Service provider.

As an exemple, here is the way to configure indenter for [bgaze/bootstrap-form](https://github.com/bgaze/bootstrap-form) custom directives.

```php
// From boot method of App\Providers\AppServiceProvider:

resolve(BladeIndenter::class)
    // @close directive closes @open, @vertical, @horizontal and @inline directives
    ->addClosingDirectives([
        'open' => 'close',
        'vertical' => 'close',
        'horizontal' => 'close',
        'inline' => 'close',
    ])
    // Indent level won't change on line after one of these directives 
    ->addSelfClosingDirectives([
        'text', 'email', 'url', 'tel', 'number', 'date', 'time', 'textarea',
        'password', 'file', 'hidden', 'select', 'range', 'checkbox', 'checkboxes',
        'radio', 'radios', 'label', 'submit', 'reset', 'button', 'link',
    ]);
```


#### Self-closing HTML tags

Indentation level won't increase after these HTML tags.  
Edit `self_closing_tags` section in configuration or use following methods:

```php
/**
 * @param  array  $tags
 * @return BladeIndenter
 */
public function setSelfClosingTags(array $tags)

/**
 * @param  array  $tags
 * @return BladeIndenter
 */
public function addSelfClosingTags(array $tags)
```

#### Self-closing Blade directives

Indentation level won't increase after these directives.  
Edit `self_closing_directives` section in configuration or use following methods:

```php
/**
 * @param  array  $directives
 * @return BladeIndenter
 */
public function setSelfClosingDirectives(array $directives)

/**
 * @param  array  $directives
 * @return BladeIndenter
 */
public function addSelfClosingDirectives(array $directives)
```

#### Closing Blade directives

The "end" version of any directive is supported (for instance **@endsection** for **@section**), but some directives can also be closed by other directives, like  **@show** closes **@section**.

Edit `closing_directives` section in configuration or use following methods to define the mapping of these behaviours (without **@** character).  

```php
/**
 * @param  array  $directives
 * @return BladeIndenter
 */
public function setClosingDirectives(array $directives)

/**
 * @param  array  $directives
 * @return BladeIndenter
 */
public function addClosingDirectives(array $directives)
```
Examples:

```php
'closing_directives' => [
    'section' => 'show',
    'opening_directive_2' => ['closing_directive_21', 'closing_directive_22'],
],  
```

#### "Else" Blade directives

"Else" directive will be indented one level down, but previous level will be preserved on following line.  
Edit `else_directives` section in configuration or use following methods:

```php
/**
 * @param  array  $directives
 * @return BladeIndenter
 */
public function setElseDirectives(array $directives)

/**
 * @param  array  $directives
 * @return BladeIndenter
 */
public function addElseDirectives(array $directives)
```
