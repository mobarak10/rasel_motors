<?php

namespace App\Http\Controllers\User;

use App\Models\Sale;
use App\Models\Party;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RetailSaleRequest;
use App\Models\BankAccount;
use App\Models\Brand;
use App\Models\Cash;
use App\Models\Customer;
use App\Models\CustomerDetails;
use App\Models\Product;
use App\Models\Warehouse;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Response;

class RetailSaleController extends Controller
{
    private $meta = [
        'title'   => 'POS',
        'menu'    => 'pos',
        'submenu' => '',
        'header'  => false
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    private $sale;
    private $errors;

    /**
     * @param RetailSaleRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function sale(RetailSaleRequest $request): JsonResponse
    {
//        return response()->json($request->all());
        DB::transaction(function () use ($request) {
            $customer = Customer::findOrFail($request->customer_id);
            $sale_data = [
                'date'                  => $request->date,
                'invoice_no'            => 'INV' . str_pad(Sale::max('id') + 1, 8, '0', STR_PAD_LEFT),
                'user_id'               => Auth::id(),
                'customer_id'           => $customer->id,
                'payment_type'          => $request->payment['method'],
                'subtotal'              => $request->payment['subtotal'],
                'discount'              => $request->payment['total_discount'],
                'discount_type'         => 'flat',
                'labour_cost'           => $request->labourCost,
                'transport_cost'        => $request->transportCost,
                'paid'                  => $request->payment['paid'],
                'due'                   => $request->sale_due ?? 0,
                'change'                => $request->payment['change'] ?? 0,
                'delivered'             => $request->delivered,
                'comment'               => $request->comment,
                'warehouse_id'          => $request->warehouse_id,
                'business_id'           => auth()->user()->business_id
            ];

            // insert sale
            $this->sale = Sale::create($sale_data);
            $sale = $this->sale;

            foreach ($request->products as $product) {
                $_product = Product::find($product['id']);

                $sale_details_data = [
                    'product_id'        => $product['id'],
                    'purchase_price'    => $product['purchase_price'],
                    'sale_price'        => $product['price'],
                    'vat'               => 0.00,
                    'discount'          => 0.00,
                    'quantity'          => $product['quantity'],
                    'quantity_in_unit'  => $product['quantity_in_unit'],
                    'discount_type'     => 'flat',
                    'line_total'        => $product['line_total'],
                ];

                // create sale details
                $sale->saleDetails()
                    ->create($sale_details_data);

                // decrement product quantity
                $warehouse = $_product->warehouses()
                    ->find($request->warehouse_id);

                // decrement quantity from warehouse
                $warehouse->stock->decrement('quantity', $product['quantity']);
            }

            // sale payment
            $sale->salePayment()
                ->create($request->payment['sale_payments']);

            // calculate paid amount
            $paid_amount = $request->payment['paid'];
            // if it has change
            if (isset($request->payment['change']) && $request->payment['change'] > 0) {
                $paid_amount = $request->payment['paid'] - $request->payment['change'];
            }

            // update customer balance
            // if customer has due then add the due in customer balance
            if ($request->payment['due'] >= 0) {
                $customer->increment('balance', $request->payment['due']);
            }

            // increment cash or bank account balance
            switch ($request->payment['method']) {
                case 'cash':
                    $cash_id = $request->payment['sale_payments']['cash_id'];
                    Cash::find($cash_id)->increment('amount', $paid_amount);
                    break;

                case 'bank':
                    $bank_account_id = $request->payment['sale_payments']['bank_account_id'];
                    BankAccount::find($bank_account_id)->increment('balance', $paid_amount);
                    break;
            }
        });
        return response()->json($this->sale);
    }

    /**
     * update retails sale
     *
     * @param [type] $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $this->meta['submenu'] = 'add';
        // $this->meta['aside'] = false; //hide aside

        $sale = Sale::with('saleDetails', 'customer')->findOrFail($id);
        $cashes = Cash::all();
        $warehouses = Warehouse::with('products.unit')->get();
        $customers = Customer::select('id', 'name', 'phone', 'address', 'type', 'balance')->get();

        $bank_accounts = BankAccount::with('bank')
            ->get();

        return view('user.pos.edit', compact('sale', 'warehouses', 'cashes', 'bank_accounts', 'customers'))
        ->with($this->meta);
    }

    /**
     * store updated sale
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function retailSaleUpdate(Request $request, $id)
    {
        DB::transaction(function() use($request, $id){
            $sale = Sale::with('saleDetails', 'salePayment')->findOrFail($id);

            $sale_updated_data = [
                'date'          => $request->date,
                'c_num'         => $request->cNum,
                'payment_type'  => $request->payment['method'],
                'subtotal'      => $request->payment['subtotal'],
                'discount'      => $request->payment['total_discount'],
                'discount_type' => 'flat',
                'paid'          => $request->payment['paid'],
                'due'           => $request->payment['due'] ?? 0,
                'change'        => $request->payment['change'] ?? 0,
                'delivered'     => $request->delivered,
                'comment'       => $request->comment,
            ];

            $removed_sale_details = $sale->saleDetails->pluck('id');

            // remove all sale details & update stock
            $this->removePreviousItemAndUpdateQuantity($removed_sale_details, $sale, $request);

            foreach ($request->products as $product) {

                $_product = Product::find($product['id']);

                $sale_details_data = [
                    'product_id'     => $product['id'],
                    'purchase_price' => $product['purchase_price'],
                    'sale_price'     => $product['price'],
                    'vat'            => 0.00,
                    'discount'       => 0.00,
                    'quantity'       => $product['quantity'],
                    'quantity_in_unit' => $product['quantity_in_unit'],
                    'discount_type'  => 'flat',
                    'line_total'     => $product['line_total'],
                ];

                // create sale details
                $sale->saleDetails()
                    ->create($sale_details_data);

                // decrement product quantity
                $warehouse = $_product->warehouses()
                    ->find($request->warehouse_id);

                // decrement quantity from warehouse
                $warehouse->stock->decrement('quantity', $product['quantity']);
            }

            // delete sale payment data
            $sale->salePayment()->where('sale_id', $sale->id)->delete();
            // sale payment
            $sale->salePayment()
            ->create($request->payment['sale_payments']);
            // return response($request->all());

            $previous_paid = ($sale->paid - $sale->change);

            if($sale->payment_type === 'cash') {
                $sale->salePayment->cash()->decrement('amount', $previous_paid);
            }else{
                $sale->salePayment->bankAccount()->decrement('balance', $previous_paid);
            }

            // calculate paid amount
            $paid_amount = $request->payment['paid'];
            // if has change
            if (isset($request->payment['change']) && $request->payment['change'] > 0) {
                $paid_amount = $request->payment['paid'] - $request->payment['change'];
            }

            // increment cash or bank account balance
            switch ($request->payment['method']) {
                case 'cash':
                    $cash_id = $request->payment['sale_payments']['cash_id'];
                    Cash::find($cash_id)->increment('amount', $paid_amount);
                    // TODO Create Ledger
                    break;
                case 'bank':
                    $bank_account_id = $request->payment['sale_payments']['bank_account_id'];
                    BankAccount::find($bank_account_id)->increment('balance', $paid_amount);
                    // TODO Create Ledger
                    break;
            }

            $sale->update($sale_updated_data);
            $this->sale = $sale;
        });
        return Response::json($this->sale);
    }

    /**
     * remove previous sale details and add sale quantity in sale warehouse
     *
     * @param [type] $removed_sale_details
     * @param [type] $sale
     * @param [type] $request
     * @return void
     */
    public function removePreviousItemAndUpdateQuantity($removed_sale_details, $sale, $request)
    {
        if ($removed_sale_details){
            foreach ($removed_sale_details as $item){
                // get deleted product
                $details = $sale->saleDetails->where('id', $item)->first();
                // quantity that should be add in warehouse
                $_quantity = $details->quantity;
                // get product
                $product = Product::findOrFail($details->product_id);
                // get warehouse
                $_warehouse = $product->warehouses->where('id', $request->warehouse_id)->first();
                if($_warehouse) {
                    //get exists quantity
                    $previous_quantity = $_warehouse->stock->quantity;
                    //update stocks
                    $product->warehouses()->updateExistingPivot($request->warehouse_id, [
                        'quantity' => $previous_quantity + $_quantity,
                    ]);
                }else{ // no previous warehouse exists
                    //add new stock in for products
                    $product->warehouses()->attach([
                        $request->orders[0]['warehouse_id'] =>  [
                            'quantity' => $_quantity,
                            'average_purchase_price' => $product->purchase_price,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    ]);
                }
                // deleted that product which in no available in new updated sale
                $sale->saleDetails->find($item)->delete();
            }
        }
    }
}
