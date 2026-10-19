<?php

namespace Azuriom\Plugin\UserNotes\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegacyRedirectController extends Controller
{
    public function redirect(Request $request, ?string $path = null): RedirectResponse
    {
        $path = preg_replace('#^players(?=/|$)#', 'users', $path ?? '');
        $url = url('/admin/user-notes'.($path === '' ? '' : '/'.$path));
        $query = $request->getQueryString();

        return redirect()->to($url.($query ? '?'.$query : ''), 301);
    }
}
