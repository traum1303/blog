<?php declare(strict_types=1);

namespace App\Controllers;

use Smarty;

abstract class AbstractController
{
    protected Smarty $smarty;
    public function __construct(Smarty $smarty)
    {
        $this->smarty = $smarty;
    }
}