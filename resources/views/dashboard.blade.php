@extends('layouts.main')
@section('content')
<style>
    .widget-four .widget-content .w-summary-info .summary-count {
    display: block;
    /* font-size: 16px; */
    margin-top: 4px;
    font-weight: 600;
    color: #515365;
    background: #03a9f4 ! important;

}
.widget-four .widget-content .w-summary-info h6 {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 0;
    color: #fbfbfc;
}
.widget-four .widget-content .summary-list:nth-child(1) .w-icon svg {
    color: #ffffff;
    /* fill: rgb(255 255 255 / 16%); */
}
.widget-four .widget-content .w-icon {
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 38px;
    width: 50px;
    margin-right: 12px;
}

    </style>

<div class="layout-px-spacing">
<div class="page-header layout-spacing">
                <nav class="breadcrumb-one" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Widgets</a></li>
                    </ol>
                </nav>
            </div>
    <div class="row layout-top-spacing">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-four">
                
                <div class="widget-content">

                    <div class="order-summary">

                        <div class="summary-list summary-income" style="background-color: #22beef; height: 81px;">

                            <div class="summery-info">

                                <div class="w-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                </div>

                                <div class="w-summary-details">

                                    <div class="w-summary-info">
                                        <h6><span  style="font-size: 23px;">{{$gettoday_lr}}</span><span class="summary-count smry"> </span>Today's LR</h6>
                                        <p class="summary-average" style="margin-top: 10px;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-list summary-profit" style="background-color: #a2d200; height: 81px;">

                            <div class="summery-info">

                                <div class="w-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7" y2="7" style="color: #ffffff;"></line></svg>
                                </div>
                                <div class="w-summary-details">

                                    <div class="w-summary-info">
                                        <h6><span  style="font-size: 23px;">{{$getcurrentmonth_lr}}</span><span class="summary-count"></span>Current Month's LR</h6>
                                        <p class="summary-average" style="margin-top: 10px;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                       
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-four">
               
                <div class="widget-content">
                    <div class="order-summary">
                        <div class="summary-list summary-income" style="background-color: #00a2ae;">
                            <div class="summery-info " >

                                <div class="w-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                </div>

                                <div class="w-summary-details">

                                    <div class="w-summary-info">
                                        <h6><span style="font-size: 23px;">{{number_format((float)$gettoday_weightlifted,2, '.', '')}} (MT)</span><span class="summary-count"> </span>Today Net Weight lifted</h6>
                                        <p class="summary-average"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-list summary-profit" style="background-color: #ff4a43;">

                            <div class="summery-info">

                                <div class="w-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7" y2="7" style="color: #ffffff;"></line></svg>
                                </div>
                                <div class="w-summary-details">

                                    <div class="w-summary-info">
                                        <h6><span style="font-size: 23px;">{{number_format((float)$getmonthly_weightlifted,2, '.', '')}} (MT)</span><span class="summary-count"> </span>Monthly Net Weight Lifted</h6>
                                        <p class="summary-average"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                      
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-four">
              
                <div class="widget-content">
                    <div class="order-summary">
                        <div class="summary-list summary-income" style="background-color: #8f44ad;">
                            <div class="summery-info">
                                <div class="w-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                </div>

                                <div class="w-summary-details">
                                    <div class="w-summary-info">
                                        <h6><span style="font-size: 23px;">{{number_format((float)$gettoday_gross_weightlifted,2, '.', '')}} (MT)</span><span class="summary-count"> </span>Today's Gross Weight Lifted</h6>
                                        <p class="summary-average"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-list summary-profit" style="background-color: #685aee;;">

                            <div class="summery-info">

                                <div class="w-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag" ><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" style="border: #ffffff;"></path><line x1="7" y1="7" x2="7" y2="7" ></line></svg>
                                </div>
                                <div class="w-summary-details">

                                    <div class="w-summary-info">
                                        <h6><span style="font-size: 23px;">{{number_format((float)$getmonthly_gross_weightlifted,2, '.', '')}} (MT)</span><span class="summary-count"> </span>Monthly Gross Weight Lifted</h6>
                                        <p class="summary-average"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

       
      

        
        <!-- <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-one">
                <div class="widget-heading">
                    <h5 class="">Revenue</h5>
                    <div class="task-action">
                        <div class="dropdown">
                            <a class="dropdown-toggle" href="#" role="button" id="pendingTask" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="pendingTask" style="will-change: transform;">
                                <a class="dropdown-item" href="javascript:void(0);">Weekly</a>
                                <a class="dropdown-item" href="javascript:void(0);">Monthly</a>
                                <a class="dropdown-item" href="javascript:void(0);">Yearly</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="widget-content">
                    <div id="revenueMonthly"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-two">
                <div class="widget-heading">
                    <h5 class="">Sales by Category</h5>
                </div>
                <div class="widget-content">
                    <div id="chart-2" class=""></div>
                </div>
            </div>
        </div> -->
        <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-three">
                <div class="widget-heading">
                    <div class="">
                        <h5 class="">Unique Visitors</h5>
                    </div>

                    <div class="dropdown ">
                        <a class="dropdown-toggle" href="#" role="button" id="uniqueVisitors" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="uniqueVisitors">
                            <a class="dropdown-item" href="javascript:void(0);">View</a>
                            <a class="dropdown-item" href="javascript:void(0);">Update</a>
                            <a class="dropdown-item" href="javascript:void(0);">Download</a>
                        </div>
                    </div>
                </div>

                <div class="widget-content">
                    <div id="uniqueVisits"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-activity-five">

                <div class="widget-heading">
                    <h5 class="">Activity Log</h5>

                    <div class="task-action">
                        <div class="dropdown">
                            <a class="dropdown-toggle" href="#" role="button" id="pendingTask" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="pendingTask" style="will-change: transform;">
                                <a class="dropdown-item" href="javascript:void(0);">View All</a>
                                <a class="dropdown-item" href="javascript:void(0);">Mark as Read</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="widget-content">

                    <div class="w-shadow-top"></div>

                    <div class="MTs-container mx-auto">
                        <div class="timeline-line">

                            @foreach($Lrsimplify as $lastlr)
                          
                         
                            <div class="item-timeline timeline-new">
                                <div class="t-dot">
                                    <div class="t-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></div>
                                </div>
                                <div class="t-content">
                                    <div class="t-uppercontent">
                                        <h5>{{$lastlr['consignee_detail']['nick_name'] ?? '-'}}<a href="javscript:void(0);"><span></span></a></h5>
                                    </div>
                                    <p>{{$lastlr['consignment_date'] ?? '-'}}</p>
                                </div>
                            </div>
                            @endforeach
               
                        </div>                                    
                    </div>

                    <div class="w-shadow-bottom"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection