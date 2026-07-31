<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return [
        //     'id' => $this->id,
        //     'user_name' => $this->user_name,
        //     'email' => $this->email,
        //     'role' => $this->role,
        //     'created_at' => $this->created_at->toISOString(),
        // ];
        $data = [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'avatar' => $this->avatar_url,
            'role' => $this->role,
            'votra'     => $this->votra,
        ];


        if ($request->user()?->role === 'admin' || $request->user()?->id === $this->id) {
            $data['email'] = $this->email;
            $data['is_banned'] = $this->is_banned;
            $data['ban_reason'] = $this->ban_reason;
            $data['banned_at'] = $this->banned_at;
        }

        return $data;
    }
}
