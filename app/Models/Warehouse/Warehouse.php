<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    public function getWareHouseName($id)
    {
        $warehouse = Warehouse::where('warehouse_id',$id)->get();

        if (isset($warehouse[0])) {
            return $warehouse[0]->warehouse_name;
        }
    }
}
