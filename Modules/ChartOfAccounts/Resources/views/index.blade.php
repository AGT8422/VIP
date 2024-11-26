@extends('layouts.app')
@section('title','chartofaccounts')

@section('content')

<section class="content-header">
    <h1>@lang('chartofaccounts::lang.chart_view' )   </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="mb-10">
        <button onclick="getaccount(0)" >test</button>
        <button type="button" class="btn  btn-primary btn-modal" onclick="addchartaccount()">
            <i class="fa fa-plus"></i> @lang( 'chartofaccounts::lang.add_account' )</button>
    </div>


    <div class="box box-primary" >
         <div class="row">
            <div class="col-lg-2 mt-5">
                <div class="main" id="account_0">
                    @foreach ($accounts as $account)
                    <div class="parent" onclick="getaccount({{$account->id}})"  id="{{$account->id}}">
                        <span>#{{$account->code}}</span>
                        {{$account->name}}
                    </div>
                   @endforeach
                </div>
            </div>
             <div class="col-lg-9 mt-5 ">
                 <div class="account-path">this is \ test \path</div>
                 <div id="acount-chiled"></div>
             </div>


        </div>






    </div>


    {{--Model div --}}
    <div class="modal fade " tabindex="-1" role="dialog" id="modeldiv"
         aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection

@section('javascript')

 <script>
     $(document).ready(function () {

     });
     function addchartaccount() {
       $.ajax({
             url: '/chartofaccounts/addaccount',
             dataType: 'html',
             success: function(result) {
                 $('#modeldiv').html(result).modal('show');
             },
         });
     }


     $('#modeldiv').on('shown.bs.modal', function() {
         $('#modeldiv')
             .find('.select2')
             .each(function() {
                 var $p = $(this).parent();
                 $(this).select2({ dropdownParent: $p });
             });
     });

     $(document).onclick('.account',function () {
         var account_id=$(this).id;
         alert(account_id);
     });

     function getaccount(id) {
         var account_id=id;
          $.ajax({
             url: '/chartofaccounts/getaccount',
             type:'GET',
             data:{
                id:account_id
             },
             success: function(result) {
                 $('#acount-chiled').html(result);
             },
         });

     }



 </script>


@endsection
