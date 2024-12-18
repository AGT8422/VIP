<?php

namespace App\Imports;

use App\Models\Product; 
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ProductImage implements ToCollection
{

    public $data;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $row)
    {
         $this->data = $row;
    }
    
}
