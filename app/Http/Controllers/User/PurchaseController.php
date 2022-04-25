<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Party;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Warehouse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    private $meta = [
        'title'   => 'Purchase',
        'menu'    => 'purchase',
        'submenu' => '',
        'header'  => false
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    private $purchase;
    private $errors;

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $this->meta['submenu'] = 'list';

        $parties = Party::all();
        $purchases = Purchase::orderBy('id', 'desc')->paginate(30);

        return view('user.purchase.index', compact('parties', 'purchases'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $this->meta['submenu'] = 'add';
        $this->meta['aside'] = false; //hide aside

        $cashes = Cash::all();
        $warehouses = Warehouse::with('products.unit')->get();
        $products = Product::with('warehouses', 'unit')->get();
        $parties = Party::select('id', 'name', 'phone', 'address', 'balance')->get();
        $bank_accounts = BankAccount::with('bank')
            ->get();

        return view('user.purchase.create', compact(
                'warehouses',
                'cashes',
                'parties',
                'products',
                'bank_accounts')
        )->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
//        return $request->all();
//        return $request->payment['due'];
        DB::transaction(function () use($request) {
            $purchase_data = [
                'date'                  => $request->date,
                'payment_type'          => $request->payment['method'],
                'party_id'              => $request->party_id,
                'cash_id'               => $request->payment['method'] == 'cash' ? $request->payment['purchase_payments']['cash_id'] : null,
                'bank_account_id'       => $request->payment['method'] == 'bank' ? $request->payment['purchase_payments']['bank_account_id'] : null,
                'warehouse_id'          => $request->warehouse_id,
                'voucher_no'            => 'VOUCHER-' . str_pad(Purchase::max('id') + 1, 8, '0', STR_PAD_LEFT),
                'subtotal'              => $request->payment['subtotal'],
                'discount'              => $request->payment['total_discount'],
                'discount_type'         => 'flat',
                'labour_cost'           => $request->labourCost,
                'transport_cost'        => $request->transportCost,
                'paid'                  => $request->payment['paid'],
                'due'                   => $request->payment['due'] ?? 0,
                'previous_balance'      => $request->previous_balance ?? 0,
                'note'                  => $request->note,
                'user_id'               => Auth::id(),
                'business_id'           => auth()->user()->business_id
            ];

            // insert purchase
            $this->purchase = Purchase::create($purchase_data);
            $purchase = $this->purchase;

            foreach ($request->products as $product) {
                $_product = Product::find($product['id']);

                $purchase_details_data = [
                    'product_id' => $product['id'],
                    'purchase_price' => $product['purchase_price'],
                    'quantity' => $product['quantity'],
                    'quantity_in_unit' => $product['quantity_in_unit'],
                    'discount' => 0.00,
                    'discount_type' => 'flat',
                    'line_total' => $product['line_total'],
                ];

                // create purchase details
                $purchase->details()->create($purchase_details_data);

                // decrement product quantity
                $warehouse = $_product->warehouses()
                    ->find($request->warehouse_id);

                if($warehouse){
                    //get exists quantity
                    $previous_quantity = $warehouse->stock->quantity;

                    // get total quantity
                    $total_quantity = $warehouse->stock->quantity + $product['quantity'];

                    // get percentage of previous quantity
                    $percentage_of_previous_quantity = ($previous_quantity * 100) / $total_quantity;

                    // get percentage of present quantity
                    $percentage_of_present_quantity = ($product['quantity'] * 100) / $total_quantity;

                    // get previous stock percentage price
                    $previous_average_purchase_price = $percentage_of_previous_quantity * ($warehouse->stock->average_purchase_price / 100);

                    // get present stock percentage price
                    $present_average_purchase_price = $percentage_of_present_quantity * ($_product->purchase_price / 100);

                    // total quantity purchase price
                    $total_price = $previous_average_purchase_price + $present_average_purchase_price;

                    //update stocks
                    $_product->warehouses()->updateExistingPivot($warehouse->id, [
                        'quantity' => $previous_quantity + $product['quantity'],
                        'average_purchase_price' => $total_price,
                    ]);

                }
                // no previous warehouse exists
                else{
                    //add new stock in for products
                    $_product->warehouses()->attach([
                        $request->warehouse_id =>  [
                            'quantity' => $product['quantity'],
                            'average_purchase_price' => $product['purchase_price'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    ]);
                }

            }

            // calculate paid amount
            $paid_amount = $request->payment['paid'];
            // if it has change
            if (isset($request->payment['change']) && $request->payment['change'] > 0) {
                $paid_amount = $request->payment['paid'] - $request->payment['change'];
            }

            $party = Party::findOrFail($request->party_id);
            if (isset($request->payment['due']) && $request->payment['due'] > 0) {
                $party->balance = $request->payment['due'];
            }else{
                $party->balance = 0;
            }
            $party->save();

            // increment cash or bank account balance
            switch ($request->payment['method']) {
                case 'cash':
                    $cash_id = $request->payment['purchase_payments']['cash_id'];
                    Cash::find($cash_id)->decrement('amount', $paid_amount);
                    break;

                case 'bank':
                    $bank_account_id = $request->payment['purchase_payments']['bank_account_id'];
                    BankAccount::find($bank_account_id)->decrement('balance', $paid_amount);
                    break;
            }

        });

        return response()->json($this->purchase, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|Response|View
     */
    public function show($id)
    {
        $this->meta['submenu'] = 'list';
        $purchase = Purchase::findOrFail($id);
        return view('user.purchase.show', compact('purchase'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
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
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
