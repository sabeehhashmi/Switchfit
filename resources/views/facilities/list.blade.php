@extends('layouts.app')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Facilities</li>
    </ol>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="page-title-box">
                        <h4 class="page-title">All Facilities </h4>
                    </div>
                </div>
            </div>
            <form action="{{route('facility.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="input-group">
                            <label>Facility Name</label>
                            <input id="name" type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder=""
                                   value="{{ old('name') }}" required autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4" style="margin-top: 35px;">
                        <div class="custom-file">
                            <input type="file"
                                   accept="image/*"
                                   name="icon" class="custom-file-input @error('icon') is-invalid @enderror"
                                   id="icon"
                            >
                            <label class="custom-file-label" for="customFile">Choose Icon</label>
                            @error('icon')
                            <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-2" style="margin-top: 35px;">
                        <div class="text-right">
                            <button type="submit" class="btn btn-md btn-add-gym">Add Facility</button>
                        </div>
                    </div>
                </div>
            </form>
            @if($facilities)
                <div class="row">
                    <div class="col-12">
                        <div class="card-box table-responsive">
                            <table id="datatable" class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Icon</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($facilities as $facility)
                                    <tr>
                                        <td>{{$facility->id}}</td>
                                        <td>{{$facility->name}}</td>
                                        <td><img class="img-icon" src="{{asset($facility->icon)}}" alt="icon"></td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="#"
                                                   onclick="questionNotification('Confirmation','Are You Sure ? You want to delete facility?','{{route('facility.delete',$facility->id)}}')">
                                                    <i class="fa fa-trash" aria-hidden="true"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                @include('includes.not_found_alert',['message'=>'No Found Any Facility'])
            @endif
        </div>
        <!-- container -->
    </div>

@endsection
