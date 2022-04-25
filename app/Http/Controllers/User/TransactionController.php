<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Party;
use App\Models\Transaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransactionController extends Controller
{
    private $meta = [
        'title'   => 'Transaction',
        'menu'    => 'transaction',
        'submenu' => ''
    ];

    private $transaction;

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $this->meta['submenu'] = 'list';
        $transactions = Transaction::paginate(30);

        return view('user.transaction.index', compact('transactions'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $this->meta['submenu'] = 'add';

        $suppliers = Party::all();
        $customers = Customer::all();

        return view('user.transaction.create', compact('suppliers', 'customers'))->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        DB::transaction(function () use($request) {
            $data = [
                'date' => $request->date,
                'transaction_from' => $request->transaction_from,
                'customer_id' => $request->customer_id,
                'party_id' => $request->party_id,
                'amount' => $request->amount,
                'note' => $request->note,
                'user_id' => \Auth::user()->id,
                'business_id' => \Auth::user()->business_id,
            ];

            // insert data into transaction table
            $this->transaction = Transaction::create($data);

            $party = Party::findOrFail($request->party_id); // get supplier
            $customer = Customer::findOrFail($request->customer_id); // get customer
            // if transaction from supplier then decrement supplier balance & increment customer balance
            if ($request->transaction_from == 'supplier'){
                $party->decrement('balance', $request->amount);
                $customer->increment('balance', $request->amount);
            }
            // else increment supplier balance & decrement customer balance
            else{
                $party->increment('balance', $request->amount);
                $customer->decrement('balance', $request->amount);
            }
        });

        return response()->json($this->transaction, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
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
