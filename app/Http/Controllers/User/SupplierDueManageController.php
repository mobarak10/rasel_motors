<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Party;
use App\Models\SupplierDueManagement;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierDueManageController extends Controller
{
    private $due_manage;
    private $meta = [
        'title'   => 'Due Management',
        'menu'    => 'manage-due',
        'submenu' => ''
    ];

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->meta['submenu'] = 'supplier';
        $business_id = Auth::user()->business_id;
        $manage_dues = SupplierDueManagement::where('business_id', $business_id)->paginate(30);
        return view('user.supplier-due-management.index', compact('manage_dues'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = Auth::user()->business_id;
        // read cash, bank and supplier/customer
        $cashes = Cash::where('business_id', $business_id)->get();
        $banks = Bank::with('bankAccounts')->where('business_id', $business_id)->get();
        $suppliers = Party::where('business_id', $business_id)->get();

        return view('user.supplier-due-management.create', compact('cashes', 'banks', 'suppliers'))->with($this->meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::transaction(function() use($request) {
            $data = $request->validate([
                'party_id' => 'required',
                'date' => 'required',
                'paid_from' => 'required|string',
                'cash_id' => 'nullable|integer',
                'bank_account_id' => 'nullable|integer',
                'check_issue_date' => 'nullable|date',
                'check_number' => 'nullable',
                'description' => 'nullable',
            ]);

            $supplier = Party::findOrFail($request->party_id);

            if($request->payment_type === 'received'){
                $data['amount'] = $request->amount;
                $supplier->decrement('balance', $request->amount);
            }else{
                $data['amount'] = (-1 * $request->amount);
                $supplier->increment('balance', $request->amount);
            }

            $data['user_id'] = Auth::id();
            $data['business_id'] = Auth::user()->business_id;

            $this->due_manage = SupplierDueManagement::create($data);

            if($request->paid_from === 'cash'){
                $cash = Cash::findOrFail($request->cash_id);
                if($request->payment_type === 'received'){
                    $cash->increment('amount', $request->amount);
                }else{
                    $cash->decrement('amount', $request->amount);
                }
            }else{
                $bank_account = BankAccount::findOrFail($request->bank_account_id);
                if($request->payment_type === 'received'){
                    $bank_account->increment('balance', $request->amount);
                }else{
                    $bank_account->decrement('balance', $request->amount);
                }
            }
        });

        return response($this->due_manage);
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
