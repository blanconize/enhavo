<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CacheKernel extends HttpCache
{
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if ($request->isMethod('PURGE')) {
            $this->invalidate($request, $catch);
            return new Response();
        }
        return parent::handle($request, $type, $catch);
    }
}
