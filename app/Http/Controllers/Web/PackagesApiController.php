<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackagesApiController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = Package::query()
        ->with(['products'])
        ->withCount(['products']);



                $query = $query->when(
                    $request->has('categories') && is_array($request->categories), 
                    fn($q) => $q->whereHas('products', function($query) use ($request) {
                        $query->whereIn('category_id', $request->categories);
                    })
                );

                $query = $query->when(
                    $request->has('routines') && is_array($request->routines), 
                    fn($q) => $q->whereHas('products.routines', function($query) use ($request) {
                        $query->whereIn('routine_id', $request->routines);
                    })
                );

                $query = $query->when(
                    $request->has('brands') && is_array($request->brands),
                    fn($q) => $q->whereHas('products', function ($query) use ($request) {
                        $query->whereIn('brand', $request->brands);
                    })
                );

                $query = $query->when($request->filled('search'), function ($q) use ($request) {
                    $search = trim($request->search);
                    $like = "%{$search}%";

                    $q->where(function ($subQ) use ($like) {
                        $subQ->whereHas('translations', function ($transQ) use ($like) {
                            $transQ->where('name', 'like', $like)
                                ->orWhere('description', 'like', $like);
                        })
                        ->orWhereHas('products', function ($productQ) use ($like) {
                            $productQ->where('brand', 'like', $like)
                                ->orWhereHas('translations', function ($transQ) use ($like) {
                                    $transQ->where('name', 'like', $like)
                                        ->orWhere('description', 'like', $like);
                                });
                        });
                    });
                });

        $perPage = $request->get('per_page', 9);
        $packages = $query->paginate($perPage);

        
        $transformedPackages = $packages
        ->values()
        ->map(function ($package , $index) {
             $packageData['html'] = view('components.package', [
                    'package' => $package,
                    'index' => $index
                ])->render();
            
                return $packageData;

        });

        return response()->json([
            'success' => true,
            'data' => $transformedPackages,
            'total' => $packages->total(),
            'per_page' => $packages->perPage(),
            'current_page' => $packages->currentPage(),
            'last_page' => $packages->lastPage(),
        ]);
    }
}
