<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuccessResource extends JsonResource
{

    /**
     * SuccessResource constructor.
     * @param array $resource
     * @param null $message
     */
    public function __construct(mixed $resource , protected $message = NULL)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->resource,
            'message' => $this->message ,
        ];
    }
}
