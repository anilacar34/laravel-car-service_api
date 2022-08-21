<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionHistoryResource extends JsonResource
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
            'id'             => $this->id,
            'amount'         => $this->amount,
            'balance_before' => $this->balance_before,
            'balance_after'  => $this->balance_after,
            'process_type'   => $this->process_type,
            'created_at'     => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
