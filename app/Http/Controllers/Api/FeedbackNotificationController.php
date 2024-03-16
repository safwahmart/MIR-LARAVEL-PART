<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PopupNotificationResource;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $popupNotifications = Feedback::all();
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
        $fileName = '';
        if ($request->image) {
            $fileName = time() . '.' . $request->image->extension();

            $request->image->move(public_path('uploads'), $fileName);
        }

        $popupNotifications = Feedback::create([
            "image" => $fileName,
        ]);

        return new PopupNotificationResource($popupNotifications);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feedback  $popupNotification
     * @return \Illuminate\Http\Response
     */
    public function show(Feedback $popupNotification)
    {
        $popupNotification = Feedback::find($popupNotification->id);

        if (is_null($popupNotification)) {
            return $this->sendError('Feedback not found.');
        }
        $response = ['status' => true, 'data' => $popupNotification];
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feedback  $popupNotification
     * @return \Illuminate\Http\Response
     */
    public function edit(Feedback $popupNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feedback  $popupNotification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $popupNotification = Feedback::find($id);
        $fileName = $popupNotification->image;
        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();

            $request->image->move(public_path('uploads'), $fileName);
        }
        $popupNotification->update([
            "image" => $fileName,
            "status" => $request->status ?? $popupNotification->status,
        ]);

        return new PopupNotificationResource($popupNotification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feedback  $popupNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $popupNotification = Feedback::find($id);
        $popupNotification->delete();
        $response = ['status' => true, 'message' => 'Deleted successfully.'];
        return response($response, 200);
    }
}
