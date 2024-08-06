<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Material;
use App\Models\Particular;
use App\Models\ProductType;

class ResourceController extends Controller
{
    public function getDepartments()
    {
        $departments = Department::all();
        return response()->json($departments);
    }

    public function getProductTypes()
    {
        $productTypes = ProductType::all();
        return response()->json($productTypes);
    }

    public function getMaterials()
    {
        $materials = Material::all();
        return response()->json($materials);
    }

    public function getParticulars()
    {
        $particulars = Particular::all();
        return response()->json($particulars);
    }

    public function getParticularMaterials($particularId)
    {
        $materials = Material::where('id', $particularId)->get();
        return response()->json($materials);
    }
}