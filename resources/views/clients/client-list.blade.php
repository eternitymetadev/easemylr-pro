@extends('layouts.main')
@section('content')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->


    <div class="layout-px-spacing">
        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Client List</h2>

            <div class="d-flex align-items-center" style="gap: 1rem;">
                <a href="{{'clients/create'}}"
                   class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="feather feather-plus">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Client
                </a>
                <a href="{{'reginal-clients'}}"
                   class="btn btn-primary">
                    Regional Client
                </a>
            </div>
        </div>


        <div class="widget-content widget-content-area br-6">
            <div class="table-responsive mb-4 mt-4">
                @csrf
                <table id="clienttable" class="table table-hover get-datatable" style="width:100%">
                    <thead>
                    <tr>
                        <th style="width: 100px">Sr No.</th>
                        <th>Name</th>
                        <th style="text-align: center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($clients) > 0)
                        @foreach ($clients as $key => $value)
                            <tr>
                                <td style="width: 100px">{{ ++$i }}</td>
                                <td>{{ ucwords($value->client_name ?? "-")}}</td>
                                <td style="text-align: center">
                                    <a class="text-center edit editIcon mx-auto"
                                       href="{{url($prefix.'/clients/'.Crypt::encrypt($value->id).'/edit')}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round"
                                             class="feather feather-edit">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
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
