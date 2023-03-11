<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public AttachmentService $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->middleware(['auth:sanctum']);
        $this->attachmentService = $attachmentService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $files = $request->file('files');
        $user = auth()->user();
        $attachments = collect();
        $exists = array();
        $failed = array();

        foreach ($files as $file) {
            $dataFile = $this->attachmentService->upload($file, $user);

            try {

                switch ($dataFile['status']) {

                    case Attachment::STATUS_SUCCESS:
                        $attachment = $this->attachmentService->save($dataFile, $user);
                        $attachments->push($attachment);
                        break;

                    case Attachment::STATUS_EXISTS:
                        array_push($exists, $dataFile);
                        break;

                    case Attachment::STATUS_FAILED;
                        array_push($failed, $dataFile);
                }

            } catch (\Exception $exception) {

            }

            dd($dataFile);
            $attachment = $this->attachmentService->save($dataFile, $user);
            dd($attachment);


        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function show(Attachment $attachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function edit(Attachment $attachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attachment $attachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attachment $attachment)
    {
        //
    }
}
