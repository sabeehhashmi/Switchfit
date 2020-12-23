<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Day;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * @Description get user by id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $user = User::with('categories','reviews','ratings')->find((int)$request->user_id);
        $availibities = Day::with(['availability'=> function ($query)  use ($user) {
                $query->whereuser_id($user->id);

            }])->get();
        $data['user'] = $user;
        $ratings = $user->reviews->sum('stars');
        $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
        $data['user']['average_rating'] = $ratings;
        $data['user']['total_reviews'] = $user->reviews->count();
        $data['user']['availibities'] = $availibities;
        $this->setResponse('success', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description update user profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function updateUserProfile(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $file = $request->file;
        $dirPath = 'assets/uploads/trainers/';
        $fileUrl = null;
        if ($file) {
            $filename = Str::random(40) . '.png';
            $imageInfo = explode(";base64,", $file);
            $image = str_replace(' ', '+', $imageInfo[1]);
            file_put_contents(public_path($dirPath . $filename), base64_decode($image));
            $fileUrl = $dirPath . $filename;

            $user = User::find((int)$request->user_id);
            if ($user->avatar) {
                deleteFile($user->avatar);
            }
            $user->update([
                'avatar' => $fileUrl,
            ]);
        }

        User::whereId((int)$request->user_id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth ?? null,
            'postal_code' => $request->postal_code ?? null,
            'gender' => $request->gender ?? null,
            'phone' => $request->phone ?? null,
            'emergency_name' => $request->emergency_name ?? null,
            'emergency_phone' => $request->emergency_phone ?? null,
        ]);

        $data['user'] = User::find((int)$request->user_id);
        $this->setResponse('Profile has been updated.', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }
}
