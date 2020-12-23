<?php

namespace App\Http\Controllers;

use App\Faqs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FaqsController extends Controller
{
    /**
     * FaqsController constructor.
     * @Author Khuram Qadeer.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * @Description Create faqs template
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function create()
    {
        return view('faqs.create');
    }

    /**
     * @Description Store faqs
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Faqs::create([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        Session::flash('alert-success', 'Faqs has been Created.');
        return redirect(route('faqs.list'));
    }

    /**
     * @Description Show view page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function show($id)
    {
        dd($id);
        $gym = Faqs::getByFaqsId($id);
        return view('faqs.show', compact('gym'));
    }

    /**
     * @Description Edit gym
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function edit($id)
    {
        $faq = Faqs::find($id);
        return view('faqs.edit', compact('faq'));
    }

    /**
     * @Description update gym
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Faqs::find((int)$id)->update([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        Session::flash('alert-success', 'Faqs has been updated.');
        return redirect(route('faqs.list'));
    }

    /**
     * @Description Delete faqs
     * @param $id
     * @Author Khuram Qadeer.
     */
    public function delete($id)
    {
        Faqs::find((int)$id)->delete();
        Session::flash('alert-danger', 'Faqs has been deleted.');
        return redirect(route('faqs.list'));
    }

    /**
     * @Description Listing of Faqs
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function list()
    {
        $faqs = Faqs::orderBy('id', 'DESC')->get();
        return view('faqs.list', compact('faqs'));
    }

}
