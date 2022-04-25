<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Brand;
use App\Models\Business;
use App\Models\Cash;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    protected $sale_return;
    private $meta = [
        'title'   => 'Sale Return',
        'menu'    => 'sale-return',
        'submenu' => '',
        'header'  => false
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->meta['submenu'] = 'list';
        $sale_returns = SaleReturn::paginate(30);

        return view('user.sale-return.index', compact('sale_returns'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->meta['submenu'] = 'add';
        $this->meta['aside'] = false; //hide aside

        $cashes = Cash::all();

        $warehouses = Warehouse::with('products.unit')->get();

        $customers = Customer::where('type', 'dealer')->select('id', 'name', 'phone', 'address', 'balance')->get();

        $bank_accounts = BankAccount::with('bank')
            ->get();


        return view('user.sale-return.create', compact('warehouses', 'cashes', 'bank_accounts', 'customers'))
            ->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        DB::transaction(function () use ($request) {

            $customer = Customer::findOrFail($request->customer_id);

            $sale_return_data = [
                'return_no'         => 'RTRN' . str_pad(SaleReturn::max('id') + 1, 8, '0', STR_PAD_LEFT),
                'user_id'           => Auth::id(),
                'customer_id'       => $customer->id,
                'warehouse_id'      => $request->warehouse_id,
                'date'              => $request->date,
                'subtotal'          => $request->payment['subtotal'],
                'discount'          => $request->payment['discount'],
                'paid'              => $request->payment['paid'],
                'paid_from'         => $request->payment['method'],
                'cash_id'           => $request->cash_id ? $request->cash_id : null,
                'bank_account_id'   => $request->bank_account_id ? $request->bank_account_id : null,
                'note'              => $request->note,
                'business_id'       => auth()->user()->business_id
            ];

            // insert sale
            $this->sale_return = SaleReturn::create($sale_return_data);

            $sale_return = $this->sale_return;

            foreach ($request->products as $product) {
                $_product = Product::find($product['id']);
                $sale_return_product_data = [
                    'product_id'        => $product['id'],
                    'purchase_price'    => $product['purchase_price'],
                    'return_price'      => $product['price'],
                    'quantity'          => $product['quantity'],
                    'quantity_in_unit'  => $product['quantity_in_unit'],
                    'line_total'        => $product['line_total'],
                ];

                // create sale details
                $sale_return->returnProducts()->create($sale_return_product_data);

                // decrement product quantity
                $warehouse = $_product->warehouses()
                    ->find($request->warehouse_id);

                // decrement quantity from warehouse
                $warehouse->stock->increment('quantity', $product['quantity']);
            }

            // calculate paid amount
            $paid_amount = $request->payment['paid'];

            // update customer balance
            // if customer has due then add the due in customer balance
            $customer->balance = ($request->payment['due'] > 0) ? (-1 * $request->payment['due']) : $request->payment['change'];
            $customer->save();

//            return $customer->balance;

            $sale_return->update([
                'customer_balance' => $customer->balance
            ]);


            // increment cash or bank account balance
            if($request->cash_id) {
                Cash::find($request->cash_id)->decrement('amount', $paid_amount);
            }else{
                BankAccount::find($request->bank_account_id)->decrement('balance', $paid_amount);
            }
        });
        return response()->json($this->sale_return);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sale_return = SaleReturn::findOrFail($id);
        $business = Business::findOrFail(Auth::user()->business_id);

        return view('user.sale-return.show', compact('sale_return', 'business'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
