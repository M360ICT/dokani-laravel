<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\ProductCategory\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        
        $productList = Product::where('products.product_status',1)->join('product_categories','products.product_category','product_categories.product_category_id')->select('products.*','product_categories.product_category_name')
        ->orderBy('products.product_id','desc')->get();
        
        return view('pages.products.list_products',compact('productList'));

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $date = date('Y-m-d');
        $productCategory = ProductCategory::where('product_category_status',1)->get();
        $row_count = Product::where('product_created_at',$date)->count();
        return view('pages.products.create_products',compact('productCategory','row_count'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|unique:products,product_name',
            'product_code' => 'required|unique:products,product_code'
        ]);


        $product = new Product();
        $product->product_name = $request->product_name;
        $product->product_entry_id = $request->product_entry_id;
        $product->product_category = $request->product_category;
        $product->product_code = $request->product_code;
        $product->product_retail_price = $request->product_retail_price;
        $product->product_wholesale_price = $request->product_wholesale_price;
        $product->product_created_by =  Auth::user()->id;
        $product->product_created_at = date('Y-m-d');
        $product->created_at = date("Y/m/d");
        $product->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['data'] = Product::where('product_id',$id)->first();
        $productCategory = ProductCategory::where('product_category_status',1)->get();
        return view('pages.products.edit_products',$data,compact('productCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        Product::where('product_id',$request->product_id)->update([
            'product_name' => $request->product_name,
            'product_entry_id' => $request->product_entry_id,
            'product_category' => $request->product_category,
            'product_code' => $request->product_code,
            'product_retail_price' => $request->product_retail_price,
            'product_wholesale_price' => $request->product_wholesale_price,
            'product_updated_by' => Auth::user()->id,
            'updated_at' => date("Y/m/d"),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::where('product_id',$id)->update([
            'product_deleted_by' => Auth::user()->id,
            'product_status' => 0,
            'product_is_deleted' => "YES",
        ]);
    }

    public function productSearch(Request $request)
    {
        $products = Product::where('product_name','like',"%{$request->q}%")->orWhere('product_code','like',"%{$request->q}%")->orWhere('product_entry_id','like',"%{$request->q}%")->get();
        // print_r($clients);
        // die;
        $product_array = array();
        foreach ($products as $product) {
            $label = $product['product_name'] . '(' . $product['product_entry_id'] . ')';
            $value = intval($product['product_name']);
            $code = intval($product['product_id']);
            $product_array[] = array("label" => $label, "value" => $value, "code" => $code);
        }
        $result = array('status' => 'ok', 'content' => $product_array);
        echo json_encode($result);
        exit;
    }
    
    public function get_product_barcode($id) {
	$product = Product::whereProductId($id)->get()[0];
	return view('pages.barcode.product_barcode')->with('product',$product);
    }
    
    
    public function barcode_scan() {
	return view('pages.barcode.scan');
    }
    
    
    function check_barcode(Request $request)
    {
     $validation = Validator::make($request->all(), [
      'BarcodeQrImage' => 'required'
     ]);
     if($validation->passes())
     {
      $image = $request->file('BarcodeQrImage');
      $image->move(public_path('images'), $image->getClientOriginalName());

      $resultArray = DecodeBarcodeFile(public_path('images/' . $image->getClientOriginalName()), 0x3FF | 0x2000000 | 0x4000000 | 0x8000000 | 0x10000000); // 1D, PDF417, QRCODE, DataMatrix, Aztec Code

      if (is_array($resultArray)) {
        $resultCount = count($resultArray);
        echo "Total count: $resultCount", "\n";
        if ($resultCount > 0) {
            for ($i = 0; $i < $resultCount; $i++) {
                $result = $resultArray[$i];
                echo "Barcode format: $result[0], ";
                echo "value: $result[1], ";
                echo "raw: ", bin2hex($result[2]), "\n";
                echo "Localization : ", $result[3], "\n";
            }
        }
        else {
            echo 'No barcode found.', "\n";
        }
      } 
}
}
}