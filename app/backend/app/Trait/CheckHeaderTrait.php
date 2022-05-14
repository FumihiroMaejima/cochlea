<?php

namespace App\Trait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

trait CheckHeaderTrait
{
    /**
     * checkHeader
     * @param Illuminate\Http\Request $request
     * @param array $targets
     * @return boolean
     */
    public function checkRequestAuthority(Request $request, array $targets)
    {
        return in_array($request->header(Config::get('myapp.headers.authority')), $targets, true);
    }
}
