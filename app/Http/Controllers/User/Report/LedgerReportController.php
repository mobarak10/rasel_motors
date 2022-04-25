<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\CustomerDueManagement;
use App\Models\Party;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\SupplierDueManagement;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;
use App\Models\DueManage;
use App\Models\Sale;
use App\Models\SaleReturn;

class LedgerReportController extends Controller
{
    private $meta = [
        'title'   => 'Ledger Report',
        'menu'    => 'ledger-report',
        'submenu' => 'ledger-report'
    ];

    public $data = [];

    public function __construct() {
        $this->middleware('auth');
    }

    public function supplierLedger()
    {
        $this->data['parties'] = Party::all();
        $party = '';
        $total_debit = 0;
        $total_credit = 0;
        $party_balance = 0;

        if (request()->search) {
            $party = Party::where('id', request()->party_id)->first();
            $party_balance = $party->balance;

            $this->data['party_ledgers'] =
                Search::add(Purchase::whereBetween('date', [\request()->from_date, \request()->to_date])
                    ->where('party_id', request()->party_id)
                    ->selectRaw("*, 'purchase' as 'type'"))

                    ->add(PurchaseReturn::whereBetween('created_at', [\request()->from_date." 00:00:00", \request()->to_date." 23:59:59"])
                        ->where('party_id', request()->party_id)
                        ->withOut('purchaseReturnProducts')
                        ->selectRaw("*, 'purchase_return' as 'type'"))

                    ->add(SupplierDueManagement::whereBetween('date', [\request()->from_date, \request()->to_date])
                        ->where('party_id', request()->party_id)
                        ->selectRaw("*, 'due_manage' as 'type'"))
                    ->get();

            foreach($this->data['party_ledgers'] as $ledger){
                $total_debit += ($ledger->grand_total);
                $total_credit += ($ledger->paid + $ledger->purchase_return_total);
                if($ledger->amount >= 0){
                    $total_debit += $ledger->amount;
                }else{
                    $total_credit -= $ledger->amount;
                }
            }
//            return $total_debit;
        }

        return view('user.reports.ledger.supplierLedger', compact('party', 'total_debit', 'party_balance', 'total_credit'))->with($this->meta)->with($this->data);
    }

    public function customerLedger()
    {
        $this->data['parties'] = Customer::all();
        $party = '';
        $total_debit = 0;
        $total_credit = 0;
        $party_balance = 0;

        if (request()->search) {
                $party = Customer::where('id', request()->party_id)->first();
                $party_balance = $party->balance;

                $this->data['party_ledgers'] =
                            Search::add(Sale::with('saleDetails')
                            ->whereBetween('date', [\request()->from_date, \request()->to_date])
                            ->where('customer_id', request()->party_id)
                            ->selectRaw("*, 'sale' as 'type'"))

                            ->add(SaleReturn::with('returnProducts')
                            ->whereBetween('date', [\request()->from_date, \request()->to_date])
                            ->where('customer_id', request()->party_id)
                            ->selectRaw("*, 'sale_return' as 'type'"))

                            ->add(CustomerDueManagement::whereBetween('date', [\request()->from_date, \request()->to_date])
                            ->where('customer_id', request()->party_id)
                            ->selectRaw("*, 'due_manage' as 'type'"))
                            ->get();

            foreach($this->data['party_ledgers'] as $ledger){
                $total_debit += ($ledger->grand_total + $ledger->return_paid);
                $total_credit += ($ledger->total_paid + $ledger->return_grand_total);
                if($ledger->amount <= 0){
                    $total_debit += $ledger->amount;
                }else{
                    $total_credit += $ledger->amount;
                }
            }
        }
        return view('user.reports.ledger.customerLedger', compact('party', 'total_debit', 'party_balance', 'total_credit'))->with($this->meta)->with($this->data);
    }
}
