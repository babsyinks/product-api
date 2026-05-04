<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $price
 * @property int $stock
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => (float) $this->price,
            'stock'       => $this->stock,
            'in_stock'    => $this->stock > 0,
            'created_at'  => $this->created_at->toIso8601String(),
            'updated_at'  => $this->updated_at->toIso8601String(),
            // Only expose deleted_at to authenticated users (e.g. admins)
            $this->mergeWhen($request->user() !== null, [
                'deleted_at' => $this->deleted_at?->toIso8601String(),
            ]),
        ];
    }
}
