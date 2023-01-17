<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Mlibre
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GoodMarket\Helper;

/**
 * Logger Helper for logs
 */
class Logger extends \Ced\Integrator\Helper\Logger
{
    public $mutelevel = 100;

    /**
     * DB logger, dependencies can be updated here, such as model.
     * Logger constructor.
     *
     * @param \Ced\Integrator\Model\LogFactory $log
     * @param $name
     */
    public function __construct(
        \Ced\Integrator\Model\LogFactory $log,
        $name = 'GOODMARKET'
    ) {
        parent::__construct($log, $name);
    }
}
