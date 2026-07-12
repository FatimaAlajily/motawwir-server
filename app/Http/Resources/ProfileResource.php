<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id'       => $this->id,
            'bio'      => $this->bio,
            'phone'    => $this->phone,
            'location' => $this->location,
            'skill'    => $this->skill ?? [], 
            'github'   => $this->github,
            'gmail'    => $this->gmail,
            'domain'   => $this->domain,
            'cv'       => $this->cv ? asset('storage/' . $this->cv) : null,
            'linkedin' => $this->linkedin,
            'user'     => [
                'id'        => $this->user?->id,
                'user_name' => $this->user?->user_name,
                'role'      => $this->user?->role, 
                'avatar'    => $this->user?->avatar ? asset('storage/' . $this->user->avatar) : null,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
