@extends('layouts.main')
@section('content')

    <style>
        .pageContainer {
            min-height: min(75vh, 600px);
            box-shadow: 0 0 16px -3px #83838370;
            border-radius: 12px;
        }

        .fileBlock {
            background: #e6e6e6;
            border-radius: 8px;
            width: 100%;
            padding: 2px;
            min-height: 90px;
        }

        .reportName {
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            text-align: center;
            margin-bottom: 0;
            color: #000;
        }

        .sampleLink {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: #61acff;
            color: #ffff;
            cursor: pointer;
            border-radius: 8px;
            transition: all 200ms ease-in-out;
            padding: 4px;
            font-size: 11px;
        }

        .sampleLink:hover {
            background: #278dff;
        }

        input[type=file] {
            padding: 0.5rem 1rem;
            border: 1px solid;
            border-radius: 8px;
        }

        input[type=file]:disabled {
            border: none;
        }

        .uploadButton {
            cursor: pointer;
            color: #8d8d8d;
            font-weight: 700;
            outline: 1px solid;
            padding: 4px 13px;
            border-radius: 35px;
        }

        .uploadButton:hover {
            color: #f9b600;
            box-shadow: 1px 6px 10px #83838380;
        }

        .notAllowed {
            cursor: not-allowed;
        }
    </style>

    <div class="layout-px-spacing">

        <div class="page-header layout-spacing">
            <h2 class="pageHeading">Import Data</h2>
        </div>

        <div class="pageContainer widget-content widget-content-area">
            <form class="d-flex flex-wrap align-items-center"
                  method="POST" action="{{url($prefix.'/consignees/upload_csv')}}" id="importfiles"
                  enctype="multipart/form-data">
                @csrf

                <?php
                $authuser = Auth::user();
                $fileBlockInputs = array(
                    array(
                        "name" => "Browse Consignees Sheet",
                        "inputName" => "consigneesfile",
                        "inputId" => "consigneefile",
                        "link" => "/sample-consignees",
                    ),
                    array(
                        "name" => "Browse Vehicles Sheet",
                        "inputName" => "vehiclesfile",
                        "inputId" => "vehiclefile",
                        "link" => "/sample-vehicle",
                    ),
                    array(
                        "name" => "Browse Consigners Sheet",
                        "inputName" => "consignersfile",
                        "inputId" => "consignerfile",
                        "link" => "/sample-consigner",
                    ),
                    array(
                        "name" => "Browse Driver Sheet",
                        "inputName" => "driversfile",
                        "inputId" => "driverfile",
                        "link" => "/sample-driver",
                    ),
                    array(
                        "name" => "Browse Zones Sheet",
                        "inputName" => "zonesfile",
                        "inputId" => "zonefile",
                        "link" => "/sample-zone",
                    ),
                    array(
                        "name" => "Browse Delivery Date Sheet",
                        "inputName" => "deliverydatesfile",
                        "inputId" => "deliverydatefile",
                        "link" => "/sample-deliverydate",
                    ),
                    array(
                        "name" => "Browse LR Type Changes",
                        "inputName" => "manualdeliveryfile",
                        "inputId" => "manualdeliveryfile",
                        "link" => "/sample-manualdelivery",
                    ),
                    array(
                        "name" => "Browse POD Zip Folder(Image)",
                        "inputName" => "podsfile",
                        "inputId" => "podfile",
                        "link" => "",
                    ),
                );
                ?>

                @foreach($fileBlockInputs as $fileBlockInput)
                    <div class="d-flex col-md-4 mb-3">
                        <div class="fileBlock d-flex flex-wrap justify-content-between align-items-center">
                            <p class="reportName">{{$fileBlockInput['name']}}</p>
                            <div class="d-flex justify-content-between align-items-center"
                                 style="width: 100%; background: #f2f2f2; padding: 2px; border-radius: 8px;">
                                <a class="sampleLink @if(empty($fileBlockInput['link']))notAllowed @endif "
                                   @if(!empty($fileBlockInput['link']))
                                   href="{{url($prefix.$fileBlockInput['link'])}}"
                                   @else href="#" disabled @endif >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="feather feather-download">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    Sample
                                </a>
                                <div class="d-flex justify-content-center align-items-center" style="flex: 1;">
                                    <span class="uploadButton">Upload ></span>
                                    <input type="file" style="display: none" name="{{$fileBlockInput['inputName']}}"
                                           class="{{$fileBlockInput['inputId']}}" id="{{$fileBlockInput['inputId']}}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-end align-items-center" style="gap: 1rem; width: 100%">
                    <a class="btn btn-outline-primary resetForm" style="width: 100px">Reset</a>

                    <button type="submit" class="mt-4 mb-4 btn btn-primary" style="width: 100px">
                        Submit
                        <span class="spinner-border loader" style="display: none; height: 1rem; width: 1rem"></span>
                    </button>

                </div>

            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.uploadButton').click(function (event) {
            $('.fileBlock .uploadButton').show();
            $('.fileBlock input').hide();
            $('.fileBlock input').val('');
            $(this).hide();
            $(this).siblings('input').show();
        });
        $('.resetForm').click(function (event) {
            $('.fileBlock input').val('');
        });

    </script>

@endsection
