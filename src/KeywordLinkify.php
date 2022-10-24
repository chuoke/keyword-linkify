<?php

namespace Chuoke\KeywordLinkify;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;

class KeywordLinkify
{
    /** @var array Attributes of the a tag */
    protected array $attributes = [];

    /** @var bool Nested substitution, whether to replace a keyword in a keyword */
    protected bool $nested = true;

    /** @var int|null */
    protected int|null $times = null;

    /** @var array<keyword, url, times> */
    protected array $keywords = [];

    protected array $replacingKeywords = [];

    /** @var Exception|null */
    protected $exception;

    public function __construct()
    {
        $this->target('blank');
    }

    public static function make(): static
    {
        return new static();
    }

    public function attributes(array $attributes): static
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    public function target(string $target): static
    {
        return $this->attributes(['target' => $target]);
    }

    /**
     * Set whether to replace nested, which means whether to replace keywords in keywords
     *
     * @param  bool  $nested
     * @return static
     */
    public function nested($nested = true): static
    {
        $this->nested = $nested;

        return $this;
    }

    public function nonnested(): static
    {
        return $this->nested(false);
    }

    public function times(int $times): static
    {
        $this->times = $times;

        return $this;
    }

    public function keywords(array $keywords): static
    {
        if ($this->nested) {
            usort($keywords, function ($a, $b) {
                return mb_strlen($b['keyword']) - mb_strlen($a['keyword']);
            });
        }

        foreach ($keywords as $link) {
            $link['times'] = 0;
            $this->keywords[strtolower($link['keyword'])] = $link;
        }

        return $this;
    }

    /**
     * @param  string  $original
     * @param  array<keyword,url>  $keywords
     * @return string
     */
    public function replace(string $original, array $keywords = []): string
    {
        $this->exception = null;

        try {
            if ($keywords) {
                $this->keywords($keywords);
                unset($keywords);
            }

            $this->replacingKeywords = $this->keywords;

            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->loadHTML('<div>' . mb_convert_encoding($original, 'HTML-ENTITIES', 'UTF-8') . '</div>');
            $domXPath = new DOMXPath($dom);

            foreach ($domXPath->query('//*[not(self::img or self::a)]/text()') as $textNode) {
                if (! trim($textNode->nodeValue)) {
                    continue;
                }

                if ($newNodes = $this->replaceKeywords($this->keywords, $textNode->nodeValue, $dom)) {
                    $newFragment = $dom->createDocumentFragment();
                    foreach ($newNodes as $newNode) {
                        if (is_string($newNode)) {
                            $newFragment->appendChild($dom->createTextNode($newNode));
                        } else {
                            $newFragment->appendChild($newNode);
                        }
                    }

                    $textNode->parentNode->replaceChild($newFragment, $textNode);
                }
            }

            $wrapperNode = $dom->getElementsByTagName('div')[0];

            $result = trim((string) $dom->saveHTML($wrapperNode));

            return $result ? mb_substr($result, 5, -6) : $original;
        } catch (Exception $e) {
            $this->exception = $e;
        }

        return $original;
    }

    /**
     * @param  array<keyword, url>  $keywords
     * @param  string  $text
     * @param  DOMDocument  $dom
     * @return null|array<\DOMNode|string>
     */
    protected function replaceKeywords($keywords, $text, $dom): array|null
    {
        $searches = [];
        $regexNeedles = [];
        foreach ($keywords as $key => $link) {
            if (mb_stripos($text, $link['keyword']) === false) {
                continue;
            }

            $searches[$key] = $link;
            $regexNeedles[] = preg_quote($link['keyword'], '/');
        }

        if (! $searches) {
            return null;
        }

        $pattern = '/(' . implode('|', $regexNeedles) . ')/ui';
        $fragments = preg_split($pattern, $text, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if (! $fragments) {
            return null;
        }

        $newNodes = [];
        $needReplace = false;
        foreach ($fragments as $fragment) {
            $fragmentLower = strtolower(trim($fragment));

            if (! $fragmentLower || ! isset($searches[$fragmentLower])) {
                $newNodes[] = $fragment;

                continue;
            }

            $searched = $searches[$fragmentLower];
            $needReplace = true;

            $children = null;
            if ($this->nested) {
                $childSearches = $searches;
                unset($childSearches[$fragmentLower]);
                $children = $this->replaceKeywords($childSearches, $fragment, $dom);
            }

            if (! $children) {
                $children[] = $fragment;
            }

            $currentReplaced = false;
            foreach ($children as $child) {
                if (is_string($child)) {
                    $currentReplaced = true;
                    $a = $this->makeAElement($dom, [
                        'href' => $searched['url'],
                        'title' => array_key_exists('title', $searched) ? $searched['title'] : $fragment,
                    ]);
                    $a->nodeValue = $child;
                    $newNodes[] = $a;
                } else {
                    $newNodes[] = $child;
                }
            }

            if ($currentReplaced) {
                $this->keywords[$fragmentLower]['times']++;
            }

            if ($this->times && $this->keywords[$fragmentLower]['times'] >= $this->times) {
                unset($this->keywords[$fragmentLower], $searches[$fragmentLower]);
            }
        }

        if (! $needReplace) {
            return null;
        }

        return $newNodes;
    }

    protected function makeAElement(DOMDocument $dom, array $attributes = []): DOMElement
    {
        $a = $dom->createElement('a');

        foreach (array_merge($this->attributes, $attributes) as $key => $val) {
            $a->setAttribute($key, $val);
        }

        return $a;
    }

    public function hasError(): bool
    {
        return ! ! $this->exception;
    }

    public function getException(): Exception|null
    {
        return $this->exception;
    }
}
