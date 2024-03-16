<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PopupNotificationResource;
use App\Models\PopupNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PopupNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $popupNotifications = PopupNotification::all();
        return PopupNotificationResource::collection($popupNotifications);
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
            'title' => 'required|unique:popupNotifications',
            // 'image' => 'required'
        ]);
        $fileName= '';
        if ($request->image) {
            $fileName = time() . '.' . $request->image->extension();

            $request->image->move(public_path('uploads'), $fileName);
        }

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        // dd($fileName);
        // $popupNotification->save();
        $popupNotifications = PopupNotification::create([
            "title"=>$request->title,
            "title_bn"=>$request->title_bn,
            "desc"=>$request->meta_desc,
            "desc_bn"=>$request->meta_desc,
            "image"=> $fileName,
        ]);

        return new PopupNotificationResource($popupNotifications);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PopupNotification  $popupNotification
     * @return \Illuminate\Http\Response
     */
    public function show(PopupNotification $popupNotification)
    {
        $popupNotification = PopupNotification::find($popupNotification->id);

        if (is_null($popupNotification)) {
            return $this->sendError('PopupNotification not found.');
        }
        $response = ['status' => true, 'data' => $popupNotification];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PopupNotification  $popupNotification
     * @return \Illuminate\Http\Response
     */
    public function edit(PopupNotification $popupNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PopupNotification  $popupNotification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $popupNotification = PopupNotification::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        $fileName= $popupNotification->image;
        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();

            $request->image->move(public_path('uploads'), $fileName);
        }
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $popupNotification->update([
            "title"=>$request->title,
            "title_bn"=>$request->title_bn,
            "desc"=>$request->meta_desc,
            "desc_bn"=>$request->meta_desc,
            "image"=> $fileName,
            "status" => $request->status??$popupNotification->status,
        ]);

        return new PopupNotificationResource($popupNotification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PopupNotification  $popupNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $popupNotification = PopupNotification::find($id);
        $popupNotification->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
