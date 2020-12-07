<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterbankDebt;
use App\Models\BankAccount;
use App\Models\BankLog;
use App\Models\Order;
use App\Models\OrderPaymentLog;
use App\Models\ResellerIncome;
use App\Models\ProductStock;
use App\Models\BankTransactionBetween;
use Carbon\Carbon;
use Datatables;

class FinancialController extends Controller
{
    protected $clientURL = 'http://localhost/project/captainbras-fe/public/';

    public function BankLogBetween($transfer_type, $this_bank, $join_bank, $cash_total){
        if($transfer_type != 'top_up' && $transfer_type != 'cut'){
            BankTransactionBetween::create([
                'thisbank_id' => $this_bank,
                'joinbank_id' => $join_bank,
                'amount' => $cash_total,
                'type' => 'TRANSFER',
                'about' => $transfer_type,
                'status' => 'CREDIT',
            ]);
        }elseif($transfer_type == 'top_up'){
            BankTransactionBetween::create([
                'thisbank_id' => $this_bank,
                'joinbank_id' => $join_bank,
                'amount' => $cash_total,
                'type' => 'TOP UP',
                'about' => 'TOP UP',
                'status' => 'DEBIT',
            ]);
        }elseif($transfer_type == 'giving_loan'){
            BankTransactionBetween::create([
                'thisbank_id' => $this_bank,
                'joinbank_id' => $join_bank,
                'amount' => $cash_total,
                'type' => 'TRANSFER',
                'about' => $transfer_type,
                'status' => 'CREDIT',
            ]);
        }else{
            BankTransactionBetween::create([
                'thisbank_id' => $this_bank,
                'joinbank_id' => $join_bank,
                'amount' => $cash_total,
                'type' => 'LOAN CUT',
                'about' => 'LOAN CUT',
                'status' => 'CREDIT',
            ]);
        }
    }
    public function BankAccountDataonly(){
        $data = BankAccount::where('deleted_at',null)->get();
        if($data)
            return response()->json(['error' => false, 'message'=>'Data is retrived', 'data' => $data], 200);
        return response()->json(['error' => true, 'message'=>'Data not found', 'data' => $data], 404);
    }
    public function BankAccountData($thisbank_id){
        $data = BankAccount::find($thisbank_id);
        if($data)
            return response()->json(['error' => false, 'message'=>'Data is retrived', 'data' => $data], 200);
        return response()->json(['error' => true, 'message'=>'Data not found', 'data' => $data], 404);
    }
    public function BankAccountTopUp(Request $request){
        $data = BankAccount::find($request->id);
        if($request->type == 'topup'){
            $data->cash_total = $data['cash_total'] + $request->cash_total;
            if($data->save()){
                Self::BankLogBetween('top_up', $request->id, 0, $request->cash_total);
                return response()->json(['error' => false, 'message' => 'Success Insert Data']);
            }
            return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
        }elseif($request->type == 'cut'){
            $tmp = $data['cash_total'] - $request->cash_total;
            if($tmp>=0){
                $data->cash_total = $tmp;
                if($data->save()){
                    Self::BankLogBetween('cut', $request->id, 0, $request->cash_total);
                    return response()->json(['error' => false, 'message' => 'Success Insert Data']);
                }
                return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
            }else{
                return response()->json(['error' => true, 'message' => 'Cutting fund affected minus for balance, balance amount only Rp. '.number_format($data['cash_total'],2,',','.').', cannot be executed']);
            }
        }else{
            if($request->transfer_type == 'transfer_only'){
                $tmp = $data['cash_total'] - $request->cash_total;
                if($tmp>=0){
                    $data->cash_total = $tmp;
                    $destination_bank = BankAccount::find($request->joinbank_id);
                    $destination_bank->cash_total = $destination_bank['cash_total'] + $request->cash_total;
                    if($destination_bank->save()){
                        if($data->save()){
                            Self::BankLogBetween($request->transfer_type, $request->id, $request->joinbank_id, $request->cash_total);
                            return response()->json(['error' => false, 'message' => 'Success Insert Data']);
                        }
                    }
                    return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
                }else{
                    return response()->json(['error' => true, 'message' => 'Transfer fund affected minus for balance, balance amount only Rp. '.number_format($data['cash_total'],2,',','.').', cannot be executed']);
                }
            }
            if($request->transfer_type == 'giving_loan'){
                $tmp = $data['cash_total'] - $request->cash_total;
                if($tmp>=0){
                    $data->cash_total = $tmp;
                    $destination_bank = BankAccount::find($request->joinbank_id);
                    $destination_bank->cash_total = $destination_bank['cash_total'] + $request->cash_total;
                    if($destination_bank->save()){
                        if($data->save()){
                            Self::BankLogBetween($request->transfer_type, $request->id, $request->joinbank_id, $request->cash_total);
                            InterbankDebt::create([
                                'from' => $request->id,
                                'to' => $request->joinbank_id,
                                'amount' => $request->cash_total,
                            ]);
                            return response()->json(['error' => false, 'message' => 'Success Insert Data']);
                        }
                    }
                    return response()->json(['error' => true, 'message' => 'Failed to insert Data']);
                }else{
                    return response()->json(['error' => true, 'message' => 'Transfer fund affected minus for balance, balance amount only Rp. '.number_format($data['cash_total'],2,',','.').', cannot be executed']);
                }
            }
        }
    }
    public function BankAccountTransactionOutDatatable(Request $request){
        $data = BankTransactionBetween::where('thisbank_id',$request->thisbank_id)->where('status','<>','DEBIT')->orderBy('created_at','DESC')->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('amount', function($data){
            return number_format($data['amount'],0,',','.');
        })
        ->addColumn('created_at', function($data){
            return Carbon::parse($data['created_at'])->format('F d, Y H:m:s');
        })
        ->make(true);
    }
    public function BankAccountTransactionInDatatable(Request $request){
        $data = BankTransactionBetween::where('joinbank_id',$request->thisbank_id)->where('status','CREDIT')->orderBy('created_at','DESC')->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('amount', function($data){
            return number_format($data['amount'],0,',','.');
        })
        ->addColumn('created_at', function($data){
            return Carbon::parse($data['created_at'])->format('F d, Y H:m:s');
        })
        ->make(true);
    }
    public function BankAccountLog(Request $request){
        $bank = BankAccount::find(1);
        $tmp_cash_total = $bank['cash_total'];
        $bank->cash_total = $request->status == 'DEBIT' ? $tmp_cash_total + $request->total : $tmp_cash_total - $request->total;
        if($bank->cash_total >= 0){
            $insertLog = BankLog::create([
                'bankaccount_id' => $bank['id'],
                'total' => $request->total,
                'status' => $request->status,
                'description' => $request->description,
            ]);
            if($insertLog){
                $bank->save();
                return response()->json(['error' => false, 'message' => 'Success Insert Data']);
            }
        }
        return response()->json(['error' => true, 'message' => 'Not enough balance, balance amount only Rp. '.number_format($tmp_cash_total,0,',','.').' please top up Captain Bras balance, required top up amount Rp. '.number_format($request->total - $tmp_cash_total,0,',','.').', cannot be executed']);
    }
    public function BankAccountLogDatatable(Request $request){
        $data = BankLog::where('bankaccount_id',$request->bankaccount_id)->orderBy('created_at','DESC')->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('total', function($data){
            return number_format($data['total'],0,',','.');
        })
        ->addColumn('created_at', function($data){
            return Carbon::parse($data['created_at'])->format('F d, Y H:m:s');
        })
        ->make(true);
    }


