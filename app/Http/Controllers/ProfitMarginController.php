<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductMargin;
use Illuminate\Http\Request;

class ProfitMarginController extends Controller
{
    public function show(Request $request)
    {
        $products = Product::all();

        // Use first product by default if none is selected
        $selectedProductId = $request->get('product_id', $products->first()->id ?? null);

        $margins = ProductMargin::where('product_id', $selectedProductId)->orderBy('min_quantity')->get();

        // Transform margins into required format for view
        $ranges = $margins->map(function ($margin) {
            return [
                'id'          => $margin->id,
                'min'         => $margin->min_quantity,
                'max'         => $margin->max_quantity,
                'company'     => $margin->company_margin,
                'distributor' => $margin->distributor_margin,
                'shop'        => $margin->shop_margin,
            ];
        })->toArray();

        return view('home', compact('products', 'ranges', 'selectedProductId'));
    }

    public function save(Request $request)
    {
        $productId = $request->input('product_id');

        foreach ($request->input('ranges') as $range) {
            if (! empty($range['id'])) {
                ProductMargin::where('id', $range['id'])->update([
                    'min_quantity'       => $range['min'],
                    'max_quantity'       => $range['max'],
                    'company_margin'     => $range['company'],
                    'distributor_margin' => $range['distributor'],
                    'shop_margin'        => $range['shop'],
                ]);
            } else {
                ProductMargin::create([
                    'product_id'         => $productId,
                    'min_quantity'       => $range['min'],
                    'max_quantity'       => $range['max'],
                    'company_margin'     => $range['company'],
                    'distributor_margin' => $range['distributor'],
                    'shop_margin'        => $range['shop'],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Margins saved successfully!');
    }

}
