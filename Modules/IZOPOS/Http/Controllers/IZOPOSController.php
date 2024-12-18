<?php

namespace Modules\IZOPOS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;

class IZOPOSController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('izopos::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('izopos::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('izopos::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('izopos::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Show the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function closeBox()
    {
        //
        return view('izopos::close_box');
    }

    /**
     * Show the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function salesReport()
    {
        //
        return view('izopos::sales_report');
    }

    /**
     * Show the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function Bills()
    {
        //
        return view('izopos::bills');
    }

    /**
     * Show the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function POS()
    {
        //
        return view('izopos::pos');
    }

}
