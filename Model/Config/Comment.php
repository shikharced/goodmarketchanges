<?php

namespace Ced\GoodMarket\Model\Config;

use Magento\Framework\UrlInterface;

/**
 * class Comment config
 */
class Comment implements \Magento\Config\Model\Config\CommentInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * Comment Function __construct
     *
     * @param UrlInterface $urlInterface
     * @return $this
     */
    public function __construct(
        UrlInterface $urlInterface
    ) {
        $this->urlInterface = $urlInterface;
    }

    /**
     * Function getCommentText
     *
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        return 'If you are not Good Market approved, click <a 
        href="https://www.goodmarket.global/apply"
        target="_blank">here</a> to apply.';
    }
}
