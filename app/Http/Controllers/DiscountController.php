<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Discount;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'DiscountName' => 'required','string',
            'DiscountCharge' => 'required','integer',
            'DiscountCategory'=>'string',
            'DiscountDescription'=>'string'
        ]);

        $created_by=Auth::user()->id;
        $discount = new Discount();
        $discount->created_by=$created_by;
        $discount->name=request('DiscountName');
        $discount->description=request('DiscountDescription');
        if(request('DiscountCategory')=='AMOUNT')
        {
        $discount->discount_amount=request('DiscountCharge');
        } else {
        $discount->discount_percentage=request('DiscountCharge');
        }
        $discount->save();

        return redirect('/dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        return view('discount.show',['discount'=>$discount]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        return view('discount.edit',['discount'=>$discount]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'editSDiscountName' => 'required','string',
            'editDiscountCharge' => 'required','integer',
            'DiscountDescription'=>'string'
        ]);

        $discount=Discount::findorfail(request('id'));
        $discount->name=request('editSDiscountName');
        $discount->description=request('editDiscountDescription');
        if(request('editDiscountCategory')=='AMOUNT')
        {
            $discount->discount_amount=request('editDiscountCharge');
        } else {
            $discount->discount_percentage=request('editDiscountCharge');
        }
        $discount->save();
        return redirect('/dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $discount=Discount::findorfail($id);
        $discount->delete();
        return redirect('/dashboard');
    }
}
