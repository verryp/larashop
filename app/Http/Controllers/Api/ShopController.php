<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Province;
use App\City;
use Auth;
use App\Book;
use App\BookOrder;
use DB;
use App\Order;

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
        $user = Auth::guard('api')->user();
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
            $user->province_id = $request->city_id;

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

    public function couriers() {
        $couriers = [
            [
                'id' => 'jne', 
                'text' => 'JNE'
            ],
            [
                'id' => 'tiki', 
                'text' => 'TIKI'
            ],
            [
                'id' => 'pos', 
                'text' => 'POS'
            ],
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'couriers',
            'data' => $couriers
        ], 200);
    }

    public function services(Request $request) {
        $status = "error";
        $message = "";
        $data = [];

        // * validasi data
        $this->validate($request, [
            'courier' => 'required',
            'carts' => 'required',
        ]);

        $user = Auth::guard('api')->user();

        if($user) {
            // * hardcode, destinasi di jogja
            $destination = $user->city_id;
            $destination = 501;

            if($destination > 0) {
                //pake cara hardcode, boleh diganti alamatnya
                $origin = 501; // * id jogja
                $courier = $request->courier;
                $carts = $request->carts;
                $carts = json_decode($carts, true); // * transform dari json ke array

                // * validasi data belanja
                $validCart = $this->validateCarts($carts);
                $data['safe_carts'] = $validCart['safe_carts'];
                $data['total'] = $validCart['total'];
                $quantity_diff = $data['total']['quantity_before'] <> $data['total']['quantity'];

                $weight = $validCart['total']['weight'] * 1000;

                if($weight > 0) {
                    // * req courier dari api rajaonkir
                    $parameter = [
                        'origin'        => $origin,
                        'destination'   => $destination,
                        'weight'        => $weight,
                        'courier'       => $courier
                    ];

                    // * cek ongkir ke api rajaonkir by methods getServices()
                    $respon_services = $this->getServices($parameter);

                    if($respon_services['error'] == null) {
                        $services = [];
                        $response = json_decode($respon_services['response']); // trans dari json jadi array
                        $costs = $response->rajaongkir->results[0]->costs;

                        foreach($costs as $cost){
                            $service_name = $cost->service;
                            $service_cost = $cost->cost[0]->value;
                            $service_estimation = str_replace('hari', '', trim($cost->cost[0]->etd));
                            $services[] = [
                                'service' => $service_name,
                                'cost' => $service_cost,
                                'estimation' => $service_estimation,
                                'resume' => $service_name .' [ Rp. '.number_format($service_cost).', Etd: '.$cost->cost[0]->etd.' day(s) ]'
                            ];
                        }

                        // * Response
                        if(count($services) > 0) {
                            $data['services'] = $services;
                            $status = "success";
                            $message = "getting services success";
                        }else {
                            $message = "courier services unavailable";
                        }

                        // * jika jumlah stok berbeda dengan jumlah beli, tampilkan warning
                        if($quantity_diff) {
                            $status = "warning";
                            $message = "Check cart data, ".$message;
                        }
                    } else {
                        $message = "curl error #: " . $respon_services['error'];
                    }
                } else {
                    $message = "weight invalid";
                }
            } else {
                $message = "destination not set";
            }
        } else {
            $message = "User not found";
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    public function validateCarts($carts) {
        $safe_carts = []; // * buat nampung data cart biar aman

        $total = [
            'quantity_before' => 0,
            'quantity' => 0,
            'price' => 0,
            'weight' => 0,
        ];

        $idx = 0;

        // * ngeloop data state cart buat ngecek valid yang akan di kirim ke server
        foreach ($carts as $cart) {
            $id = (int)$cart['id'];
            $quantity = (int)$cart['quantity'];
            $total['quantity_before'] += $quantity;

            $book = Book::find($id);

            if($book) {
                if($book->stock > 0) {//* check stok
                    $safe_carts[$idx]['id'] = $book->id;
                    $safe_carts[$idx]['title'] = $book->title;
                    $safe_carts[$idx]['cover'] = $book->cover;
                    $safe_carts[$idx]['price'] = $book->price;
                    $safe_carts[$idx]['weight'] = $book->weight;

                    if($book->stock < $quantity) { // * kondisi jika jumlah buku yang di pesan melebihi stok
                        $quantity = (int)$book->stock; // * jumlah yang dipesan disamakan dengan stok
                    }

                    $safe_carts[$idx]['quantity'] = $quantity;

                    $total['quantity'] += $quantity; // * total jumlah pesan dihutung ulang
                    $total['price'] += $book->price * $quantity; // * total harga dihitung ulang
                    $total['weight'] += $book->weight * $quantity; // * total berat juga dihitung ulang
                    $idx++;
                }else {
                    continue;
                }
            }
        }

        return [
            'safe_carts' => $safe_carts,
            'total' => $total,
        ];
    }

    protected function getServices($data) {

        $url_cost = "https://api.rajaongkir.com/starter/cost";
        $key="5d1afc3c9a5eaf229615a9f33a2eff29";
        $postdata = http_build_query($data);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url_cost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postdata, 
            CURLOPT_HTTPHEADER => [
                "content-type: application/x-www-form-urlencoded",
                "key: ".$key
            ],
        ]);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        return [
            'error' =>  $error,
            'response' =>  $response,
        ];
    }

    public function payment(Request $request) {
        $error = 0;
        $status = "error";
        $message = "";
        $data = [];

        $user = Auth::guard('api')->user();

        if($user) {

            // * validasi data
            $this->validate($request, [
                'courier' => 'required',
                'service' => 'required',
                'carts' => 'required',
            ]);
            
            // ? menggunakan DB Transaction
            DB::beginTransaction(); // * memulai transaksi

            try {
                // * init data kurir
                $origin = 501; // * id jogja dari api rajaongkir
                $destination = $user->city_id; // * maap hardcode, kalo mau auto tambahin relasi dari user ke city

                if($destination <= 0)
                    $error++;

                $courier = $request->courier;
                $service = $request->service;
                $carts = json_decode($request->carts, true);

                // set order(create)
                $order = new Order;
                $order->user_id = $user->id;
                $order->total_price = 0;
                $order->invoice_number = date('YmdHis'); // year-month-day-hour-minutes-second
                $order->courier_service = $courier.'-'.$service;
                $order->status = 'SUBMIT';
        
                if($order->save()) {
                    $total_receipt = 0;
                    $total_weight = 0;

                    // * melakukan pengecekan kembali dalam kayak validateCarts()
                    foreach($carts as $cart) {
                        $id = (int)$cart['id'];
                        $quantity = (int)$cart['quantity'];
        
                        $book = Book::find($id);
        
                        if($book) {
                            if($book->stock >= $quantity) {
                                $total_receipt += $book->price * $quantity;
                                $total_weight += $book->weight * $quantity;
        
                                // buat detil order dari model BookOrder
                                $book_order = new BookOrder;
                                $book_order->book_id = $book->id;
                                $book_order->order_id = $order->id;
                                $book_order->quantity = $quantity;
        
                                if($book_order->save()) {
                                    // * kurangin stok
                                    $book->stock = $book->stock - $quantity;
                                    $book->save();
                                }
                            } else {
                                $error++;
                                throw new \Exception('stok kurang');
                            }
                        } else {
                            $error++;
                            throw new \Exception('buku tidak ditemukan');
                        }
                    }

                    // * cek ongkir
                    $totalPrice = 0;
                    $weight = $total_weight * 1000; // ubah ke gram, biar gampang buat kondisinya :v

                    if($weight <= 0) {
                        $error++;
                        throw new \Exception('Berat null');
                    }

                    $data = [
                        'origin' => $origin,
                        'destination' => $destination,
                        'weight' => $weight,
                        'courier' => $courier
                    ];

                    $data_cost = $this->getServices($data);

                    if($data_cost['error']) {
                        $error++;
                        throw new \Exception('Courier tidak tersedia');
                    }

                    $response = json_decode($data_cost['response']);
                    $costs = $response->rajaongkir->results[0]->costs;
                    $service_cost = 0;

                    foreach($costs as $cost) {
                        $service_name = $cost->service;

                        if($service == $service_name) {
                            $service_cost = $cost->cost[0]->value;
                            break;
                        }
                    }

                    if($service_cost <= 0) {
                        $error++;
                        throw new \Exception('Biaya Service invalid');
                    }

                    $total_price = $total_receipt + $service_cost;
                    
                    // * udpate total harga order
                    $order->total_price = $$total_price;

                    if($order->save()) {
                        if($error==0) {
                            DB::commit(); // * commit transaksi yang suda berhasil
                            $status = 'success';
                            $message = 'Transaction Success';
                            $data = [
                                'order_id' => $order->id,
                                'total_price' => $total_price,
                                'invoice_number' => $order->invoice_number
                            ];
                        } else {
                            $message = 'Error : '.$error;
                        }
                    }
                }

            }catch(\Exception $e){
                $message = $e->getMessage();
                DB::rollback(); // * jika terjadi error, maka transaksi dibatalkan
            }
        } else {
            $message = 'user tidak ditemukan';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], 200);
    }
}