<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductType;
use App\Models\ProductMerk;
use App\Models\ProductStock;
use Carbon\Carbon;
use Datatables;

class ProductController extends Controller
{
    protected $clientURL = 'https://kedeikoko.rrrgho.com/';
    public function ProductTypeRegist(Request $request){
        $insert = ProductType::create($request->all());
        if($insert)
            return response()->json(['error' => false, 'message' => 'Success Insert Data']);
        return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
    }
    public function ProductTypeDatatable(){
        $data = ProductType::where('deleted_at', null)->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function($data){
            $link_delete = "'".$this->clientURL."products/product-type-delete/".$data['id']."'";
            $messageDelete = "'This action cannot be undo'";
            $delete = '<button class="btn btn-danger p-1 text-white" onclick="alertConfirm('.$messageDelete.','.$link_delete.')"> <i class="fa fa-trash"></i> </button>';
            return $delete;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function ProductTypeDataonly(){
        $data = ProductType::where('deleted_at',null)->get();
        if($data)
            return response()->json(['error' => false, 'message' => 'Success get data', 'data'=>$data]);
        return response()->json(['error' => true, 'message' => 'Data not found', 'data'=>$data]);
    }
    public function ProductTypeDelete($id){
        $data = ProductType::find($id);
        $data->deleted_at = Carbon::now('Asia/Jakarta');
        if($data->save())
            return response()->json(['error' => false, 'message' => 'Data is deleted']);
        return response()->json(['error' => true, 'message' => 'Data has failed to be deleted, something went wrong, contact Developer !']);
    }


    // Product Merk
    public function ProductMerkRegist(Request $request){
        $insert = ProductMerk::create($request->all());
        if($insert)
            return response()->json(['error' => false, 'message' => 'Success Insert Data']);
        return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
    }
    public function ProductMerkDatatable(){
        $data = ProductMerk::where('deleted_at', null)->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function($data){
            $link_delete = "'".$this->clientURL."products/product-merk-delete/".$data['id']."'";
            $messageDelete = "'This action cannot be undo'";
            $delete = '<button class="btn btn-danger p-1 text-white" onclick="alertConfirm('.$messageDelete.','.$link_delete.')"> <i class="fa fa-trash"></i> </button>';
            return $delete;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function ProductMerkDelete($id){
        $data = ProductMerk::find($id);
        $data->deleted_at = Carbon::now('Asia/Jakarta');
        if($data->save())
            return response()->json(['error' => false, 'message' => 'Data is deleted']);
        return response()->json(['error' => true, 'message' => 'Data has failed to be deleted, something went wrong, contact Developer !']);
    }
    public function ProductMerkDataonly(){
        $data = ProductMerk::where('deleted_at',null)->get();
        if($data)
            return response()->json(['error' => false, 'message' => 'Success get data', 'data'=>$data]);
        return response()->json(['error' => true, 'message' => 'Data not found', 'data'=>$data]);
    }

    // Product Stock
    public function ProductStockRegist(Request $request){
        $data = ProductStock::where('productmerk_id',$request->productmerk_id)->where('deleted_at',null)->first();
        if(!$data){
            $insert = ProductStock::create($request->all());
            if($insert)
                return response()->json(['error' => false, 'message' => 'Success Insert Data']);
            return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
        }
        $tmp_stock = $data['stock_amount'];
        $data->stock_amount = $tmp_stock + $request->stock_amount;
        $data->profit_in_each = $request->profit_in_each;
        $data->buyingprice_in_each = $request->buyingprice_in_each;
        if($data->save())
            return response()->json(['error' => false, 'message' => 'Success Insert Data']);
        return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
    }
    public function ProductStockDatatable(){
        $data = ProductStock::where('deleted_at', null)->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function($data){
            $link_delete = "'".$this->clientURL."products/product-stock-delete/".$data['id']."'";
            $messageDelete = "'This action cannot be undo'";
            $delete = '<button class="btn btn-danger p-1 text-white" onclick="alertConfirm('.$messageDelete.','.$link_delete.')"> <i class="fa fa-trash"></i> </button>';
            return $delete;
        })
        ->addColumn('profit_in_each', function($data){
            return "Rp. ".number_format($data['profit_in_each'],2,',','.');
        })
        ->addColumn('buyingprice_in_each', function($data){
            return "Rp. ".number_format($data['buyingprice_in_each'],2,',','.');
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function ProductStockDelete($id){
        $data = ProductStock::find($id);
        $data->deleted_at = Carbon::now('Asia/Jakarta');
        if($data->save())
            return response()->json(['error' => false, 'message' => 'Data is deleted']);
        return response()->json(['error' => true, 'message' => 'Data has failed to be deleted, something went wrong, contact Developer !']);
    }
    public function ProductStockDataonly($id){
        $data = ProductStock::where('productmerk_id',$id)->where('deleted_at',null)->first();
        if($data)
            return response()->json(['error' => false, 'message' => 'Success get data', 'data'=>$data]);
        return response()->json(['error' => true, 'message' => 'Data not found', 'data'=>$data]);
    }
}
