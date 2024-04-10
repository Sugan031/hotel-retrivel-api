<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Hotel extends Model
{
    use HasFactory;

    protected $table = "sample_hotel_data";
    // public function getValues() {
    //         return self::paginate(5);
    // }

    protected $guarded = [];
    // protected $updated

    public $timestamps = false;

    public function getValuesFiltered(
        $countryName = null,
        $city = null,
        $gridNumber = null,
        $uniqueId = null,
        $hotelName = null,
        $validation = null,
    ) {

        $query = self::query();
        if ($countryName) {
            $query->where('country_name', $countryName);
        }
        if ($city) {
            $query->where('city', $city);
        }
        if ($gridNumber) {
            $query->where('grid_number', $gridNumber);
        }
        if ($uniqueId) {
            $query->where('unique_id', $uniqueId);
        }

        if ($hotelName) {
            $query->where('name', $hotelName);
        }

        if ($validation) {
            $query->where('validation', $validation);
        }
        $count = ceil($query->count() / config('common.paginationCount'));

        $data = $query->orderBy('unique_id', 'ASC')->paginate(config('common.paginationCount'));

        $columns = Schema::getColumnListing("sample_hotel_data");

        $result = ['columns'=>$columns,'count' => $count, 'data' => $data];

        return $result;
    }

    public function getCountofValue($uniqueId){
        $existingCount = self::where('unique_id', $uniqueId)->count();
        return $existingCount;
    }
    public function updateUniqueId($uniqueId, $id)
    {
            if ($uniqueId == "n" || $uniqueId == "N") {
                $maxValue = self::max('unique_id');
                $result = self::where('ind', $id)->update(['unique_id' => $maxValue+1]);
            } else {
                $result = self::where('ind', $id)->update(['unique_id' => $uniqueId]);
            }
        return $result;
    }


    public function getHotelDbByref($uniqueId = null, $supplierId = null)
    {
        $baseData=null;
        $mappedData=[];
        $baseQuery = self::query();
        $mappedQuery = self::query();
        if ($uniqueId) {
            $baseQuery->where('unique_id', $uniqueId);
            $mappedQuery->where('unique_id', $uniqueId);
        }
        if ($supplierId) {
            $baseQuery->where('primary_id',$supplierId);
           $mappedQuery->where('primary_id',$supplierId);
        }
        if($uniqueId || $supplierId){
            $mappedData = $mappedQuery->where('mapping', 'Mapped')->get();
            $baseData = $baseQuery->where('mapping', 'Base')->first();
        }

        $columns = Schema::getColumnListing("sample_hotel_data");

        $result = ['baseData' => $baseData, 'mappedData' => $mappedData,'columns'=>$columns];


        return $result;
    }

   public function DeleteRows($id){
    $message = ['status' => 0, 'message' => "Value is not deleted"];
    $result = self::where('ind',$id)->update(['isActive',0]);

    if($result){
        $message = ['status' => 1, 'message' => "Successfully deleted"];
    }

    return $message;

   }
}
