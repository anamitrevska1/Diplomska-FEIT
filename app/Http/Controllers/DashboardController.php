<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Discount;
use App\Models\Service;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $customers =Customer::query()
            ->where('created_by',request()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $services =Service::query()
            ->where('created_by',request()->user()->id)
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        $discounts = Discount::query()
            ->where('created_by',request()->user()->id)
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        return view('dashboard',['customers'=>$customers,'services'=>$services,'discounts'=>$discounts]);
    }
}
