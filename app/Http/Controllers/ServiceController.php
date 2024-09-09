<?php

namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
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
            'ServiceName' => 'required','string',
            'ServiceCharge' => 'required','integer',
            'ServiceCategory'=>'string',
            'ServiceDescription'=>'string'
        ]);

        $created_by=Auth::user()->id;
        $service = new Service();
        $service->created_by=$created_by;
        $service->name=request('ServiceName');
        $service->type=request('ServiceCategory');
        $service->description=request('ServiceDescription');
        $service->service_charge=request('ServiceCharge');
        $service->save();

        return redirect('/dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return view('service.show',['service'=>$service]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('service.edit',['service'=>$service]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'ServiceName' => 'required','string',
            'ServiceCharge' => 'required','integer',
            'ServiceCategory'=>'string',
            'ServiceDescription'=>'string'
        ]);
        $service=Service::findorfail($id);
        $service->name=request('ServiceName');
        $service->type=request('ServiceCategory');
        $service->description=request('ServiceDescription');
        $service->service_charge=request('ServiceCharge');
        $service->save();
        return redirect('/dashboard');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $service=Service::findorfail($id);
        $service->delete();
        return redirect('/dashboard');
    }
}
