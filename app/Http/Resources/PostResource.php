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
        return [

            'data' => match ($this->type) {
                'work' => new WorkPostResource($this),
                'project' => new ProjectPostResource($this),
                'new' => new NewPostResource($this),
                'team' => new TeamPostResource($this),
                'question' => new QuestionPostResource($this),
                default    => null,
            },
        ];
    }
}
