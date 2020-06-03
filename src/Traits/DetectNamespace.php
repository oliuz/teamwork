<?php

namespace Mpociot\Teamwork\Traits;

/**
 * This file is part of Teamwork
 *
 * PHP version 7.2
 *
 * @category PHP
 * @package  Teamwork
 * @author   Marcel Pociot <m.pociot@gmail.com>
 * @license  MIT
 * @link     http://github.com/mpociot/teamwork
 */

use Illuminate\Container\Container;

trait DetectNamespace
{
    public function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }
}
