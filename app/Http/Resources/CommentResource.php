<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'user' => [
                'id' => $this->user->id,
                'user_name' => $this->user->user_name,
                'avatar' => $this->user->avatar,
            ],



            'votes' => [
                'upvotes' => $this->votes->where('custom', 'upvote')->count(),
                'downvotes' => $this->votes->where('custom', 'downvote')->count(),
                'ai' => $this->votes->where('custom', 'ai')->count(),
            ],
        ];
    }
}
