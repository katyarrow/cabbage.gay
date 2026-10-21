<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteWideMessage;

class AdminPanelController extends Controller
{
    public function index()
    {
        $currentMessages = SiteWideMessage::currentlyViewable()->orderBy('created_at')->get();
        $oldMessages = SiteWideMessage::notCurrentlyViewable()->orderBy('created_at')->get();

        return view('admin.index', [
            'messagesCurrent' => $currentMessages,
            'messagesOld' => $oldMessages,
        ]);
    }
}
