<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Province;
use App\City;

use App\Http\Resources\Provinces as ProvinceResourceCollection;
use App\Http\Resources\Cities as CityResourceCollection;

class ShopController extends Controller
{
    public function provinces() {
        return new ProvinceResourceCollection(Province::get());
    }

    public function cities() {
        return new CityResourceCollection(City::get());
    }

    public function shipping(Request $request) {
        $user = Auth::user();
        $status = "error";
        $message = "";
        $data = null;
        $status_code = 200;

        if($user){
            $this->validate($request, [
                'name' => 'required',
                'address' => 'required',
                'phone' => 'required',
                'province_id' => 'required',
                'city_id' => 'required'
            ]);

            $user->name = $request->name;
            $user->address = $request->address;
            $user->phone = $request->phone;
            $user->provinde_id = $request->city_id;

            if($user->save()) {
                $status = "success";
                $message = "Belanja anda berhasil diupdate";
                $data = $user->toArray();
            }else {
                $message = "Belanja anda gagal terupdate";
            }
        }else {
            $message = "User tidak ditemukan";
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }
}
