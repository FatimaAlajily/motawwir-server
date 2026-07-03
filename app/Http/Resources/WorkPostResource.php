<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkPostResource extends JsonResource
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
            'content' => $this->content,
            'skill' => $this->skill,
            'type' => $this->type,
            'created_at' => $this->created_at->toISOString(),

        'work' => [
            'location' => $this->work?->location ,
            'salary_range' => $this->work?->salary_range ,
            'work_place' => $this->work?->work_place ,
            'contact' => $this->work?->contact ,
            'hours' => $this->work?->hours,
            'user' => new PostUserResource($this->whenLoaded('user')),
            'votes' => new VoteResource($this),
        ],
    ];
        
    }
}
