<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hide empty tags
    |--------------------------------------------------------------------------
    |
    | This value determines whether meta tags should be displayed without a set
    | value or not.
    |
    */
    'empty_tags_hidden' => true,

    /*
    |--------------------------------------------------------------------------
    | Charset
    |--------------------------------------------------------------------------
    |
    | Specify the character encoding for the HTML document.
    |
    */
    'charset' => 'utf-8',

    /*
    |--------------------------------------------------------------------------
    | Rules
    |--------------------------------------------------------------------------
    |
    | If the regex pattern matches any meta tag key (eg. twitter:title), the 
    | rule is applied to the value.
    |
    */
    'rules' => [
        'max_length' => [
            '/^description/' => 150,
            '/description$/' => 200,
            '/title$/' => 70,
        ],
        'format' => [
            '/title$/' => '%s - ' . config('app.name'),
        ]
    ],

    'meta_tags' => [
        [
            'format' => '<meta name="%s" content="%s">',
            'tags' => [
                /*
                |--------------------------------------------------------------------------
                | General Purpose
                |--------------------------------------------------------------------------
                */
                'viewport'          => 'width=device-width, initial-scale=1, shrink-to-fit=no',
                'application-name'  => config('app.name'), // Name of web application
                'theme-color'       => '#1d8cf8', // Theme color
                'description'       => '', // Short description of the document
                'subject'           => '',
                'keywords'          => '',
                'referrer'          => 'same-origin',
                'rating'            => 'general',
                'format-detection'  => '',
                'robots'            => 'index,follow',
                'googlebot'         => 'index,follow',

                /*
                |--------------------------------------------------------------------------
                | Site Verification
                |--------------------------------------------------------------------------
                */
                'google-site-verification'  => '', // Google Searcg Console
                'yandex-verification'       => '', // Yandex Webmasters
                'msvalidate.01'             => '', // Bing Webmaster Center
                'alexaVerifyID'             => '', // Alexa Console
                'p:domain_verify'           => '', // Pinterest Console

                /*
                |--------------------------------------------------------------------------
                | Twitter Card
                |--------------------------------------------------------------------------
                */
                'twitter:card'          => 'summary',
                'twitter:site'          => '', // @username
                'twitter:site:id'       => '', // Same as twitter:site, but the user’s Twitter ID
                'twitter:creator'       => '', // @username of content creator
                'twitter:creator:id'    => '', // Twitter user ID of content creator
                'twitter:description'   => '', // Description of content (maximum 200 characters)
                'twitter:title'         => '', // Title of content (max 70 characters)
                'twitter:image'         => '',
                'twitter:image:alt'     => '',
                'twitter:dnt'           => 'on',
                'twitter:url'           => '[url]', // Set to [url] for URl::current()
            ],
        ],

        [
            'format' => '<meta property="%s" content="%s">',
            'tags' => [
                /*
                |--------------------------------------------------------------------------
                | Open Graph
                |--------------------------------------------------------------------------
                */
                'fb:app_id'         => '',
                'og:type'           => 'website',
                'og:title'          => '',
                'og:image'          => '',
                'og:image:alt'      => '',
                'og:description'    => '',
                'og:site_name'      => config('app.name'),
                'og:locale'         => config('app.locale'),
                'article:author'    => '',
                'og:url'            => '[url]', // Set to [url] for URl::current()
            ],
        ],

        [
            'format' => '<link rel="%s" href="%s">',
            'tags' => [
                /*
                |--------------------------------------------------------------------------
                | Prefetching, Preloading, Prebrowsing
                |--------------------------------------------------------------------------
                */
                'dns-prefetch'  => '',
                'preconnect'    => '',
                'prefetch'      => '',
                'prefetch'      => '',
                'prerender'     => '',
                'preload'       => '',
                'canonical'     => '[url]', // Set to [url] for URl::current()
            ],
        ],
    ],
];
