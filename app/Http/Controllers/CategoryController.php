<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * CategoryController constructor.
     * @Author Khuram Qadeer.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'is_role:super_admin']);
    }

    /**
     * @Description Store category
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories|max:50|regex:/^[\pL\s\-]+$/u',
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
        $category = new Category;
        $category->name = $request->name;

        if($fileUrl){
            
           $category->icon = $fileUrl;
       }
       $category->save();
       Session::flash('alert-success', 'Category has been Created.');
       return redirect(route('category.list'));
   }

    /**
     * @Description Delete category
     * @param $id
     * @Author Khuram Qadeer.
     */
    public function delete($id)
    {
        Category::deleteById($id);
        Session::flash('alert-danger', 'Category has been deleted.');
        return redirect(route('category.list'));
    }

    /**
     * @Description Listing of categories
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function list()
    {
        $categories = Category::orderBy('id', 'DESC')->get();
        return view('categories.list', compact('categories'));
    }

}
