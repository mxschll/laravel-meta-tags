<?php

namespace mxschll\MetaTags;

class MetaTags
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var array
     */
    private $config;
    /**
     * @var array
     */
    private $rules;
    /**
     * @var array
     */
    private $meta_tags;
    /**
     * @var string
     */
    private $charset;
    /**
     * @var string
     */
    private $title = '';
    /**
     * @var array
     */
    private $special_tags = [
        'title', 'charset',
    ];

    public function __construct(string $url, array $config = [])
    {
        $this->url = $url;
        $this->config = $config;
        $this->meta_tags = $config['meta_tags'];
        $this->rules = $config['rules'];
        $this->charset = $config['charset'];
    }

    /**
     * Returns request url.
     * 
     * @return string
     */
    private function getUrl(): string
    {
        return $this->url;
    }


    /**
     * Print html tags.
     * 
     * @return void
     */
    public function toHtml($key = null): void
    {
        echo ($key == null ? implode("\n", $this->buildTags()) : $this->buildTag($key)) . "\n";
    }

    /**
     * Set meta tag.
     * 
     * @param string $key meta tag name
     * @param string $value meta tag value
     * @param bool $discover if method set$key() exists
     * 
     * @return void
     */
    public function set(string $key, string $value = null, bool $discover = true): void
    {
        $method = 'set' . $key;
        if ($discover && method_exists($this, $method)) {
            $this->$method($value);
            return;
        }

        foreach ($this->meta_tags as $index => $meta_tags) {
            if (key_exists($key, $meta_tags['tags'])) {
                $this->meta_tags[$index]['tags'][$key] = $value;
            }
        }
    }

    /**
     * Build given html tag.
     * 
     * @param string $key
     * 
     * @return string
     */
    public function buildTag(string $key): string
    {
        $method = 'build' . $key . 'tag';
        if (method_exists($this, $method)) {
            return $this->$method($key);
        }

        foreach ($this->meta_tags as $meta_tags) {
            if (array_key_exists($key, $meta_tags['tags'])) {
                return sprintf(
                    $meta_tags['format'],
                    $key,
                    $this->applyRules($key, $meta_tags['tags'][$key])
                );
            }
        }
    }

    /**
     * Builds html tags.
     * 
     * @return string html tags
     */
    public function buildTags(): array
    {
        $tags = [];

        foreach ($this->special_tags as $special_tag) {
            $method = 'build' . $special_tag . 'tag';
            if (method_exists($this, $method)) {
                $tags[] = $this->$method();
            }
        }

        foreach ($this->meta_tags as $meta_tags) {
            // Remove empty tags from array
            if ($this->config['empty_tags_hidden']) {
                $meta_tags['tags'] = array_filter($meta_tags['tags']);
            }

            foreach ($meta_tags['tags'] as $key => $value) {
                $tags[] = sprintf(
                    $meta_tags['format'],
                    $key,
                    $this->applyRules($key, $value)
                );
            }
        }

        return $tags;
    }

    /**
     * Builds custom html tag. 
     * 
     * @return string html tags
     */
    public function buildCustomTag(string $tag, array $values): string
    {

        $attributes = [];
        foreach ($values as $key => $value) {
            $attributes[] = $key . '="' . $value . '"';
        }
        $attributes = implode(' ', $attributes);

        return "<$tag {$attributes}>";
    }

    /**
     * Applies the rules specified in the config file.
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return mixed
     */
    private function applyRules(string $key, $value)
    {
        if ($value == null)
            return null;

        $value = $this->replaceSpecialValues($value);
        $value = $this->applyMaxLengthRule($key, $value);
        $value = $this->applyFormatRule($key, $value);

        return $value;
    }

    /**
     * Replaces special values with the corresponding value.
     * Special values: [url], [asset:ressource]
     * 
     * @param string $value
     * 
     * @return string
     */
    private function replaceSpecialValues($value)
    {
        // Repalce [url] with request url
        $value = str_replace(['[url]'], [$this->getUrl()], $value);

        // Replace [asset:ressource] with asset(ressource)
        $match = [];
        if (preg_match('/\[asset:(.*)\]/', $value, $match)) {
            $value = asset($match[1]);
        }

        return $value;
    }

    /**
     * Applies max length rule to $value uf $key matches regex pattern.
     * 
     * @param string $key
     * @param string $value
     * 
     * @return string
     */
    private function applyMaxLengthRule(string $key, $value): string
    {
        $max_length_rules = $this->rules['max_length'];
        foreach ($max_length_rules as $pattern => $length) {
            if (preg_match($pattern, $key)) {
                return $this->trim($value, $length);
            }
        }

        return $value;
    }

    /**
     * Applies format rule to $value if $key matches regex pattern.
     * 
     * @param string $key
     * @param string $value
     * 
     * @return string
     */
    private function applyFormatRule(string $key, $value)
    {
        $format_rules = $this->rules['format'];
        foreach ($format_rules as $pattern => $format) {
            if (preg_match($pattern, $key)) {
                return sprintf($format, $value);
            }
        }

        return $value;
    }

    /**
     * Trim string to ($length - 3) and add three dots.
     * 
     * @param string $string to be trimmed
     * @param int $length of the trimmed string
     * 
     * @return string
     */
    private function trim(string $string, int $lenght): string
    {
        if (strlen($string) > $lenght)
            return rtrim(substr($string, 0, $lenght - 3)) . "...";

        return $string;
    }

    /**
     * Set description meta tags.
     * 
     * @param string $value
     * 
     * @return void
     */
    private function setDescription(string $value = null): void
    {
        $this->set('description', $value, false);
        $this->set('og:description', $value, false);
        $this->set('twitter:description', $value, false);
    }

    /**
     * Set title meta tags.
     * 
     * @param string $value
     * 
     * @return void
     */
    private function setTitle(string $value = null): void
    {
        $this->title = $value;
        $this->set('twitter:title', $value, false);
        $this->set('og:title', $value, false);
    }

    /**
     * Set url meta tags.
     * 
     * @param string $value
     * 
     * @return void
     */
    private function setUrl(string $value): void
    {
        $this->set('twitter:url', $value, false);
        $this->set('og:url', $value, false);
        $this->set('canonical', $value, false);
    }

    /**
     * Set charset meta tag.
     * 
     * @param string $value encoding
     * 
     * @return void
     */
    private function setCharset(string $value): void
    {
        $this->charset = $value;
    }

    /**
     * Set image meta tags.
     * 
     * @param string $value url
     * 
     * @return void
     */
    private function setImage(string $value): void
    {
        $this->set('twitter:image', $value);
        $this->set('og:image', $value);
    }

    /*
    |--------------------------------------------------------------------------
    | Special Tags Builders
    |--------------------------------------------------------------------------
    |
    | The following methods are used to build tags that don't apply to any 
    | standard meta tag format.
    |
    */

    /**
     * Build charset meta tag.
     * 
     * @return string
     */
    private function buildTitleTag(): string
    {
        return "<title>{$this->applyRules('title',$this->title)}</title>";
    }

    /**
     * Build charset meta tag.
     * 
     * @return string
     */
    private function buildCharsetTag(): string
    {
        return $this->buildCustomTag('meta', ['charset' => $this->charset]);
    }
}
