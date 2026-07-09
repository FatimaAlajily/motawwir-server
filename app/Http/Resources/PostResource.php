<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return match ($this->type) {
            'work' => new WorkPostResource($this)->toArray($request),
            'project' => new ProjectPostResource($this)->toArray($request),
            'new' => new NewPostResource($this)->toArray($request),
            'team' => new TeamPostResource($this)->toArray($request),
            'question' => new QuestionPostResource($this)->toArray($request),
            default    => null,
        };
    }
}
