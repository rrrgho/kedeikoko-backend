<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FinancialController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function(){
    // Reseller
    Route::get('reseller-dataonly', [ResellerController::class , 'ResellerDataonly']);
    Route::get('reseller-datatable', [ResellerController::class, 'ResellerDatatable']);
    Route::get('reseller-data/{uid}', [ResellerController::class, 'ResellerData']);
    Route::get('reseller-delete/{uid}', [ResellerController::class, 'ResellerDelete']);
    Route::post('reseller-regist', [ResellerController::class, 'ResellerRegist']);

    // Reseller Customer
    Route::get('reseller-customer-delete/{uid}', [ResellerController::class, 'ResellerCustomerDelete']);
    Route::get('reseller-customer-detail/{uid}', [ResellerController::class, 'ResellerCustomerDetail']);
    Route::post('reseller-customer-regist', [ResellerController::class, 'ResellerCustomerRegist']);
    Route::post('reseller-customer-datatable', [ResellerController::class, 'ResellerCustomerDatatable']);
    Route::post('reseller-customer-dataonly', [ResellerController::class, 'ResellerCustomerDataonly']);

    // Product
    Route::get('product-type-dataonly', [ProductController::class, 'ProductTypeDataonly']);
    Route::get('product-type-datatable', [ProductController::class, 'ProductTypeDatatable']);
    Route::get('product-type-delete/{id}', [ProductController::class, 'ProductTypeDelete']);
    Route::post('product-type-regist', [ProductController::class, 'ProductTypeRegist']);

    Route::get('product-merk-dataonly', [ProductController::class, 'ProductMerkDataonly']);
    Route::get('product-merk-datatable', [ProductController::class, 'ProductMerkDatatable']);
    Route::get('product-merk-delete/{id}', [ProductController::class, 'ProductMerkDelete']);
    Route::post('product-merk-regist', [ProductController::class, 'ProductMerkRegist']);

    Route::get('product-stock-dataonly/{id}', [ProductController::class, 'ProductStockDataonly']);
    Route::get('product-stock-datatable', [ProductController::class, 'ProductStockDatatable']);
    Route::get('product-stock-delete/{id}', [ProductController::class, 'ProductStockDelete']);
    Route::post('product-stock-regist', [ProductController::class, 'ProductStockRegist']);

    // Bank Account
    Route::get('bankaccount-dataonly', [FinancialController::class, 'BankAccountDataonly']);
    Route::get('bankaccount-data/{thisbank_id}', [FinancialController::class, 'BankAccountData']);
    Route::post('bankaccount-transaction-out-datatable', [FinancialController::class, 'BankAccountTransactionOutDatatable']);
    Route::post('bankaccount-transaction-in-datatable', [FinancialController::class, 'BankAccountTransactionInDatatable']);
    Route::post('bankaccount-topup', [FinancialController::class, 'BankAccountTopUp']);
    Route::post('bankaccount-log', [FinancialController::class, 'BankAccountLog']);
    Route::post('bankaccount-buy-sell-log-datatable', [FinancialController::class, 'BankAccountLogDatatable']);

    // Order
    Route::get('unpaid-orders-datatable', [FinancialController::class, 'UnpaidOrdersDatatable']);
    Route::get('unpaid-orders-customer-datatable/{uid}/{status}', [FinancialController::class, 'OrdersCustomerDatatable']);
    Route::get('order-detail/{id}', [FinancialController::class, 'OrderDetail']);
    Route::get('order-payment-log-datatable/{order_id}', [FinancialController::class, 'OrderPaymentLogDatatable']);
    Route::post('new-order', [FinancialController::class, 'NewOrder']);
    Route::post('pay-order', [FinancialController::class, 'PayOrder']);

});


