<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function successResponse($resource = null, string $message = 'نجح', int $code = 200)
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

    public function index(NotificationRequest $request)
    {
        $data = $request->validated();

        $query = $request->user()
            ->notifications()
            ->with(['fromUser', 'vote.user', 'comment.user']);

        if (isset($data['type'])) {
            $query->where('type', $data['type']);
        }

        if (isset($data['is_read'])) {
            $query->where('is_read', $data['is_read']);
        }

        $notifications = $query->latest()->paginate();

        return NotificationResource::collection($notifications);
    }

    public function unReadCount(Request $request)
    {
        $count = $request->user()
            ->notifications()
            ->where('is_read', false)
            ->count();

        return $this->successResponse(['count' => $count]);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return $this->errorResponse('لست مخولاً للوصول إلى هذا الإشعار', 403);
        }

        $notification->update(['is_read' => true]);

        return $this->successResponse(new NotificationResource($notification), 'تم تحديد الإشعار كمقروء');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->successResponse(null, 'تم تحديد جميع الإشعارات كمقروءة');
    }
}
