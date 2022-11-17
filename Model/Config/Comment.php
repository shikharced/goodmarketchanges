<?php

namespace Ced\GoodMarket\Model\Config;

use Magento\Framework\UrlInterface;

class Comment implements \Magento\Config\Model\Config\CommentInterface
{
    protected $urlInterface;

    public function __construct(
        UrlInterface $urlInterface
    ) {
        $this->urlInterface = $urlInterface;
    }

    public function getCommentText($elementValue)
    {
        return 'If you are not Good Market approved, click <a href="https://demo8-marketplace.cedcommerce.com/gm/pub/csmarketplace/account/register/?ced_integration"target="_blank">here</a> to apply.';
    }
}