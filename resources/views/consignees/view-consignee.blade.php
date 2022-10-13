@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="page-header">
                        <nav class="breadcrumb-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Consignees</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">View Consignee</a></li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-9 text-right">
                        <!-- <a href="{{url($prefix.'/consignees/'.Crypt::encrypt($getconsignee->id).'/edit')}}" class="btn my-3" href="" style="background:#fff;" title="Edit Consignee"><i class="fa fa-edit m-0"></i></a> -->
                    </div>
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row">Consignee Nick Name</th>
                                    <td>{{isset($getconsignee->nick_name)?ucfirst($getconsignee->nick_name):'-'}} </td>
                                </tr>
                                <tr>
                                    <th scope="row">Consignee Legal Name</th>
                                    <td>{{isset($getconsignee->legal_name)?ucfirst($getconsignee->legal_name):'-'}} </td>
                                </tr>
                                <tr>
                                    <th scope="row">Contact Person Name</th>
                                    <td>{{isset($getconsignee->contact_name)?ucfirst($getconsignee->contact_name):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email ID</th>
                                    <td>{{isset($getconsignee->email)?ucfirst($getconsignee->email):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Mobile No.</th>
                                    <td>{{isset($getconsignee->phone)?ucfirst($getconsignee->phone):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Consigner</th>
                                    <td> 
                                        {{isset($getconsignee->GetConsigner->nick_name) ? ucfirst($getconsignee->GetConsigner->nick_name) : "-" }}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Type Of Dealer</th>
                                    <td><?php if($getconsignee->dealer_type == 1){
                                            echo "Register";
                                        }else if($getconsignee->dealer_type == 0){
                                            echo "Unregister";
                                        } else{ ?>
                                                {{$getconsignee->dealer_type ?? "-"}}
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">GSTNO.</th>
                                    <td>{{isset($getconsignee->gst_number)?ucfirst($getconsignee->gst_number):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Pincode</th>
                                    <td>{{isset($getconsignee->postal_code) ? ucfirst($getconsignee->postal_code):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">City</th>
                                    <td>{{isset($getconsignee->city) ? ucfirst($getconsignee->city):'-'}} </td>
                                </tr>
                                <tr>
                                    <th scope="row">District</th>
                                    <td>{{isset($getconsignee->GetZone->district)?ucfirst($getconsignee->GetZone->district):'-'}} </td>
                                </tr>
                              
                                <tr>
                                    <th scope="row">State</th>
                                    <td>{{isset($getconsignee->GetZone->state) ? ucfirst($getconsignee->GetZone->state) : "-" }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Primary Zone</th>
                                    <td>{{isset($getconsignee->GetZone->primary_zone) ? ucfirst($getconsignee->GetZone->primary_zone) : "-" }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Address Line 1</th>
                                    <td>{{isset($getconsignee->address_line1)?ucfirst($getconsignee->address_line1):'-'}} </td>
                                </tr>
                                <tr>
                                    <th scope="row">Address Line 2</th>
                                    <td>{{isset($getconsignee->address_line2)?ucfirst($getconsignee->address_line2):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Address Line 3</th>
                                    <td>{{isset($getconsignee->address_line3)?ucfirst($getconsignee->address_line3):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Address Line 4</th>
                                    <td>{{isset($getconsignee->address_line4)?ucfirst($getconsignee->address_line4):'-'}}</td>
                                </tr>
                               
                                <!-- <tr>
                                    <th scope="row">Status</th>
                                    <td>
                                        <?php// if($getconsignee->status == 1){
                                        //    echo "Active";
                                       // }else if($getconsignee->status == 0){
                                         //   echo "Deactive";
                                      //  } else{ ?>
                                                {{$getconsignee->status ?? "-"}}
                                        <?php// } ?>
                                    </td>
                                </tr> -->
                                    
                            </tbody>
                        </table>  
                     <a href="{{url($prefix.'/consignees/'.Crypt::encrypt($getconsignee->id).'/edit')}}" class="btn btn-primary my-3" href="" style="background:#fff;" title="Edit Consignee"><i class="fa fa-edit m-0"></i>Edit</a>
                        <a class="btn btn-primary" href="{{url($prefix.'/consignees')}}"> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection