<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Customer;
use Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    private $customer;
    private $meta = [
        'title'   => 'Customer',
        'menu'    => 'customer',
        'submenu' => ''
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

        $business_id = Auth::user()->business_id;
        $customers = Customer::where('business_id', $business_id)->paginate(30);

        if (request()->search) {

            // set conditions
            $where = [
                ['business_id', '=', $business_id]
            ];

            foreach (request()->condition as $input => $value) {
                // return $input;
                if ($value != null) {
                    if ($input == 'name') {
                        $where[] = [$input, 'like', '%' . $value . '%'];
                    } else {
                        $where[] = [$input, '=', $value];
                    }
                }
            }

            $customers = Customer::where($where)->paginate(30);
        }

        return view('user.customer.index', compact('customers'))->with($this->meta);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->meta['submenu'] = 'add';
        $brands = Brand::all();
        return view('user.customer.create', compact('brands'))->with($this->meta);
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
                'name'           => 'required|string|max:191',
                'phone'          => 'required',
                'email'          => 'nullable|email',
                'address'        => 'nullable|string',
                'type'           => 'required|string',
                'credit_limit'   => 'required|numeric|min:0',
                'description'    => 'nullable|string',
                'thumbnail'      => 'nullable|integer'
            ]);

            $metas = $request->validate([
                'contact_person'       => 'nullable|string',
                'contact_person_phone' => 'nullable|string',
            ]);

            $data['code']  = 'CUST' . str_pad(Customer::max('id') + 1, 8, '0', STR_PAD_LEFT);
            $data['business_id'] = Auth::user()->business_id;

            if ($request->balance_status == 'payable') {
                $data['balance'] = -1 * $request->balance;
            } else{
                $data['balance'] = $request->balance;
            }

//            return $data;
            $this->customer = Customer::create($data); //create customer
            $customer = $this->customer;

            foreach ($metas as $key => $meta) {
                $customer->metas()->create(['meta_key' => $key, 'meta_value' => $meta]);
            }
        });

        if($this->customer){
            return redirect()->back()->withSuccess('Customer created successfully');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $business_id = Auth::user()->business_id;
        $customer = Customer::where('business_id', $business_id)->find($id);
        return view('user.customer.show', compact('customer'))->with($this->meta);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business_id = Auth::user()->business_id;
        $customer = Customer::where('business_id', $business_id)->find($id);

        return view('user.customer.edit', compact('customer'))->with($this->meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
//        return $request->all();
        DB::transaction(function() use($request, $customer) {
            $data = $request->validate([
                'name'           => 'required|string|max:191',
                'phone'          => 'required|max:45',
                'email'          => 'nullable|email',
                'type'           => 'required|string',
                'credit_limit'   => 'required|numeric|min:0',
                'address'        => 'nullable|string',
                'description'    => 'nullable|string',
                'thumbnail'      => 'nullable|integer'
            ]);

            $metas = $request->validate([
                'contact_person'       => 'nullable|string',
                'contact_person_phone' => 'nullable|string'
            ]);

            if ($request->balance_status == 'payable') {
                $data['balance'] = -1 * $request->balance;
            } else{
                $data['balance'] = $request->balance;
            }

            $this->customer = $customer->update($data); //update supplier

            foreach ($metas as $key => $value) {
                $customer->metas()->updateOrCreate(['meta_key' => $key], ['meta_value' => $value]);
            }
        });

        if($this->customer){
            return redirect()->route('customer.index')->withSuccess('Customer updated successfully');
        }
    }

    public function changeCustomerStatus($id)
    {
        $customer = Customer::find($id);
        $customer->active = ($customer->active) ? 0 : 1;
        $customer->save();

        return redirect()->back()->withSuccess($customer->name . ' customer successfully ' . ($customer->active == true ? 'Activated' : 'Deactivated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->back()->withSuccess('Customer deleted successfully');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|ViewAlias
     */
    public function viewTrashed()
    {
        $trashedCustomers = Customer::onlyTrashed()->get();

        return view('user.customer.trashed', compact('trashedCustomers'))->with($this->meta);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function forceDelete($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->forceDelete();
        return redirect()->back()->withSuccess('customer delete successfully');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->deleted_at = null;
        $customer->save();

        return redirect()->back()->withSuccess('customer restore successfully');
    }

    /*------------------AJAX METHOD-------------------*/

    /**
     * All active customers
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function allActiveCustomers()
    {
        $business_id = Auth::user()->business_id;
        return response(Customer::where('business_id', $business_id)->with('media')->customers()->active()->orderBy('created_at', 'DESC')->get(), 200);
    }

    public function createNewCustomer(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:191',
            'phone'       => 'nullable|max:45',
            'email'       => 'nullable|email',
            'address'     => 'nullable|string',
        ]);


        $data['code']  = 'PRT' . str_pad(Customer::max('id') + 1, 8, '0', STR_PAD_LEFT);
        $data['business_id'] = Auth::user()->business_id;

        $customer = Customer::create($data);

        return response()->json($customer, 200);
    }
}
