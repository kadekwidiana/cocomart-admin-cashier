<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'oxyItemMasterId' => $this->oxy_item_master_id,
            'oxyItemMasterCategoryId' => $this->oxy_category_id,
            'oxyItemMasterSubCategoryId' => $this->oxy_sub_category_id,
            'oxyItemMasterCode' => $this->oxy_code,
            'oxyItemMasterBarcode' => $this->oxy_barcode,
            'oxyItemMasterName' => $this->oxy_name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal,
        ];
    }
}
