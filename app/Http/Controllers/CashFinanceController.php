<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use Illuminate\Http\Request;
use App\Models\Financial_entry;
use App\Models\Cash_box;
use App\Models\Client;
use App\Models\Open_balance;
use App\Models\Currency;
use App\Models\Finan_trans_type;
use App\Models\Supplier;
use App\Models\Agent;
use File;
use DB;
use Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class CashFinanceController extends Controller
{
    protected $object;

    protected $viewName;
    protected $routeName;
    protected $message;
    protected $errormessage;

    public function __construct(Financial_entry $object)
    {
        $this->middleware('auth');

        $this->object = $object;
        $this->viewName = 'cash-finance.';
        $this->routeName = 'cash-finance.';
        $this->message = 'The Data has been saved';
        $this->errormessage = 'check Your Data ';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rows = Cash_box::orderBy("created_at", "Desc")->get();

        return view($this->viewName . 'index', compact('rows',));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Adding with id
     */
    public function addCashFinance($id)
    {
        $Selectrow = Cash_box::where('id', '=', $id)->first();
        $currentBalance = Financial_entry::where('cash_box_id', $Selectrow->id)->sum('depit') - Financial_entry::where('cash_box_id', $Selectrow->id)->sum('credit');

        $clients = Client::all();

        return view($this->viewName . 'add', compact('Selectrow', 'clients', 'currentBalance'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //save in finance entry
        if ($request->input('tab') === "igottwo") {
            $obj = new Financial_entry();

            $obj->trans_type_id = Finan_trans_type::where('id', '=', 2)->first()->id;
            $obj->entry_date = Carbon::parse($request->input('entry_date'));
            $obj->depit = $request->input('depit');
            $obj->currency_id = $request->input('currency_id');
            $obj->cash_box_id = $request->input('cash_box_id');
            $obj->notes = $request->input('notes');

            if ($request->input('client_id')) {
                $obj->client_id = $request->input('client_id');
            }
        } else {
            $fristSelect = $request->input('selector_type');
            $obj = new Financial_entry();

            $obj->trans_type_id = Finan_trans_type::where('id', '=', $request->input('selector_type'))->first()->id;
            $obj->entry_date = Carbon::parse($request->input('entry_date'));
            $obj->credit = $request->input('credit');
            $obj->currency_id = $request->input('currency_id');
            $obj->cash_box_id = $request->input('cash_box_id');
            $obj->notes = $request->input('notes');

            if ($fristSelect == 3) {
                $obj->ocean_carrier_id = $request->input('xxselector');
            }
            if ($fristSelect == 4) {
                $obj->air_carrier_id = $request->input('xxselector');
            }

            if ($fristSelect == 5) {
                $obj->trucking_id = $request->input('xxselector');
            }
            if ($fristSelect == 6) {
                $obj->clearance_id = $request->input('xxselector');
            }
            if ($fristSelect == 7) {
                $obj->agent_id = $request->input('xxselector');
            }
        }
        DB::transaction(function () use ($obj,  $request) {
            $obj->save();
            // $this->object::save($obj);

            $cashUpdate = Cash_box::where('id', '=', $request->input('cash_box_id'))->first();
            $currentBalance = Financial_entry::where('cash_box_id', $request->input('cash_box_id'))->sum('depit') - Financial_entry::where('cash_box_id', $request->input('cash_box_id'))->sum('credit');

            $cash_data = [
                'current_balance' => $currentBalance,
            ];
            // $cashUpdate->update($cash_data);
        });


        return redirect()->route($this->routeName . 'show', $request->input('cash_box_id'))->with('flash_success', $this->message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Selectrow = Cash_box::where('id', '=', $id)->first();
        $currentBalance = Financial_entry::where('cash_box_id', $Selectrow->id)->sum('depit') - Financial_entry::where('cash_box_id', $Selectrow->id)->sum('credit');

        $rows = Financial_entry::where('cash_box_id', '=', $id)->orderBy("created_at", "Desc")->get();

        return view($this->viewName . 'select', compact('Selectrow', 'rows', 'currentBalance'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editrow = Financial_entry::where('id', '=', $id)->first();
        $Selectrow = Cash_box::where('id', '=', $editrow->cash_box_id)->first();
        $currentBalance = Financial_entry::where('cash_box_id', $Selectrow->id)->sum('depit') - Financial_entry::where('cash_box_id', $Selectrow->id)->sum('credit');

        $clients = Client::all();
        $dataClient = 0;
        $dataOther = 0;
        if ($editrow->client_id) {
            $dataClient = Financial_entry::where('client_id', $editrow->client_id)->where('currency_id', '=', $editrow->currency_id)->sum('credit') - Financial_entry::where('client_id', $editrow->client_id)->where('currency_id', '=', $editrow->currency_id)->sum('depit');
        }
        if ($editrow->ocean_carrier_id) {
            $dataOther = Financial_entry::where('ocean_carrier_id', $editrow->ocean_carrier_id)->where('currency_id', '=', $editrow->currency_id)->sum('depit') - Financial_entry::where('ocean_carrier_id', $editrow->ocean_carrier_id)->where('currency_id', '=', $editrow->currency_id)->sum('credit');
        }
        if ($editrow->air_carrier_id) {
            $dataOther = Financial_entry::where('air_carrier_id', $editrow->air_carrier_id)->where('currency_id', '=', $editrow->currency_id)->sum('depit') - Financial_entry::where('air_carrier_id', $editrow->air_carrier_id)->where('currency_id', '=', $editrow->currency_id)->sum('credit');
        }
        if ($editrow->trucking_id) {
            $dataOther = Financial_entry::where('trucking_id', $editrow->trucking_id)->where('currency_id', '=', $editrow->currency_id)->sum('depit') - Financial_entry::where('trucking_id', $editrow->trucking_id)->where('currency_id', '=', $editrow->currency_id)->sum('credit');
        }
        if ($editrow->clearance_id) {
            $dataOther = Financial_entry::where('clearance_id', $editrow->clearance_id)->where('currency_id', '=', $editrow->currency_id)->sum('depit') - Financial_entry::where('clearance_id', $editrow->clearance_id)->where('currency_id', '=', $editrow->currency_id)->sum('credit');
        }
        if ($editrow->agent_id) {
            $dataOther = Financial_entry::where('agent_id', $editrow->agent_id)->where('currency_id', '=', $editrow->currency_id)->sum('depit') - Financial_entry::where('agent_id', $editrow->agent_id)->where('currency_id', '=', $editrow->currency_id)->sum('credit');
        }
        return view($this->viewName . 'edit', compact('editrow', 'Selectrow', 'clients', 'dataOther','currentBalance', 'dataClient'));
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
        //save in finance entry
        if ($request->input('tab') === "igottwo") {
            $obj =  $this->object::findOrFail($id);


            $obj->entry_date = Carbon::parse($request->input('entry_date'));
            $diffetant = $request->input('depit') - $obj->depit;
            $obj->depit = $obj->depit + $diffetant;
            $obj->notes = $request->input('notes');

            $obj->update();
        } else {

            $obj = $this->object::findOrFail($id);
            $obj->entry_date = Carbon::parse($request->input('entry_date'));
            $diffetant = $request->input('credit') - $obj->credit;
            $obj->credit = $obj->credit + $diffetant;
            $obj->notes = $request->input('notes');
            $obj->update();
        }



        return redirect()->route($this->routeName . 'show', $request->input('cash_box_id'))->with('flash_success', $this->message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $row = Financial_entry::where('id', '=', $id)->first();

        try {
            $row->delete();
        } catch (QueryException $q) {

            return redirect()->back()->with('flash_danger', 'You cannot delete related with another...');
        }

        return redirect()->back()->with('flash_success', 'Data Has Been Deleted Successfully !');
    }

    function clientSelect(Request $request)
    {
        $dataAjax = array();
        $select = $request->get('select');
        $value = $request->get('value');
        $cash = $request->get('cash');
        $clients = Open_balance::where('client_id', '=', $value)->get();
        foreach ($clients as $client) {

            if ($cash == $client->currency_id) {

                $data = Financial_entry::where('client_id', $value)->where('currency_id', '=', $cash)->sum('credit') - Financial_entry::where('client_id', $value)->where('currency_id', '=', $cash)->sum('depit');
                break;
            } else {

                $data = 0;
            }
        }


        $currency = Currency::where('id', '=', $cash)->first()->currency_name;

        array_push($dataAjax, $data);
        array_push($dataAjax, $currency);

        return ($dataAjax);
    }



    function selector_type(Request $request)
    {
        $dataAjax = array();
        $select = $request->get('select');
        $value = $request->get('value');
        $cash = $request->get('cash');
        $data = [];
        switch ($value) {
            case 3:
                $data = Carrier::where('carrier_type_id', '=', 1)->get();

                break;
            case 4:
                $data = Carrier::where('carrier_type_id', '=', 2)->get();

                break;
            case 5:
                $data = Supplier::where('supplier_type_id', '=', 1)->get();

                break;
            case 6:
                $data = Supplier::where('supplier_type_id', '=', 2)->get();

                break;
            case 7:
                $data = Agent::all();

                break;

            default:
                break;
        }

        $output = '<option value="">Select </option>';

        foreach ($data as $row) {
            if ($value == 3 || $value == 4) {
                $output .= '<option value="' . $row->id . '">' . $row->carrier_name . '</option>';
            }
            if ($value == 5 || $value == 6) {
                $output .= '<option value="' . $row->id . '">' . $row->supplier_name . '</option>';
            }
            if ($value == 7) {
                $output .= '<option value="' . $row->id . '">' . $row->agent_name . '</option>';
            }
        }

        echo $output;
    }


    function SelectionSelect(Request $request)
    {
        $dataAjax = array();
        $select = $request->get('select');
        $value = $request->get('value');
        $cash = $request->get('cash');
        $fristSelect = $request->get('fristSelect');

        if ($fristSelect == 3 || $fristSelect == 4) {
            $rows = Open_balance::where('carrier_id', '=', $value)->get();
        }
        if ($fristSelect == 5 || $fristSelect == 6) {
            $rows = Open_balance::where('supplier_id', '=', $value)->get();
        }
        if ($fristSelect == 7) {
            $rows = Open_balance::where('agent_id', '=', $value)->get();
        }

        foreach ($rows as $row) {

            if ($cash == $row->currency_id) {
                if ($fristSelect == 3) {
                    $data = Financial_entry::where('ocean_carrier_id', $value)->where('currency_id', '=', $cash)->sum('depit') - Financial_entry::where('ocean_carrier_id', $value)->where('currency_id', '=', $cash)->sum('credit');
                }
                if ($fristSelect == 4) {
                    $data = Financial_entry::where('air_carrier_id', $value)->where('currency_id', '=', $cash)->sum('depit') - Financial_entry::where('air_carrier_id', $value)->where('currency_id', '=', $cash)->sum('credit');
                }

                if ($fristSelect == 5) {
                    $data = Financial_entry::where('trucking_id', $value)->where('currency_id', '=', $cash)->sum('depit') - Financial_entry::where('trucking_id', $value)->where('currency_id', '=', $cash)->sum('credit');
                }
                if ($fristSelect == 6) {
                    $data = Financial_entry::where('clearance_id', $value)->where('currency_id', '=', $cash)->sum('depit') - Financial_entry::where('clearance_id', $value)->where('currency_id', '=', $cash)->sum('credit');
                }
                if ($fristSelect == 7) {
                    $data = Financial_entry::where('agent_id', $value)->where('currency_id', '=', $cash)->sum('depit') - Financial_entry::where('agent_id', $value)->where('currency_id', '=', $cash)->sum('credit');
                }
                break;
            } else {

                $data = 0;
            }
        }


        $currency = Currency::where('id', '=', $cash)->first()->currency_name;

        array_push($dataAjax, $data);
        array_push($dataAjax, $currency);

        return ($dataAjax);
    }
}