@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Payout Account</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">Payout Account</h4>
                </div>
            </div>
        </div>
        <form id="post-form" method="post" action="javascript:void(0)" autocomplete="on">
            <input type="hidden" name="id" id="account_id" value="{{isset($account->id) ? $account->id: ''}}">
            <div class="row">
                <div class="col-xs-6 col-sm-6">
                    <div class="input-group">
                        <label>Account Name</label>
                        <input id="name" type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder=""
                        value="{{ isset($account->account_name) ? old('name',$account->account_name) : old('name') }}"
                        autofocus>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6">
                    <div class="input-group">
                        <label>Account Type</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                            <option
                            value="individual" {{ isset($account->account_type) && $account->account_type == 'individual'?'selected':''}}>
                            Individual
                        </option>
                        <option
                        value="company" {{isset($account->account_type) && $account->account_type == 'company'?'selected':''}}>
                        Company
                    </option>
                </select>
                @error('type')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6">
            <div class="input-group">
                <label>Routing Number</label>
                <input id="routing_number" type="text" name="routing_number"
                class="form-control @error('routing_number') is-invalid @enderror"
                placeholder=""
                value="{{ isset($account->routing_number) ? old('routing_number',$account->routing_number) : old('routing_number')}}"
                autofocus>
                @error('routing_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-xs-6 col-sm-6">
            <div class="input-group">
                <label>Social Security Number</label>
                <input id="social_security_number" type="text" name="social_security_number"
                class="form-control @error('social_security_number') is-invalid @enderror"
                placeholder=""
                value="{{ isset($account->social_security_number) ? old('social_security_number',$account->social_security_number) : old('social_security_number') }}"
                autofocus>
                @error('social_security_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6">
            <div class="input-group">
                <label>Account Number</label>
                <input id="account_number" type="text" name="account_number"
                class="form-control @error('account_number') is-invalid @enderror"
                placeholder=""
                value="{{  isset($account->account_number) ? old('account_number',$account->account_number) : old('account_number')   }}"
                autofocus>
                @error('account_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-xs-6 col-sm-6">
            <div class="input-group">
                <label>Retype Account Number</label>
                <input id="retype_account_number" type="text" name="retype_account_number"
                class="form-control @error('retype_account_number') is-invalid @enderror"
                placeholder=""
                value="{{  isset($account->account_number) ? old('retype_account_number',$account->account_number) : old('retype_account_number')  }}"
                autocomplete="false">
                @error('retype_account_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6">
            <div class="input-group">
                <label>DOB</label>
                <input id="dob" type="text" name="dob"
                class="form-control  @error('dob') is-invalid @enderror"
                placeholder=""
                value="{{ isset($account->dob) ? old('dob',$account->dob) : old('dob')  }}"
                autofocus>
                <span class="fa fa-calendar input-calendar"></span>
                @error('dob')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="input-group">
                <label>Complete address as it appears on your bank account</label>
                <input id="address" type="text" name="address"
                class="form-control @error('address') is-invalid @enderror"
                placeholder=""
                value="{{ isset($account->address) ? old('address',$account->address) : old('address')  }}"
                autofocus>
                @error('address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>

    @if(!isset($account->id))
    <div class="row">
        <div class="col-sm-6 input-group" id="file_front">
            <label>Front side of identity document (e.g passport,local ID card)</label>
            <input id="sample_input" type="hidden" name="test[image]"/>

            @error('front')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="col-sm-6 input-group" id="file_back">
            <label>Back side of identity document (e.g passport,local ID card)</label>
            <input id="sample_input_doc" type="hidden" name="test[image]"/>
            @error('back')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-sm-2">
            <button type="submit"
            onclick="saveOrUpdateBankAccount()"
            class="btn btn-md primay-btn inline-block btn-action">{{isset($account->id)?'Update':'Save'}}</button>
        </div>
        <div class="col-sm-3 loader-parent">  <div class="loader"></div> </div>
    </div>
</form>
</div>
<!-- container -->
</div>

@endsection

@section('head_script')
<style type="text/css">
    .loader-parent {
        padding: 18px;
        display: none;
    }
    .loader {
      border: 6px solid #d0cdcd; /* Light grey */
      border-top: 6px solid #3c15a3; /* Blue */
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 2s linear infinite;
  }

  @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
  }
  .modal-body .col-md-3 {
    display: none;
}
.image-field-haver {
    position: relative;
}
.progress-bar,
.progress {
    opacity: 0;
}
.cropped-image {
    width: 100%;
    margin: 0 0 30px;
}
.croped-image-div, .img-holder {
    width: 295px;
    margin-left: 31px;
    margin-top: 40px;
}
.modal-body .cropped-image {
    width: auto;
}

.modal-dialog {
    max-width: 900px;
    margin: 0.20rem auto;
}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script> 
<script src="{{ url('/') }}/assets/components/imgareaselect/scripts/jquery.imgareaselect.js"></script> 
<script src="{{ url('/') }}/assets/build/jquery.awesome-cropper.js"></script>
<script>
    $(document).ready(function () {

        $('#sample_input').awesomeCropper(
            { width: 150, height: 150, debug: true }
            );
        $('#sample_input_doc').awesomeCropper(
            { width: 150, height: 150, debug: true }
            );

    });
</script>
<link href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="Stylesheet" type="text/css"/>
{{--        <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>--}}
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script>
    $(document).ready(function () {
        $("#dob").datepicker({
            maxDate: new Date()
        });
    });
    function dataURLtoFile(dataurl, filename) {
        var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, {type:mime});
    }
        /**
         * @Description Create and update bank account and attach with stripe at backend
         * @Author Khuram Qadeer.
         */
         function saveOrUpdateBankAccount() {
            if (!$('#name').val() || !$('#routing_number').val()
                || !$('#social_security_number').val() || !$('#account_number').val()
                || !$('#retype_account_number').val() || !$('#dob').val() || !$('#address').val()) {
                notification('danger', 'All field are required');
            return false;
        }

        if ($('#account_number').val() != $('#retype_account_number').val()) {
            notification('danger', 'Retype account number must be same.');
            return false;
        }
        
        $('.btn-action').addClass('div-disable');
        var formData = new FormData();
        formData.append('_token', '{{csrf_token()}}');
        formData.append('id', $('#account_id').val());
        formData.append('name', $('#name').val());
        formData.append('type', $('#type').val());
        formData.append('routing_number', $('#routing_number').val());
        formData.append('social_security_number', $('#social_security_number').val());
        formData.append('account_number', $('#account_number').val());
        formData.append('retype_account_number', $('#retype_account_number').val());
        formData.append('dob', $('#dob').val());
        formData.append('address', $('#address').val());

        if (!$('#account_id').val()) {
            var front = $('#sample_input').val();
            var back = $('#sample_input_doc').val();

            var front_image  = dataURLtoFile(front, 'front.png');
            var back_image  = dataURLtoFile(back, 'back.png');
            formData.append('front', front_image);
            formData.append('back',back_image);
        }
        $('.loader-parent').show();
        $.ajax({
            url: "{{ route('bank_account.store_update') }}",
            type: "POST",
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            enctype: 'multipart/form-data',
            success: function (res) {
                $('.loader-parent').hide();
                window.location.href = '{{route('dashboard')}}';
            },
            error: function (res) {
                $('.btn-action').removeClass('div-disable');
                notification('danger', res.responseJSON.message);
                $('.loader-parent').hide();
            }
        });
    }

</script>
@endsection
