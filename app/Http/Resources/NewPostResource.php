<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewPostResource extends JsonResource
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
            'title' => $this->title,
            'primary_link' => $this->primary_link,
            'file' => $this->file_url,
            'type' => $this->type,
            'created_at' => $this->created_at->toISOString(),
            'user' => new PostUserResource($this->whenLoaded('user')),
            'votes' => new VoteResource($this),
        ];
    }
}
