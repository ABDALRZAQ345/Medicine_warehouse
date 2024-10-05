<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\AdminServices;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    protected $adminServices;

    public function __construct(AdminServices $adminServices)
    {
        $this->adminServices = $adminServices;
    }

    //
    public function panel()
    {

        $data = Cache::remember('admin_panel', 600, function () {
            return $this->adminServices->get_panel_data();
        });

        return response()->json([
            'message' => 'This data will be updated every 10 minutes.',
            'data' => $data,
        ]);

    }
}
