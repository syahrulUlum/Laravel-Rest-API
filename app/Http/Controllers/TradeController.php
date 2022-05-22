<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Survivor;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class TradeController extends Controller
{
    public function __construct()
    {
        // $trade_success = Survivor::with('trade')->get();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $survivors = Survivor::with('trade', 'inventory')->get()->whereNotNull('trade');
        // trade water, food, medication, ammunition
        $id = 0;
        $trade_items = [0, 0, 0, 0];
        $own_items = [0, 0, 0, 0];
        // check survivor data
        foreach ($survivors as $survivor) {
            $trade_items[0] = $survivor->trade['req_water'];
            $trade_items[1] = $survivor->trade['req_food'];
            $trade_items[2] = $survivor->trade['req_medication'];
            $trade_items[3] = $survivor->trade['req_ammunition'];

            $trade_items[0] > 0 ? '' : $own_items[0] = $survivor->inventory['water'];
            $trade_items[1] > 0 ? '' : $own_items[1] = $survivor->inventory['food'];
            $trade_items[2] > 0 ? '' : $own_items[2] = $survivor->inventory['medication'];
            $trade_items[3] > 0 ? '' : $own_items[3] = $survivor->inventory['ammunition'];

            $other_trade_items = [0, 0, 0, 0];
            $other_items = [0, 0, 0, 0];
            $id = $survivor->trade['survivor_id'];

            //check other survivor data
            foreach ($survivors as $survivor_trade) {
                // check survivor data is not the same as other survivor data
                if ($id != $survivor_trade->trade['survivor_id']) {
                    $other_trade_items[0] = $survivor_trade->trade['req_water'];
                    $other_trade_items[1] = $survivor_trade->trade['req_food'];
                    $other_trade_items[2] = $survivor_trade->trade['req_medication'];
                    $other_trade_items[3] = $survivor_trade->trade['req_ammunition'];

                    $other_items[0] > 0 ? '' : $other_items[0] = $survivor_trade->inventory['water'];
                    $other_items[1] > 0 ? '' : $other_items[1] = $survivor_trade->inventory['food'];
                    $other_items[2] > 0 ? '' : $other_items[2] = $survivor_trade->inventory['medication'];
                    $other_items[3] > 0 ? '' : $other_items[3] = $survivor_trade->inventory['ammunition'];

                    $id_inv_trader = $survivor_trade->trade['survivor_id'];
                    $data_inventory = "";
                    $data_trade = "";
                    foreach ($trade_items as $key => $trade) {
                        if ($other_trade_items[$key] == 0 && $trade_items[$key] > 0) {
                            switch ($key) {
                                case 0:
                                    $data_inventory = "water";
                                    $data_trade = "req_water";
                                    break;
                                case 1:
                                    $data_inventory = "food";
                                    $data_trade = "req_food";
                                    break;
                                case 2:
                                    $data_inventory = "medication";
                                    $data_trade = "req_medication";
                                    break;
                                case 3:
                                    $data_inventory = "ammunition";
                                    $data_trade = "req_ammunition";
                                    break;
                            }

                            if ($other_items[$key] > $trade_items[$key]) {
                                $other_items[$key] -= $trade_items[$key];
                                Trade::where('survivor_id', $id)->update([$data_trade => 0]);
                                Inventory::where('survivor_id', $id_inv_trader)->update([$data_inventory => $other_items[$key]]);
                            } else if ($other_items[$key] < $trade_items[$key]) {
                                foreach ($other_items as $key2 => $other_item) {
                                    # code...
                                }
                            }
                        }
                    }
                }
            }
        }
        return $survivors;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $survivor = Survivor::with('inventory')->where('id', $request->survivor_id)->first();
        $inventory = $survivor->inventory;

        $validator = Validator::make($request->all(), [
            'survivor_id' => 'required',
            'req_food' => "required|integer|min:0|max:{$inventory->food}",
            'req_water' => "required|integer|min:0|max:{$inventory->water}",
            'req_medication' => "required|integer|min:0|max:{$inventory->medication}",
            'req_ammunition' => "required|integer|min:0|max:{$inventory->ammunition}",
        ]);

        $need_points = [];
        $own_points = [];
        //check food
        $request->req_food != 0 ? array_push($need_points, $request->req_food * 3) : array_push($own_points, $inventory->food * 3);

        //check water
        $request->req_water != 0 ? array_push($need_points, $request->req_water * 4) : array_push($own_points, $inventory->water * 4);

        //check ammunition
        $request->req_ammunition != 0 ? array_push($need_points, $request->req_ammunition) : array_push($own_points, $inventory->ammunition);

        //check medication
        $request->req_medication != 0 ? array_push($need_points, $request->req_medication * 2) : array_push($own_points, $inventory->medication * 2);

        //check points
        if (array_sum($need_points) > array_sum($own_points)) {
            return response()->json("What you want is more than what you have", Response::HTTP_NOT_ACCEPTABLE);
        }

        //check if survivor is available to trade
        if (Trade::where('survivor_id', $request->survivor_id)->first()) {
            return response()->json("Survivor is still available trade", Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        Trade::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Trade  $trade
     * @return \Illuminate\Http\Response
     */
    public function show(Trade $trade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Trade  $trade
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Trade $trade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Trade  $trade
     * @return \Illuminate\Http\Response
     */
    public function destroy(Trade $trade)
    {
        //
    }
}
