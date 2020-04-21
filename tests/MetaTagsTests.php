<?php

use mxschll\MetaTags\Facades\MetaTags as FacadesMetaTags;
use mxschll\MetaTags\MetaTags;
use mxschll\MetaTags\MetaTagsServiceProvider;
use Orchestra\Testbench\TestCase;

class MetaTagsTests extends TestCase
{

    protected static $meta;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [MetaTagsServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Meta' => FacadesMetaTags::class,
        ];
    }

    /**
     * Generates random string with a specified length.
     * 
     * @var int $length
     * 
     * @return string
     */
    private function generateString(int $length): string
    {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $string = "";

        $characters_length = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, $characters_length - 1);
            $string = $string . $characters[$index];
        }

        return $string;
    }

    /**
     * Returns string between two strings.
     *
     * @var string $string
     * @var string $start
     * @var string $end
     * 
     * @return string
     */
    private function getStringBetween(string $string, string $start, string $end): string
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Set tags and test if tags contain the generated string.
     */
    public function testSetTags()
    {
        $string = $this->generateString(10);

        // Set tags
        foreach (config('meta-tags.meta_tags') as $meta_tags) {
            foreach ($meta_tags['tags'] as $tag => $value) {
                Meta::set($tag, $string);
            }
        }

        // Test if tags contain string
        foreach (config('meta-tags.meta_tags') as $meta_tags) {
            foreach ($meta_tags['tags'] as $tag => $value) {
                $this->assertStringContainsString($string, Meta::buildTag($tag));
            }
        }

        // Set special tags
        Meta::set('title', $string);
        $this->assertStringContainsString($string, Meta::buildTag('title'));
        Meta::set('charset', $string);
        $this->assertStringContainsString($string, Meta::buildTag('charset'));
    }


    /**
     * Test if [url] gets replaced with request url.
     */
    public function testUrlPlaceholder()
    {
        foreach (config('meta-tags.meta_tags') as $meta_tags) {
            foreach ($meta_tags['tags'] as $tag => $value) {
                if ($value == '[url]') {
                    $this->assertStringContainsString("http://localhost", Meta::buildTag($tag));
                }
            }
        }

        Meta::set('canonical', '[url]');
        $meta = Meta::buildTag('canonical');
        $url = $this->getStringBetween($meta, 'href="', '">');
        $this->assertEquals('http://localhost', $url);
    }

    /**
     * Test if [asset:ressource] loads the ressource
     */
    public function testAssetPlaceholder()
    {
        Meta::set('og:image', '[asset:img/social.png]');
        $meta = Meta::buildTag('og:image');
        $url = $this->getStringBetween($meta, 'content="', '">');
        $this->assertEquals('http://localhost/img/social.png', $url);
    }

    /**
     * Check if title format rule is applied.
     */
    public function testTitleFormat()
    {
        Meta::set('title', 'Hello World!');
        $this->assertEquals('<title>Hello World! - Laravel</title>', Meta::buildTag('title'));
    }

    /**
     * Test if max_length rule is applied and strings are trimmed correctly.
     */
    public function testRuleMaxLength()
    {
        $string = $this->generateString(500);

        // Rule: '/^description/' => 150
        Meta::set('description', $string);
        $string_ = $this->getStringBetween(
            Meta::buildTag('description'),
            'content="',
            '"'
        );
        $this->assertEquals(config('meta-tags.rules.max_length./^description/'), strlen($string_));
        $this->assertStringContainsString(substr($string_, 0, -3), $string);

        // Rule: '/description$/' => 200
        Meta::set('og:description', $string);
        $string_ = $this->getStringBetween(
            Meta::buildTag('og:description'),
            'content="',
            '"'
        );
        $this->assertEquals(config('meta-tags.rules.max_length./description$/'), strlen($string_));
        $this->assertStringContainsString(substr($string_, 0, -3), $string);

        // Rule: '/description$/' => 200
        Meta::set('twitter:description', $string);
        $string_ = $this->getStringBetween(
            Meta::buildTag('twitter:description'),
            'content="',
            '"'
        );
        $this->assertEquals(config('meta-tags.rules.max_length./description$/'), strlen($string_));
        $this->assertStringContainsString(substr($string_, 0, -3), $string);
    }
}
