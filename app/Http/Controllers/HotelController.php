<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    private $hotelModel;

    public function __construct(Hotel $hotelModel)
    {
        $this->hotelModel = $hotelModel;
    }

    public function getValuesFromHotelMaster(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'country_name' => 'sometimes|required|string',
            'city' => 'sometimes|required|string|min:3',
            'grid_number' => 'sometimes|required|regex:/^\s*\d+(?:_\d+)*\s*$/|min:3',
            'unique_id' => 'sometimes|required|regex:/^\d+$/',
            'name' => 'sometimes|required|string',
            'validation' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $countryName = $request->input('country_name');
        $city = $request->input('city');
        $gridNumber = $request->input('grid_number');
        $uniqueId = $request->input('unique_id');
        $hotelName = $request->input('name');
        $validation = $request->input('validation');
        $supplier_id = $request->input('supplier_id');
        $flag = $request->input('flag');

        // return $flag;
        if($flag != "fromRef"){
        $dataFromHotelMaster = $this->hotelModel->getValuesFiltered(
            $countryName,
            $city,
            $gridNumber,
            $uniqueId,
            $hotelName,
            $validation,
        );
    }else{
        if($uniqueId || $supplier_id){
            $dataFromHotelMaster = $this->hotelModel->getHotelDbByref($uniqueId, $supplier_id);
        }
    }

        return response()->json($dataFromHotelMaster);
    }

    public function UpdateValueToDb(Request $request)
    {
        $data = ['message' => 'neither Id nor unique id is given'];
        $uniqueId = $request->input('unique_id');
        $ind = $request->input('ind');
        if ($ind && $uniqueId) {
            $countOfUniqueId = $this->hotelModel->getCountofValue($uniqueId);
            $data = ['message' => 'Value is already present'];
            if ($countOfUniqueId == 0) {
                $data=['message' => "Value is not updated"];
                $result = $this->hotelModel->updateUniqueId($uniqueId, $ind);
                return $result;
                if($result){
                    $data=['message' => "Successfully updated"];
                }
            }
        }

        return response()->json($data);
    }

    public function getValuesFromDbForRef(Request $request)
    {
        $data = ['message'=>'No record found'];
        $uniqueId = $request->input('unique_id');
        $supplier_id = $request->input('supplier_id');
        $data = $this->hotelModel->getHotelDbByref($uniqueId, $supplier_id);
        return response()->json($data);
    }

    public function DeleteRowsFromDb(Request $request)
    {
        $id = $request->input('ind');
        $data = $this->hotelModel->DeleteRows($id);

        return $data;
    }
}
