<?php

it('can replace', function () {
    expect(createKeywordLinkify()
        ->replace(
            'php',
            [
                [
                    'keyword' => 'php',
                    'url' => 'https://www.php.net/',
                ],
            ]
        ))
        ->toEqual('<a target="blank" href="https://www.php.net/" title="php">php</a>');
});

it('can replace all and wont replace html attr value', function () {
    expect(createKeywordLinkify()
        ->replace(
            '<img class="hero-logo" src="/images/logos/php-logo-white.svg" alt="php" width="240" height="120"><p class="hero-text">PHP is a <strong>popular general-purpose scripting language</strong> that is especially suited to web development.<br>Fast, flexible and pragmatic, PHP powers everything from your blog to the most popular websites in the world.</p>',
            [
                [
                    'keyword' => 'php',
                    'url' => 'https://www.php.net/',
                ],
            ]
        ))
        ->toEqual(
            '<img class="hero-logo" src="/images/logos/php-logo-white.svg" alt="php" width="240" height="120"><p class="hero-text"><a target="blank" href="https://www.php.net/" title="PHP">PHP</a> is a <strong>popular general-purpose scripting language</strong> that is especially suited to web development.<br>Fast, flexible and pragmatic, <a target="blank" href="https://www.php.net/" title="PHP">PHP</a> powers everything from your blog to the most popular websites in the world.</p>'
        );
});

it('can replace multiple keywords', function () {
    expect(createKeywordLinkify()
        ->replace(
            '<p class="hero-text">PHP is a <strong>popular general-purpose scripting language</strong> that is especially suited to web development.<br>Fast, flexible and pragmatic, PHP powers everything from your blog to the most popular websites in the world.</p>',
            [
                [
                    'keyword' => 'php',
                    'url' => 'https://www.php.net/',
                ],
                [
                    'keyword' => 'web',
                    'url' => 'https://en.wikipedia.org/wiki/Web',
                ],
            ]
        ))
        ->toEqual(
            '<p class="hero-text"><a target="blank" href="https://www.php.net/" title="PHP">PHP</a> is a <strong>popular general-purpose scripting language</strong> that is especially suited to <a target="blank" href="https://en.wikipedia.org/wiki/Web" title="web">web</a> development.<br>Fast, flexible and pragmatic, <a target="blank" href="https://www.php.net/" title="PHP">PHP</a> powers everything from your blog to the most popular <a target="blank" href="https://en.wikipedia.org/wiki/Web" title="web">web</a>sites in the world.</p>'
        );
});

it('can replace only onece', function () {
    expect(createKeywordLinkify()
        ->times(1)
        ->replace(
            '<p class="hero-text">PHP is a <strong>popular general-purpose scripting language</strong> that is especially suited to web development.<br>Fast, flexible and pragmatic, PHP powers everything from your blog to the most popular websites in the world.</p>',
            [
                [
                    'keyword' => 'php',
                    'url' => 'https://www.php.net/',
                ],
            ]
        ))
        ->toEqual(
            '<p class="hero-text"><a target="blank" href="https://www.php.net/" title="PHP">PHP</a> is a <strong>popular general-purpose scripting language</strong> that is especially suited to web development.<br>Fast, flexible and pragmatic, PHP powers everything from your blog to the most popular websites in the world.</p>'
        );
});

it('can replace only twice', function () {
    expect(createKeywordLinkify()
        ->times(2)
        ->replace(
            'PHP is the best. And I love PHP. PHP is dead that is a long jok.',
            [
                [
                    'keyword' => 'php',
                    'url' => 'https://www.php.net/',
                ],
            ]
        ))
        ->toEqual(
            '<a target="blank" href="https://www.php.net/" title="PHP">PHP</a> is the best. And I love <a target="blank" href="https://www.php.net/" title="PHP">PHP</a>. PHP is dead that is a long jok.'
        );
});

