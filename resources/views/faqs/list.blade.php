@extends('layouts.app')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">FAQ's</li>
    </ol>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="page-title-box">
                        <h4 class="page-title">FAQ's </h4>
                    </div>
                </div>
                <div class="col">
                    <div class="text-right">
                        <a href="{{route('faqs.create')}}" class="btn btn-md btn-add-gym mt-2">Add FAQ</a>
                    </div>
                </div>
            </div>

            @if($faqs)
                @foreach($faqs as $faq)
                    <button class="accordion">{{$faq->question}}</button>
                    <div class="panel-accordion">
                        <p>{{$faq->answer}}</p>
                        <div class="d-flex action-btn">
                            <a href="{{route('faqs.edit',$faq->id)}}"><i class="fa fa-pencil"
                                                                         aria-hidden="true"></i></a>
                            <a href="#"
                               onclick="questionNotification('Confirmation','Are You Sure ? You want to delete faqs?','{{route('faqs.delete',$faq->id)}}')"><i
                                    class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                @endforeach
            @else
                @include('includes.not_found_alert',['message'=>'No Found Any Faqs'])
            @endif

        </div>
        <!-- container -->
    </div>
@endsection

@section('script')
    <script>
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function () {
                this.classList.toggle("active-accordion");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    </script>
@endsection
