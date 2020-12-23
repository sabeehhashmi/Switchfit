@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('gym.list')}}">Gyms</a></li>
    <li class="breadcrumb-item active" aria-current="page">Passes</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-9">
                <div class="page-title-box">
                    <h4 class="page-title">{{\App\Gym::find($passes[0]->gym_id)['name']}} </h4>
                </div>
                
            </div>
            <div class="col-sm-3 text-right">
                <a href="#" class="waves-effect waves-light change-gym-percentage-link  {{(isSuperAdmin())?'':' inactiveLink'}}" data-toggle="modal"
                data-target=".change-gym-percentage"
                
                >Switchfit fee: {{\App\Gym::find($passes[0]->gym_id)['percentage']}}%</a>
            </div>
        </div>

        @if($passes)
        @foreach($passes as $pass)
        <div class="gym-panel pass-panel">
            <div class="row">
                <div class="col-sm-2">
                    <div class="gym-image-holder">
                        <img src="{{asset($pass->image)}}" style="object-fit: contain;" alt="img"/>
                    </div>
                </div>
                <div class="col-sm-10">
                    <div class="gym-content-panel">
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="d-flex align-self-center">
                                    <h3>{{$pass->title}}</h3>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="d-flex action-btn text-right pull-right">
                                    <div style="margin-right: 15px;">
                                        <input type="checkbox" data-plugin="switchery"
                                        data-color="#32317d" data-size="small" data-switchery="true"
                                        style="display: none;"
                                        id="cbx-{{$pass->id}}"
                                        onchange="updatePassActive(this,'{{$pass}}')"
                                        {{$pass->active ? 'checked':''}}>
                                    </div>
                                    <a href="#" class=" waves-effect waves-light" data-toggle="modal"
                                    data-target=".pass-change-price"
                                    id="edit_{{$pass->id}}"
                                    onclick="updatePriceModal('{{$pass}}')"><i
                                    class="fa fa-pencil"
                                    aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                        <small>{{$pass->description}}</small>
                        @if($pass->price>0)
                        <div class="price-panel">
                            <strong>&#163; {{number_format((double)$pass->price,2, '.', ',')}}</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif

        <div class="clearfix"></div>
    </div>
    <!-- container -->
</div>

{{--    update price modal--}}
<div class="modal fade pass-change-price" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
style="display: none;" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered ">
    <div class="modal-content">
        <div class="modal-header" style="border: none;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                    <a href="javascript:void(0)" class=" text-center">
                        <img id="pass_img"
                        class="img-responsive img-thumbnail"
                        src="/assets/images/login-logo.png" alt="">
                    </a>
                </div>
                <div class="col-sm-4"></div>
            </div>
            <form method="post" action="{{route('passes.price.update')}}" id="update_pass">
                @csrf
                <input type="hidden" name="pass_id" id="pass_id" value="">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 text-center">
                        <h4 class="color_blue" id="pass_title" style="font-size: 25px;"></h4>
                    </div>
                    <div class="col-xs-12 col-sm-12 text-center">
                        <p id="pass_desc"></p>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                        <div class="input-group">
                            <h4 class="color_blue">Price (&#163;)</h4>
                            <input type="number" step="any" class="form-control"
                            name="price"
                            id="price"
                            min="1"
                            max="999"
                            onKeyPress="if(this.value.length==5) return false;"
                            value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" onclick="updatePassPrice()"
                        class="btn btn-md primay-btn pull-right inline-block">Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>

<div class="modal fade change-gym-percentage" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
style="display: none;" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered ">
    <div class="modal-content">
        <div class="modal-header" style="border: none;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                    <a href="javascript:void(0)" class=" text-center">
                        <img id="pass_img"
                        class="img-responsive img-thumbnail"
                        src="/assets/images/login-logo.png" alt="">
                    </a>
                </div>
                <div class="col-sm-4"></div>
            </div>
            <form method="post" action="/gym/update/gym/percentage" id="update_percentage">
                @csrf
                <input type="hidden" name="gym_id" id="gym_id" value="{{\App\Gym::find($passes[0]->gym_id)['id']}}">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 text-center">
                        <h4 class="color_blue" id="pass_title" style="font-size: 25px;">
                            {{\App\Gym::find($passes[0]->gym_id)['name']}}
                        </h4>
                    </div>
                    <div class="col-xs-12 col-sm-12 text-center">
                        <p id="pass_desc"></p>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                        <div class="input-group">
                            <h4 class="color_blue">Percentage</h4>
                            <input type="number" step="any" class="form-control"
                            name="gym_price"
                            id="price"
                            min="1"
                            max="100"
                            value="{{\App\Gym::find($passes[0]->gym_id)['percentage']}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" onclick="updateGymPercentagePrice()"
                        class="btn btn-md primay-btn pull-right inline-block">Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>

@endsection

@section('script')
<style type="text/css">
    .change-gym-percentage-link{
        color: #3c15a3 !important;
        font-size: 18px;
    }
    .inactiveLink {
   pointer-events: none;
   cursor: default;
}
</style>
<script>

        /**
         * @Description set pass data into modal inputs
         * @Author Khuram Qadeer.
         **/
         function updatePriceModal($pass) {
            var current_pass = JSON.parse($pass);
            if (current_pass) {
                $('.pass-change-price #pass_title').text(current_pass.title);
                $('.pass-change-price #pass_desc').text(current_pass.description);
                $('.pass-change-price #pass_img').attr('src', '/' + current_pass.image);
                $('.pass-change-price #pass_id').val(current_pass.id);
                $('.pass-change-price #price').val(current_pass.price > 0 ? current_pass.price : 1);
            }
        }

        /**
         * @Description Update Pass Active or de-active
         * @param el
         * @param passId
         */
         function updatePassActive(el, pass) {
            var current_pass = JSON.parse(pass);
            var active = $(el).is(':checked') ? 1 : 0;
            if (current_pass.price > 0 || active == 0) {
                $.ajax({
                    url: '/gym/pass/update/active',
                    type: "POST",
                    data: {
                        _token: getCsrfToken(),
                        active: active,
                        passId: current_pass.id
                    },
                    success: function (res) {
                        // notification('success', res.msg);
                    }
                });
            } else {
                updatePriceModal(pass);
                $('#edit_' + current_pass.id).click();
            }
        }

        /**
         * @Author Khuram Qadeer.
         */
         function updatePassPrice() {
            if ($('.pass-change-price #price').val() < 1000) {
                $('#update_pass').submit();
            } else {
                notification('success', 'Price must be less than 1000')
            }
        }
        function updateGymPercentagePrice() {
            $('#update_percentage').submit();
        }

    </script>
    @endsection
