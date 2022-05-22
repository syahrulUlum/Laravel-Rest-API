<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Survivor;
use Exception;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class SurvivorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $survivor = Survivor::with('inventory')->get();

        return $survivor;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'inventory' => 'required',
            'inventory.water' => 'required',
            'inventory.food' => 'required',
            'inventory.medication' => 'required',
            'inventory.ammunition' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }
        $faker = Factory::create();
        $faker = Factory::create();
        $last_location = $request->last_location;
        // cek apakah last_location terisi
        if (!$last_location) {
            // membuat lokasi secara acak
            $last_location = $faker->latitude(-100, 100) . "," . $faker->latitude(-100, 100);
        }

        // pengecekan gender
        if ($request->gender != "M" && $request->gender != "F") {
            return response("gender field must be filled with M or F", 400);
        }

        //cek is_infected 0 or 1
        if ($request->is_infected == 0 || $request->is_infected == 1) {
            $is_infected = $request->is_infected;
        }
        // cek is_infected true
        if ($request->is_infected == true || $request->is_infected == "true") {
            $is_infected = 1;
        }
        // cek is_infected false
        if ($request->is_infected == false || $request->is_infected == "false") {
            $is_infected = 0;
        } else {
            return response("is_infected must be filled with the number 0 or 1, it can also be filled with true or false", 400);
        }

        $data = [
            'name' => $request->name,
            'age' => $request->age,
            'gender' => $request->gender,
            'last_location' => $last_location,
            'is_infected' => $is_infected
        ];

        $survivor = Survivor::create($data);

        $inventory = [
            'survivor_id' => $survivor->id,
            'water' => $request->inventory['water'],
            'food' => $request->inventory['food'],
            'medication' => $request->inventory['medication'],
            'ammunition' => $request->inventory['ammunition'],
        ];

        try {
            Inventory::create($inventory);
            return response()->json("data added successfully", Response::HTTP_CREATED);
        } catch (Exception $e) {
            Survivor::where('id', $survivor->id)->delete();
            return response()->json("data failed to add", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Survivor  $survivor
     * @return \Illuminate\Http\Response
     */
    public function update(Survivor $survivor)
    {
        $faker = Factory::create();
        $data = [
            'last_location' => $faker->latitude(-100, 100) . "," . $faker->latitude(-100, 100)
        ];

        $survivor->update($data);
        return response()->json("Location has been updated", Response::HTTP_ACCEPTED);
    }

    public function reported($survivor)
    {
        Survivor::where('id', $survivor)->increment('reported', 1);
        $result = Survivor::select('reported')->where('id', $survivor)->first();

        if ($result->reported >= 3) {
            Survivor::where('id', $survivor)->update(['is_infected' =>   1]);
            return response()->json("Survivor have been reported & This survivor has been infected ", Response::HTTP_ACCEPTED);
        }
        return response()->json("Survivor have been reported", Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survivor  $survivor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survivor $survivor)
    {
        $survivor->delete();
        return response()->json("Data has been Deleted", Response::HTTP_OK);
    }
}
