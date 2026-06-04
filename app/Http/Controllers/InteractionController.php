<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Interaction;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function store(Request $request, Business $business)
    {
        $validated = $request->validate([
            'type' => 'required|in:email,dm,phone,call,meeting,note',
            'direction' => 'required|in:inbound,outbound',
            'content' => 'required|string',
            'occurred_at' => 'required|date',
        ]);

        $normalizedType = $validated['type'] === 'call' ? 'phone' : $validated['type'];

        Interaction::create([
            'business_id' => $business->id,
            'type' => $normalizedType,
            'direction' => $validated['direction'],
            'content' => $validated['content'],
            'occurred_at' => $validated['occurred_at'],
        ]);

        return redirect('/businesses/' . $business->id)->with('success', 'Interaction added.');
    }
}
