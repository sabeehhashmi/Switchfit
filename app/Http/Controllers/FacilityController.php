<?php

namespace App\Http\Controllers;

use App\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FacilityController extends Controller
{
    /**
     * facilityController constructor.
     * @Author Khuram Qadeer.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'is_role:super_admin']);
    }

    /**
     * @Description Store facility
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:facilities|max:50|regex:/^[\pL\s\-]+$/u',
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

        Facility::create([
            'name' => $request->name,
            'icon' => $fileUrl ?? null,
        ]);

        Session::flash('alert-success', 'Facility has been Created.');
        return redirect(route('facility.list'));
    }

    /**
     * @Description Delete facility
     * @param $id
     * @Author Khuram Qadeer.
     */
    public function delete($id)
    {
        Facility::deleteById($id);
        Session::flash('alert-danger', 'facility has been deleted.');
        return redirect(route('facility.list'));
    }

    /**
     * @Description Listing of facilities
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function list()
    {
        $facilities = Facility::orderBy('id', 'DESC')->get();
        return view('facilities.list', compact('facilities'));
    }

}
