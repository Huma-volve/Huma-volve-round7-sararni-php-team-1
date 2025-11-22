<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    //
    public function index()
    {
        try {
            $brands = Brand::select('id', 'name')->with('cars')->get();    
            
            return ApiResponse::successResponse($brands, 'All Brands retrieved successfully');

        } catch (\Throwable $th) {
            return ApiResponse::errorResponse($th->getMessage(), 500);
        }
    }


}
