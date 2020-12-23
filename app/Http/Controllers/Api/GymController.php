<?php

namespace App\Http\Controllers\Api;

use App\FavouriteGym;
use App\Gym;
use App\Http\Controllers\Controller;
use App\Pass;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GymController extends Controller
{
    use ApiResponse;

    /**
     * @Description search Gym
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function searchGyms(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $data = [];
        $lat = (double)$request->lat;
        $lng = (double)$request->lng;
        $radius = $request->radius ? (int)$request->radius : 5000;
        $facilitiesIds = $request->facility_ids ? explode(',', $request->facility_ids) : null;
        $amenitiesIds = $request->amenity_ids ? explode(',', $request->amenity_ids) : null;
        $searchKey = $request->search_key ?? null;
        $pagination = (int)$request->pagination ?? 0;

//        get by location
        $gymsData = getNearByData('gyms', 'lat', 'lng', $lat, $lng, $radius);

        $filtered_data = []; 

//    search keyword into gyms
        if ($searchKey && $gymsData) {
            foreach ($gymsData as $gymsDatum) {
                if (Str::contains(strtolower($gymsDatum->name), strtolower($searchKey))
                    || Str::contains(strtolower($gymsDatum->about), strtolower($searchKey))
                    || Str::contains(strtolower($gymsDatum->postal_code), strtolower($searchKey))
                    || Str::contains(strtolower($gymsDatum->address), strtolower($searchKey))
                    || Str::contains(strtolower($gymsDatum->country), strtolower($searchKey))
                    || Str::contains(strtolower($gymsDatum->state), strtolower($searchKey))
                    || Str::contains(strtolower($gymsDatum->city), strtolower($searchKey))
                ) {

                    array_push($data, $gymsDatum);
            }
        }
    } else if ($gymsData) {
        $data = $gymsData;
    }
        //return $data;
//   facilities search
    if ($facilitiesIds && $data) {
        foreach ($data as $gymsDatum) {
            foreach ($facilitiesIds as $facilityId) {

                if (\App\FacilityLink::checkGymHaveFacility((int)$facilityId, (int)$gymsDatum->id)) {
                    if (!in_array($gymsDatum, $filtered_data)) {
                        array_push($filtered_data, $gymsDatum);
                    }
                }
            }
        }
    }

//   amenities search
    if ($amenitiesIds && $gymsData) {        
        foreach ($gymsData as $gymsDatum) {
            foreach ($amenitiesIds as $amenityId) {
                if (\App\AmenityLink::checkGymHaveAmenity((int)$amenityId, (int)$gymsDatum->id)) {
                    if (!in_array($gymsDatum, $filtered_data)) {
                        array_push($filtered_data, $gymsDatum);
                    }
                }
            }
        }
    }
    if($filtered_data){
        $data = $filtered_data;
    }
    if(!empty($data)){
        $not_deleted = [];
        foreach($data as $sdata){

            if($sdata->is_deleted ==  0){
                array_push($not_deleted, $sdata);
            }

        }
       $data = $not_deleted;
    }
    $res = [];
    if ($data) {
            // convert into basic info Array format
        $res = convertGymBasicInfoArr($data);
            //        sort asc by distance
        $res = collect($res)/*->sortBy('distance')*/;

    }
//       make pagination
    if ($pagination) {
        $res = makePaginate(collect($res), 15);
        $data_res = [];
        $data_res['total'] = $res->total();
        $data_res['current_page'] = $res->toArray()['current_page'];
        $data_res['next_page_url'] = $res->nextPageUrl();
        $data_res['prev_page_url'] = $res->previousPageUrl();
        $data_res['per_page'] = $res->perPage();
        $data_res['first_page_url'] = $res->toArray()['first_page_url'];
        $data_res['last_page_url'] = $res->toArray()['last_page_url'];
        $data_res['to'] = $res->toArray()['to'];
        $data_res['from'] = $res->toArray()['from'];
            // get only data array from pagination array
        $d = [];
        if ($res) {
            foreach ($res as $re) {
                array_push($d, $re);
            }
            $data_res['data'] = $d;
        }
        $res = $data_res;
    }
    $this->setResponse('success', 1, 200, $res);
    return response()->json($this->response, $this->status);
}

    /**
     * @Description Get Gym Detail by gym_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getGym(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'gym_id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $res = Gym::getByGymId((int)$request->gym_id);
        $res['favorite'] = FavouriteGym::checkFavorite((int)$request->gym_id, (int)$request->user_id);
        $this->setResponse('success', 1, 200, $res);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description make Gym Favourite
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function makeFavGym(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'gym_id' => 'required',
            'fav' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        FavouriteGym::updateOrCreate([
            'user_id' => (int)$request->user_id,
            'gym_id' => (int)$request->gym_id,
        ], [
            'fav' => (int)$request->fav,
        ]);
        $this->setResponse('success', 1, 200, []);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description Get Favourite Gyms
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getFavGym(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $res = FavouriteGym::getFavouriteGyms((int)$request->user_id, true);
        $this->setResponse('success', 1, 200, $res);
        return response()->json($this->response, $this->status);
    }


}
