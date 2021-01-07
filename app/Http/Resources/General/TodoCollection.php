<?php

namespace App\Http\Resources\General;

use Illuminate\Http\Resources\Json\JsonResource;
//use Illuminate\Http\Resources\Json\ResourceCollection;

class TodoCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'id' => $this->id,
            'user' => $this->user_id,
            'ip_address' => $this->ip_address,
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'date' => $this->date,
            'link' => route('todos.show', $this->id)
        ];
    }
}
