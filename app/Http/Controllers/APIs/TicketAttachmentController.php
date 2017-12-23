<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\TicketAttachment;
use Illuminate\Http\Request;

class TicketAttachmentController extends Controller
{
    /**
     * @param Request $request
     * @param $id string
     *
     * @return $this|string
     */

    public function get_attachment(Request $request, $id, $filename = "")
    {
        if ($id > 0) {
            $attachment = TicketAttachment::where('id', $id)->get();

            if ($attachment->count() == 0) {
                return '';
            }

            $attachment = $attachment[0];

            if ($filename == '') {
                $filename = $attachment->file_name;
            }

            return response($attachment->data)
                ->header('Content-Type', $attachment->mime_type)
                ->header('Content-Length', $attachment->size)
                ->header('Content-Disposition', "inline; filename={$filename}");
        } else {
            return '';
        }

    }
}
