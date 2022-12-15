@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->

    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Regional Client List</h2>
        </div>


        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4">
                @csrf
                <table id="clienttable" class="table table-hover get-datatable" style="width:100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Location Id</th>
                        <th style="text-align: center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($regclients)>0)
                        @foreach ($regclients as $key => $value)
                            <tr>
                                <td>{{ $value->id ?? "-" }}</td>
                                <td>
                                    <a href="{{url($prefix.'/'.$segment.'/add-regclient-detail/'.Crypt::encrypt($value->id))}}">
                                        {{ ucwords($value->name ?? "-")}}
                                    </a>
                                </td>
                                <td>{{$value->location_id ?? "-"}}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a class="edit editIcon"
                                           href="{{url($prefix.'/regclient-detail/'.Crypt::encrypt($value->id).'/edit')}}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-edit">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>
                                        <a class="view viewIcon"
                                           href="{{url($prefix.'/'.$segment.'/view-regclient-detail/'.Crypt::encrypt($value->id))}}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>

            </div>
        </div>

    </div>
@endsection
