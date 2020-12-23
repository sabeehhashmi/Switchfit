<?php

namespace App\Http\Controllers\Api;

use App\Amenity;
use App\Category;
use App\Facility;
use App\Faqs;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\User;
use Illuminate\Http\Request;

class HelperController extends Controller
{
    use ApiResponse;

    /**
     * @Description Get all lists data like (facilities,amenities)
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getLists()
    {
        $data = [];
        $data['facilities'] = Facility::orderByDESC('id')->get();
        $data['amenities'] = Amenity::orderByDESC('id')->get();
        $data['categories'] = Category::orderByDESC('id')->get();
        $this->setResponse('success', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description get all Faq's
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getFaqs()
    {
        $data = [];
        $data['faqs'] = Faqs::orderByDESC('id')->get();
        $this->setResponse('success', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description get all user data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getUserData(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $data = User::getUserData((int)$request->user_id);

        $this->setResponse('success', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

}
