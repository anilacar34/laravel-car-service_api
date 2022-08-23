<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            'url'                         => $this->url,
            'brand'                       => $this->brand->name,
            'model'                       => $this->model,
            'year'                        => CarModelYearResource::collection($this->whenLoaded('years'))->values(),
            'option'                      => $this->option,
            'engine_cylinders'            => $this->engine_cylinders,
            'engine_displacement'         => $this->engine_displacement,
            'engine_power'                => $this->engine_power,
            'engine_torque'               => $this->engine_torque,
            'engine_fuel_system'          => $this->engine_fuel_system,
            'engine_fuel'                 => $this->engine_fuel,
            'engine_c2o'                  => $this->engine_c2o,
            'performance_top_speed'       => $this->performance_top_speed,
            'performance_acceleration'    => $this->performance_acceleration,
            'fuel_economy_city'           => $this->fuel_economy_city,
            'fuel_economy_highway'        => $this->fuel_economy_highway,
            'fuel_economy_combined'       => $this->fuel_economy_combined,
            'transmission_drive_type'     => $this->transmission_drive_type,
            'transmission_gearbox'        => $this->transmission_gearbox,
            'brakes_front'                => $this->brakes_front,
            'brakes_rear'                 => $this->brakes_rear,
            'tires_size'                  => $this->tires_size,
            'dimensions_length'           => $this->dimensions_length,
            'dimensions_width'            => $this->dimensions_width,
            'dimensions_height'           => $this->dimensions_width,
            'dimensions_front_rear_track' => $this->dimensions_front_rear_track,
            'dimensions_wheelbase'        => $this->dimensions_wheelbase,
            'dimensions_ground_clearance' => $this->dimensions_ground_clearance,
            'dimensions_cargo_volume'     => $this->dimensions_cargo_volume,
            'dimensions_cd'               => $this->dimensions_cd,
            'weight_unladen'              => $this->weight_unladen,
            'weight_limit'                => $this->weight_limit,
        ];
    }
}
