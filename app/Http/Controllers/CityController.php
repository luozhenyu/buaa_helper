<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Support\Facades\Request;

class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $cities = City::doesntHave('parent')->get()
            ->map(function ($item, $key) {
                return [
                    'code' => $item->code,
                    'name' => $item->name,
                ];
            });
        return response()->json($cities);
    }

    public function show(Request $request, $code)
    {
        $city = City::where('code', $code)->firstOrFail();
        return response()->json([
            'code' => $city->code,
            'name' => $city->name,
        ]);
    }

    public function parent(Request $request, $code)
    {
        $city = City::where('code', $code)->firstOrFail()
            ->parent;
        return response()->json($city ? [
            'code' => $city->code,
            'name' => $city->name,
        ] : null);
    }

    public function children(Request $request, $code)
    {
        $cities = City::where('code', $code)->firstOrFail()
            ->children->map(function ($item, $key) {
                return [
                    'code' => $item->code,
                    'name' => $item->name,
                ];
            });
        return response()->json($cities);
    }
}
