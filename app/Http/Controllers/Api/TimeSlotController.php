<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HighlightTypeResource;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timeSlots = TimeSlot::all();
        return HighlightTypeResource::collection($timeSlots);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_slot_name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $brands = TimeSlot::create($request->all());
        
        return new HighlightTypeResource($brands);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeSlot  $timeSlot
     * @return \Illuminate\Http\Response
     */
    public function show(TimeSlot $timeSlot)
    {
        $timeSlot = TimeSlot::find($timeSlot->id);
      
        if (is_null($timeSlot)) {
            return $this->sendError('Brand not found.');
        }
        $response = ['status'=>true, 'data'=>$timeSlot];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeSlot  $timeSlot
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeSlot $timeSlot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeSlot  $timeSlot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $timeSlot = TimeSlot::find($id);
        $validator = Validator::make($request->all(), [
            'time_slot_name' => 'required'
        ]);
       
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $timeSlot->update($request->all());
        
        return new HighlightTypeResource($timeSlot);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeSlot  $timeSlot
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $timeSlot = TimeSlot::find($id);
        if($timeSlot->delete()){
            $response = ['status'=>true,'message' => 'Deleted successfully.'];
            return response($response, 200);
        }else{
            $response = ['status'=>false,'message' => 'Something is wrong'];
            return response($response, 200);
        }
    }
}