it('can repalce Chinese', function () {
    expect(createKeywordLinkify()
        ->replace(
            '李白诗中常将想象、夸张、比喻、拟人等手法综合运用，从而造成神奇异彩、瑰丽动人的意境，这就是李白的浪漫主义诗作给人以豪迈奔放、飘逸若仙的原因所在。乾元二年，李白应友人之邀，再次与被谪贬的贾至泛舟赏月于洞庭之上，发思古之幽情，赋诗抒怀。',
            [
                [
                    'keyword' => '李白',
                    'url' => 'https://baike.baidu.com/item/%E6%9D%8E%E7%99%BD/1043',
                ],
            ]
        ))
        ->toEqual(
            '<a target="blank" href="https://baike.baidu.com/item/%E6%9D%8E%E7%99%BD/1043" title="李白">李白</a>诗中常将想象、夸张、比喻、拟人等手法综合运用，从而造成神奇异彩、瑰丽动人的意境，这就是<a target="blank" href="https://baike.baidu.com/item/%E6%9D%8E%E7%99%BD/1043" title="李白">李白</a>的浪漫主义诗作给人以豪迈奔放、飘逸若仙的原因所在。乾元二年，<a target="blank" href="https://baike.baidu.com/item/%E6%9D%8E%E7%99%BD/1043" title="李白">李白</a>应友人之邀，再次与被谪贬的贾至泛舟赏月于洞庭之上，发思古之幽情，赋诗抒怀。'
        );
});

it('can repalce nested', function () {
    expect(createKeywordLinkify()
        ->replace(
            'KeywordLinkify is a PHP package.',
            [
                [
                    'keyword' => 'PHP',
                    'url' => 'https://www.php.net/',
                ],
                [
                    'keyword' => 'PHP package',
                    'url' => 'https://packagist.org/packages/chuoke/keyword-linkify',
                ],
            ]
        ))
        ->toEqual(
            'KeywordLinkify is a <a target="blank" href="https://packagist.org/packages/chuoke/keyword-linkify" title="PHP package"><a target="blank" href="https://www.php.net/" title="PHP">PHP</a> package</a>.'
        );
});

it('can change target', function () {
    expect(createKeywordLinkify()
        ->target('top')
        ->times(1)
        ->replace(
            '李白诗中常将想象、夸张、比喻、拟人等手法综合运用，从而造成神奇异彩、瑰丽动人的意境，这就是李白的浪漫主义诗作给人以豪迈奔放、飘逸若仙的原因所在。乾元二年，李白应友人之邀，再次与被谪贬的贾至泛舟赏月于洞庭之上，发思古之幽情，赋诗抒怀。',
            [
                [
                    'keyword' => '李白',
                    'url' => 'https://baike.baidu.com/item/%E6%9D%8E%E7%99%BD/1043',
                ],
            ]
        ))
        ->toEqual(
            '<a target="top" href="https://baike.baidu.com/item/%E6%9D%8E%E7%99%BD/1043" title="李白">李白</a>诗中常将想象、夸张、比喻、拟人等手法综合运用，从而造成神奇异彩、瑰丽动人的意境，这就是李白的浪漫主义诗作给人以豪迈奔放、飘逸若仙的原因所在。乾元二年，李白应友人之邀，再次与被谪贬的贾至泛舟赏月于洞庭之上，发思古之幽情，赋诗抒怀。'
        );
});

it('can change title', function () {
    expect(createKeywordLinkify()
        ->target('top')
        ->times(1)
        ->replace(
            '李白诗中常将想象、夸张、比喻、拟人等手法综合运用，从而造成神奇异彩、瑰丽动人的意境，这就是李白的浪漫主义诗作给人以豪迈奔放、飘逸若仙的原因所在。乾元二年，李白应友人之邀，再次与被谪贬的贾至泛舟赏月于洞庭之上，发思古之幽情，赋诗抒怀。',
            [
                [
                    'keyword' => '李白',
                    'url' => 'https://baike.baidu.com/item/%E6%9D%8E%E7%99%BD/1043',
                    'title' => '点击查看李白详情',
                ],
            ]
        ))
        ->toEqual(
            '<a target="top" href="https://baike.baidu.com/item/%E6%9D%8E%E7%99%BD/1043" title="点击查看李白详情">李白</a>诗中常将想象、夸张、比喻、拟人等手法综合运用，从而造成神奇异彩、瑰丽动人的意境，这就是李白的浪漫主义诗作给人以豪迈奔放、飘逸若仙的原因所在。乾元二年，李白应友人之邀，再次与被谪贬的贾至泛舟赏月于洞庭之上，发思古之幽情，赋诗抒怀。'
        );
});
