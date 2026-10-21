<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteWideMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminSiteMessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message_value' => 'required|max:5000',
            'message_level' => 'required|in:info,warning,danger',
            'message_hours_to_display' => 'required|numeric|min:1',
        ]);

        $message = new SiteWideMessage;
        $message->message = $request->input('message_value');
        $message->level = $request->input('message_level');
        $message->display_until = Carbon::now()->addHours($request->integer('message_hours_to_display'));
        $message->save();

        return redirect()->back();
    }

    public function destroy(SiteWideMessage $message)
    {
        $message->delete();

        return redirect()->back();
    }
}
