@extends('layouts.app')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{route('faqs.list')}}">FAQ's</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="page-title-box">
                        <h4 class="page-title">Add FAQ's </h4>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('faqs.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="input-group">
                            <label>Question</label>
                            <input id="name" type="text" name="question"
                                   class="form-control @error('question') is-invalid @enderror"
                                   placeholder=""
                                   value="{{ old('question') }}" required autofocus>
                            @error('question')
                            <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                        <div class="input-group">
                            <label>Answer</label>
                            <textarea class="form-control @error('answer') is-invalid @enderror"
                                      name="answer">{{ old('answer') }}</textarea>
                            @error('answer')
                            <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-md primay-btn inline-block">Save</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- container -->
    </div>

@endsection
