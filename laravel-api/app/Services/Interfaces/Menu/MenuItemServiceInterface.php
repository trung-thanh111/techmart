<?php

namespace App\Services\Interfaces\Menu;

use Illuminate\Http\Request;

interface MenuItemServiceInterface
{
    public function rebuildTree(Request $request);
    public function dragDrop(Request $request);
}
