<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id'                          => $this->id,
            'brand'                       => $this->carModel->car->brand->name,
            'year'                        => $this->carModel->year,
            'car'                         => $this->carModel->car,
            'carService'                 => new CarServiceResource($this->whenLoaded('carService')),
            'transaction'                => new TransactionHistoryResource($this->whenLoaded('transaction')),
            'status'                     => $this->status,
            'created_at'                 => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
