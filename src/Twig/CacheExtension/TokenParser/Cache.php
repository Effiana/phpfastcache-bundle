<?php

/**
 *
 * This file is part of phpFastCache.
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the docs/CREDITS.txt file.
 *
 * @author Georges.L (Geolim4)  <contact@geolim4.com>
 * @author PastisD https://github.com/PastisD
 * @author Alexander (asm89) <iam.asm89@gmail.com>
 * @author Khoa Bui (khoaofgod)  <khoaofgod@gmail.com> http://www.phpfastcache.com
 *
 */
declare(strict_types=1);

namespace Phpfastcache\Bundle\Twig\CacheExtension\TokenParser;

use Phpfastcache\Bundle\Twig\CacheExtension\Node\CacheNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Parser for cache/endcache blocks.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class Cache extends AbstractTokenParser
{
    /**
     * @param Token $token
     *
     * @return boolean
     */
    public function decideCacheEnd(Token $token)
    {
        return $token->test('endcache');
    }

    /**
     * {@inheritDoc}
     */
    public function getTag()
    {
        return 'cache';
    }

    /**
     * {@inheritDoc}
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $annotation = $this->parser->getExpressionParser()->parseExpression();

        $key = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideCacheEnd'), true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new CacheNode($annotation, $key, $body, $lineno, $this->getTag());
    }
}
