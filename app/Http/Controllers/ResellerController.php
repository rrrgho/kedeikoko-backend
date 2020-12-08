<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Reseller;
use App\Models\Customer;
use Datatables;

class ResellerController extends Controller
{
    protected $clientURL = 'https://kedeikoko.rrrgho.com/';
    public function ResellerDatatable(){
        $data = Reseller::where('deleted_at',null)->get();
        return Datatables::of($data)
        ->addColumn('action', function($data){
            $link_detail = "'".$this->clientURL."reseller/reseller/".$data['uid']."'";
            $link_delete = "'".$this->clientURL."reseller/reseller-delete/".$data['uid']."'";
            $messageDelete = "'This action cannot be undo'";
            $detail = '<button class="btn btn-dark p-1 text-white" onclick="document.location.href='.$link_detail.'"> <i class="fa fa-eye"></i> </button>';
            $delete = '<button class="btn btn-danger p-1 text-white" onclick="alertConfirm('.$messageDelete.','.$link_delete.')"> <i class="fa fa-trash"></i> </button>';
            return $detail.'  '.$delete;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function ResellerData($uid){
        $data = Reseller::find($uid);
        if($data)
            return response()->json(['error' => false, 'message'=>'Data is retrived', 'data' => $data], 200);
        return response()->json(['error' => true, 'message'=>'Data not found', 'data' => $data], 404);
    }
    public function ResellerDataonly(){
        $data = Reseller::where('deleted_at',null)->get();
        if($data)
            return response()->json(['error' => false, 'message' => 'Success get data', 'data'=>$data]);
        return response()->json(['error' => true, 'message' => 'Data not found', 'data'=>$data]);
    }
    public function ResellerRegist(Request $request){
        $requestData = $request->all();
        if ($request->hasFile('idcard_image')) {
            if (!in_array($request->file('idcard_image')->getClientOriginalExtension(), array('jpg', 'jpeg', 'png'))) return response()->json(['error' => true, 'message' => 'FIle input is not supported, support only JPG, JPEG, PNG']);
            $request->file('idcard_image')->move('reseller/id-card/', $request->national_id . $request->file('idcard_image')->getClientOriginalName());
            $idcard_image_storaged_location = asset('reseller/id-card/', $request->national_id . $request->file('idcard_image')->getClientOriginalName());
            $requestData['idcard_image'] = $idcard_image_storaged_location;
        }
        $insert = Reseller::create($requestData);
        if($insert)
            return response()->json(['error' => false, 'message' => 'Success Insert Data']);
        return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
    }
    public function ResellerDelete($uid){
        $data = Reseller::find($uid);
        $data->deleted_at = Carbon::now('Asia/Jakarta');
        if($data->save())
            return response()->json(['error' => false, 'message' => 'Data is deleted']);
        return response()->json(['error' => true, 'message' => 'Data has failed to be deleted, something went wrong, contact Developer !']);
    }

    // Customer
    public function ResellerCustomerDatatable(Request $request){
        $data = Customer::where('reseller_uid',$request->reseller_uid)->where('deleted_at',null)->get();
        $reseller_uid = $request->reseller_uid;
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function($data) use($reseller_uid){
            $link_detail = "'".$this->clientURL."reseller/reseller-customer-detail/".$data['uid']."'";
            $link_delete = "'".$this->clientURL."reseller/reseller-customer-delete/".$data['uid']."/".$reseller_uid."'";
            $messageDelete = "'This action cannot be undo'";
            $detail = '<button class="btn btn-dark p-1 text-white" onclick="document.location.href='.$link_detail.'"> <i class="fa fa-eye"></i> </button>';
            $delete = '<button class="btn btn-danger p-1 text-white" onclick="alertConfirm('.$messageDelete.','.$link_delete.')"> <i class="fa fa-trash"></i> </button>';
            return $detail.'  '.$delete;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function ResellerCustomerData($uid){
        $data = Customer::find($uid);
        if($data)
            return response()->json(['error' => false, 'message'=>'Data is retrived', 'data' => $data], 200);
        return response()->json(['error' => true, 'message'=>'Data not found', 'data' => $data], 404);
    }
    public function ResellerCustomerDataonly(Request $request){
        $data = Customer::where('reseller_uid', $request->reseller_uid)->get();
        if($data)
            return response()->json(['error' => false, 'message'=>'Data is retrived', 'data' => $data], 200);
        return response()->json(['error' => true, 'message'=>'Data not found', 'data' => $data], 404);
    }
    public function ResellerCustomerRegist(Request $request){
        $insert = Customer::create($request->all());
        if($insert)
            return response()->json(['error' => false, 'message' => 'Success Insert Data']);
        return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
    }
    public function ResellerCustomerDelete($uid){
        $data = Customer::find($uid);
        $data->deleted_at = Carbon::now('Asia/Jakarta');
        if($data->save())
            return response()->json(['error' => false, 'message' => 'Data is deleted']);
        return response()->json(['error' => true, 'message' => 'Data has failed to be deleted, something went wrong, contact Developer !']);
    }
    public function ResellerCustomerDetail($uid){
        $data = Customer::find($uid);
        if($data)
            return response()->json(['error' => false, 'message'=>'Data is retrived', 'data' => $data], 200);
        return response()->json(['error' => true, 'message'=>'Data not found', 'data' => $data], 404);
    }
}
