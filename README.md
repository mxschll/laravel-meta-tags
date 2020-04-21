# Laravel Meta Tags

[![Build Status](https://travis-ci.org/mxschll/laravel-meta-tags.svg?branch=master)](https://travis-ci.org/mxschll/laravel-meta-tags)

This package allows you easily to manage HTML meta tags from your controllers, blade views or really everywhere else. It is super customizable and easy to configure.

___

## Installation

Navigate to the root directory of your Laravel project and run the following command from your console:

```
composer require mxschll/laravel-meta-tags
```

The package will automatically register a service provider.

In order to configure your installation you need to publish the configuration file:

```
php artisan vendor:publish --provider="mxschll\MetaTags\MetaTagsServiceProvider"
```

You will find the configuration file at `src/config/meta-tags.php`.



## First Step

To get started, add the `@meta` Blade directive inside the `<head>` tag of your page:

```php+HTML
<head>
    <title>Laravel</title>
    @meta
    ....
</head>
```
Now all the standard tags are inserted into the page as set in the configuration file.



## Usage in Blade Templates

### Add specific meta tags

If you only want to add specific tags, you can do so by using the Blade directive `@meta_get()`:

```html
<head>
    <title>Laravel</title>
    @meta_get('keywords')
    @meta_get('og:description')
    ....
</head>
```

### Set meta tag values 

To set meta tags dynamically inside a Blade template, use `@meta_set()`:

```html
<head>
    <title>Laravel</title>
    @meta_set({
    	"description": "This is a very nice description.",
    	"robots": "noindex"
	})
    @meta
    ....
</head>
```

This generates the following meta tags:

```html
...
<meta name="description" content="This is a very nice description.">
<meta name="twitter:description" content="This is a very nice description.">
<meta property="og:description" content="This is a very nice description.">
<meta name="robots" content="noindex">
...
```

As you can see, the values of all description tags have changed. Of course, you can also set individual values for each tag by giving the exact tag names:

```html
<head>
    <title>Laravel</title>
    @meta_set({
    	"description": "This is a very nice description.",
    	"twitter:description": "This is a twitter card description.",
    	"og:description": "This is an open graph description."
    	"robots": "noindex"
	})
    @meta
    ....
</head>
```

This generates the following meta tags:

```html
...
<meta name="description" content="This is a very nice description.">
<meta name="twitter:description" content="This is a twitter card description.">
<meta property="og:description" content="This is an open graph description.">
<meta name="robots" content="noindex">
...
```



## Usage in Controllers

You can set meta tags anywhere in your Laravel application by using `\Meta::set($tag, $value)`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct()
    {
        \Meta::set('og:site_name:', 'Laravel');
    }
    
    public function showHomePage(Request $request)
    {
        \Meta::set('description', 'This is the home page description.');
        return view('home');
    }

    public function showArticlePage(Request $request)
    {
        \Meta::set('description', 'This is the article page description.');
        \Meta::set('og:type', 'article');
        return view('article');
    }
}

```



## Usage in Routes

```php
Route::get('/', function () {
    Meta::set('description', 'Hello World!');
    return view('welcome');
});
```



## Configuration

There are some special values you can use when setting meta tag values. These can not only be used in the configuration file, but everywhere where meta tags can be set.

| Value               | Description                                          | Example                  |
| ------------------- | ---------------------------------------------------- | ------------------------ |
| `[url]`             | Gets replaced with the current request url.          | `[url]`                  |
| `[asset:ressource]` | Passes `ressource` to the `asset()` helper function. | `[asset:img/social.png]` |