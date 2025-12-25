<?php

namespace App\Http\Resources\Reward;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "code"=> $this->code,
            "name"=> $this->name,
            "description"=> $this->description,
            "points"=> $this->points,
            "createdAt"=> $this->created_at->Format('Y-m-d'),
            "logoFile" => $this->whenLoaded('logoFile', function () {
                return [
                    "id" => $this->logoFile->id,
                    "file_path" => $this->logoFile->file_path,
                ];
            }),
        ];
    }
}
