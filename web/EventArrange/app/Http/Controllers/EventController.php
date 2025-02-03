<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    // イベント一覧を表示
    public function index()
    {
        $events = Event::all(); // すべてのイベントを取得
        return view('events.index', compact('events')); // ビューを返す
    }

    // イベント作成
    public function store(Request $request)
    {
        $event = Event::create([
            'name' => $request->name,
            'edit_password' => bcrypt($request->edit_password),
            'detail' => $request->detail
        ]);

        return response()->json([
            'message' => 'イベント作成成功',
            'event_id' => $event->id,
            'url' => url("/events/" . $event->id)
        ]);
    }

    // 特定のイベントを表示
    public function show($id)
    {
        $event = Event::with(['availabilities.responses.user'])->findOrFail($id);
        return response()->json($event);
    }
}
