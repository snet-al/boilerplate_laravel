<?php

namespace App\Http\Resources\ApiApp;

use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExampleListResource extends JsonResource
{
    use ApiTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->resourceFields($request);
    }
}
