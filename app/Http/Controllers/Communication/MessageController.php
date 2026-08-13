<?php

namespace App\Http\Controllers\Communication;

use App\Events\MessageDeletedEvent;
use App\Events\MessageSentEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private function successResponse($resource = null, string $message = "نجح", int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $resource,
        ], $code);
    }

    private function errorResponse(string $message = "خطأ", int $code = 403)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

    public function index()
    {
        return MessageResource::collection(
            Message::with('user')->latest()->take(50)->get()->reverse()->values()
        );
    }

    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();
        $message = Message::create([
            'message' => $data['message'],
            'user_id' => $request->user()->id,
        ]);
        $message->load('user');
        broadcast(new MessageSentEvent($message))->toOthers();

        return $this->successResponse(new MessageResource($message), 'تم إرسال الرسالة', 201);
    }

    public function destroy(Request $request, Message $message)
    {
        if ($message->user_id !== $request->user()->id) {
            return $this->errorResponse('لا يمكنك حذف رسالة مستخدم آخر', 403);
        }

        $messageId = $message->id;
        $message->delete();

        broadcast(new MessageDeletedEvent($messageId))->toOthers();

        return $this->successResponse(['id' => $messageId], 'تم حذف الرسالة بنجاح');
    }
}
