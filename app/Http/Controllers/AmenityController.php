<?php

namespace App\Http\Controllers;

use App\Amenity;
use App\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AmenityController extends Controller
{
    /**
     * AmenityController constructor.
     * @Author Khuram Qadeer.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'is_role:super_admin']);
    }

    /**
     * @Description Store amenity
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:amenities|max:50|regex:/^[\pL\s\-]+$/u',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $fileUrl = '';
        $file = $request->file('icon');
        if ($file) {
            $filename = str_replace(' ', '_', Str::random(10) . $file->getClientOriginalName());
            //Move Uploaded File
            $dirPath = 'assets/uploads/icons/';
            $fileUrl = $dirPath . $filename;
            $file->move($dirPath, $filename);
        }

        Amenity::create([
            'name' => $request->name,
            'icon' => $fileUrl ?? null,
        ]);

        Session::flash('alert-success', 'Amenity has been Created.');
        return redirect(route('amenity.list'));
    }

    /**
     * @Description Delete amenity
     * @param $id
     * @Author Khuram Qadeer.
     */
    public function delete($id)
    {
        Amenity::deleteById($id);
        Session::flash('alert-danger', 'Amenity has been deleted.');
        return redirect(route('amenity.list'));
    }

    /**
     * @Description Listing of amenities
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function list()
    {
        $amenities = Amenity::orderBy('id', 'DESC')->get();
        return view('amenities.list', compact('amenities'));
    }
}
