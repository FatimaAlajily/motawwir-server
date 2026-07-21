<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'type' => $this->type,
            'is_read' => $this->is_read,
            'from_user' => new PostUserResource($this->whenLoaded('fromUser')),
            'comment' => $this->whenLoaded('comment', fn () => new SimpleCommentResource($this->comment)),
            'vote' => $this->whenLoaded('vote', fn () => new SingleVoteResource($this->vote)),
            'created_at' => $this->created_at,
        ];
    }
}