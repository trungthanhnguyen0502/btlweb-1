<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\TicketAttachment;
use Illuminate\Http\Request;

class TicketAttachmentController extends Controller
{
    /**
     * Output attachment as file
     *
     * @param Request $request
     * @param $id string
     *
     * @return $this|string
     */

    public function get_attachment($id, $filename = '')
    {
        if ($id > 0) {
            $attachment = TicketAttachment::where('id', $id)->get();

            if ($attachment->count() == 0) {
                return null;
            }

            $attachment = $attachment[0];

            if ($filename == '') {
                return redirect($attachment->file_name);
            }

            return response($attachment->data)
                ->header('Content-Type', $attachment->mime_type)
                ->header('Content-Length', $attachment->size)
                ->header('Content-Disposition', "inline; filename={$filename}");
        }

        return redirect(route('home'));
    }

}
