<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketPriorityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->when($this->id, $this->id),
            'name' => $this->when($this->name, $this->name),
            'color' => $this->when($this->color, $this->color),
            'createdAt' => $this->when($this->created_at, $this->created_at),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
            'ticketsCount' => $this->when($this->tickets_count, $this->tickets_count),
        ];
    }
}
