@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="page-header">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Consignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Consignment List</a></li>
                    </ol>
                </nav>
            </div>
            <div class="widget-content widget-content-area br-6">
                <div class="mb-4 mt-4">
                    <div class="btn-group relative">
                        <?php  $authuser = Auth::user(); 
                        if($authuser->role_id != 6 && $authuser->role_id != 7){ ?>
                            <a href="{{'consignments/create'}}" class="btn btn-primary pull-right" style="font-size: 13px; padding: 6px 0px;">Create Consignment</a>
                        <?php } ?>
                    </div>

                    @csrf
                    <div class="table-responsive">
                    @include('consignments.consignment-list-ajax')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('models.delete-user')
@include('models.common-confirm')
@include('models.manual-updatrLR')
@endsection