    // Order
    public function NewOrder(Request $request){
        // Check Product Stock
        $productStock = ProductStock::where('productmerk_id', $request->productmerk_id)->where('deleted_at',null)->first();
        $tmp_product_stock = $productStock['stock_amount'];
        if($tmp_product_stock >= $request->amount){
            // Check customer progress
            $debt = Order::where('customer_uid',$request->customer_uid)->where('is_paid',false)->first();
            if(!$debt){
                $n = count(Order::all());
                $queue = "";
                for ($i=1; $i<= 6 - strlen(strval($n+1)); $i++){
                    $queue .= '0';
                }
                $invoice_number = 'INV/'.Carbon::now('Asia/Jakarta')->format('M/Y').'/'.$queue.strval($n == 0 ? 1  : $n+1);
                $insert = Order::create([
                    'invoice_number' => $invoice_number,
                    'reseller_uid' => $request->reseller_uid,
                    'customer_uid' => $request->customer_uid,
                    'productmerk_id' => $request->productmerk_id,
                    'amount' => $request->amount,
                    'reseller_profit' => $request->reseller_profit,
                    'payment_total' => $request->payment_total,
                    'due_date' => $request->due_date,
                ]);
                if($insert){
                    $productStock = ProductStock::where('productmerk_id', $request->productmerk_id)->where('deleted_at',null)->first();
                    $productStock->stock_amount = $tmp_product_stock - $request->amount;
                    $productStock->save();
                    return response()->json(['error' => false, 'message' => 'Success Insert Data']);
                }
                return response()->json(['error' => true, 'message' => 'Failed insert Data, contact Developer']);
            }else{
                return response()->json(['error' => true, 'message' => 'Failed insert Data, this customer has unpaid purchase']);
            }
        }
        return response()->json(['error' => true, 'message' => 'Product stock is not enough, only '.$tmp_product_stock.' left.']);
    }
    public function UnpaidOrdersDatatable(){
        $data = Order::where('deleted_at',null)->where('is_paid',false)->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function($data){
            $link_detail = "'".$this->clientURL."order/order-detail/".$data['id']."'";
            $detail = '<button class="btn btn-dark p-1 text-white" onclick="document.location.href='.$link_detail.'"> <i class="fa fa-eye"></i> </button>';
            return $detail;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function OrdersCustomerDatatable($uid,$status){
        if($status == 'ongoing')
            $data = Order::where('deleted_at',null)->where('customer_uid',$uid)->where('is_paid',false)->get();
        else
            $data = Order::where('deleted_at',null)->where('customer_uid',$uid)->where('is_paid',true)->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function($data){
            $link_detail = "'".$this->clientURL."order/order-detail/".$data['id']."'";
            $detail = '<button class="btn btn-dark p-1 text-white" onclick="document.location.href='.$link_detail.'"> <i class="fa fa-eye"></i> </button>';
            return $detail;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function OrderDetail($id){
        $data = Order::find($id);
        if($data)
            return response()->json(['error' => false, 'message'=>'Data is retrived', 'data' => $data], 200);
        return response()->json(['error' => true, 'message'=>'Data not found', 'data' => $data], 404);
    }
    public function PayOrder(Request $request){
        $order = Order::find($request->order_id);
        $tmp_need_to_be_paid = $order['need_to_be_paid'];

        // Check if Pay amount is bigger than amount left
        if($order['need_to_be_paid']>=$request->amount){
            // Create Payment History
            $insertLog = OrderPaymentLog::create([
                'order_id' => $request->order_id,
                'amount' => $request->amount,
            ]);
            // Create Debit for Company
            if($insertLog){
                $bank = BankAccount::find(1);
                $tmp_cash_total = $bank['cash_total'];
                $bankLog = BankLog::create([
                    'bankaccount_id' => $bank['id'],
                    'total' => $request->amount,
                    'status' => 'DEBIT',
                    'description' => $order['invoice_number'],
                ]);
                if($bankLog){
                    $bank->cash_total = $tmp_cash_total+$request->amount;
                    $bank->save();
                    if($tmp_need_to_be_paid - $request->amount == 0){
                        $order->is_paid = true;
                        $order->save();
                        ResellerIncome::create([
                            'reseller_uid' => $order['reseller_uid'],
                            'amount' => $order['reseller_profit'],
                        ]);
                    }
                    return response()->json(['error' => false, 'message' => 'Success Insert Data']);
                }
            }
        }
        return response()->json(['error' => true, 'message' => 'Paying amount is larger than what being requested, requested only Rp. ' . number_format($order['need_to_be_paid'],0,',','.')]);
    }
    public function OrderPaymentLogDatatable($order_id){
        $data = OrderPaymentLog::where('order_id',$order_id)->orderBy('created_at','DESC')->get();
        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('amount', function($data){
            return number_format($data['amount'],0,',','.');
        })
        ->addColumn('created_at', function($data){
            return Carbon::parse($data['created_at'])->format('d-M-Y H:m:s');
        })
        ->make(true);
    }
}
