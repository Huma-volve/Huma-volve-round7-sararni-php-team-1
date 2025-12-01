<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBrandRequest;
use App\Models\Brand;
use App\Services\CarBrandService;
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

    public function store(StoreBrandRequest $request, CarBrandService $brandService )
    {
        try {
            $data = $request->validated();    

            $brand = $brandService->createBrand($data);

            return ApiResponse::successResponse($brand, ' Brand created successfully');

        } catch (\Throwable $th) {
            return ApiResponse::errorResponse($th->getMessage(), 500);
        }
    }


}
