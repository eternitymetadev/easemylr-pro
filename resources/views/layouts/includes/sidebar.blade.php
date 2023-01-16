<ul class="list-unstyled menu-categories" id="accordionExample">
    <div class="logoBox">
        <img alt="logo" src="{{asset('assets/img/LOGO_Frowarders.jpg')}}">
    </div>

    <?php $authuser = Auth::user();
    $currentURL = url()->current();
    ?>

    <?php
    $url = URL::to('/');
    $string = request()->route()->getPrefix();
    $getprefix = str_replace('/', '', $string);
    $segment = Request::segment(2);
    $prefixurl = $url . '/' . $getprefix . '/';
    $authuser = Auth::user();
    $permissions = App\Models\UserPermission::where('user_id', $authuser->id)->pluck('permisssion_id')->ToArray();
    $submenusegment = Request::segment(3);
    // dd($permissions);
    ?>
    <div class="shadow-bottom"></div>
    <li class="menu">
        <a href="{{$prefixurl.'dashboard'}}" data-active="<?php if($segment == 'dashboard'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'dashboard')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-home">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Dashboard</span>
            </div>
        </a>
    </li>
    <?php
    if(!empty($permissions)){
    if(in_array('1', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'users'}}" data-active="<?php if($segment == 'users'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'users')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Users</span>
            </div>
        </a>
    </li>
    <?php }
    }
    ?>
    <?php
    if(!empty($permissions)){
    if(in_array('2', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'locations'}}" data-active="<?php if($segment == 'locations'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'locations')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-map-pin">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>Branch Locations</span>
            </div>
        </a>
    </li>
    <?php }
    }
    ?>
    <?php
    if(!empty($permissions)){
    if(in_array('3', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'consigners'}}" data-active="<?php if($segment == 'consigners'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'consigners')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-layers">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                <span>Consigners</span>
            </div>
        </a>
    </li>
    <?php }
    } ?>
    <?php
    if(!empty($permissions)){
    if(in_array('4', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'consignees'}}" data-active="<?php if($segment == 'consignees'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'consignees')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-github">
                    <path
                        d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                    </path>
                </svg>
                <span>Consignee</span>
            </div>
        </a>
    </li>
    <?php }
    }
    ?>
    <!-- <li class="menu">
                        <a href="{{$prefixurl.'brokers'}}" data-active="<?php if($segment == 'brokers'){?>true<?php }?>" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <span>Brokers</span>
                            </div>
                        </a>
                    </li> -->
    <?php
    if(!empty($permissions)){
    if(in_array('5', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'drivers'}}" data-active="<?php if($segment == 'drivers'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'drivers')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Drivers</span>
            </div>
        </a>
    </li>
    <?php }
    } ?>
    <?php
    if(!empty($permissions)){
    if(in_array('6', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'vehicles'}}" data-active="<?php if($segment == 'vehicles'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'vehicles')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-truck">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
                <span>Vehicles</span>
            </div>
        </a>
    </li>
    <?php }
    } ?>
    <?php
    if(!empty($permissions)){
    if(in_array('7', $permissions))
    {
    ?>
    <?php }
    } ?>
    <li class="menu">
        <a href="#consignment" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'consignments') || str_contains($currentURL, 'bulklr-view')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Consignment</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="consignment" data-parent="#accordionExample">
            <li>
                <a href="{{$prefixurl.'consignments/create'}}">Create Consignment </a>
            </li>
            <li>
                <a href="{{$prefixurl.'consignments'}}"> Consignment List </a>
            </li>
            <li>
                <a href="{{$prefixurl.'bulklr-view'}}"> Bulk Lr Download </a>
            </li>

        </ul>
    </li>

    <li class="menu">
        <a href="#ftl" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'order-book-ftl') || str_contains($currentURL, 'create-ftl')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>FTL Consignment</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="ftl" data-parent="#accordionExample">
            <li>
                <a href="{{$prefixurl.'order-book-ftl'}}"> Book Order </a>
            </li>
            <li>
                <a href="{{$prefixurl.'create-ftl'}}"> Create LR Ftl</a>
            </li>
            <li>
                <a href="#"> LR List </a>
            </li>
        </ul>
    </li>

    <li class="menu">
        <a href="#Ptl" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'order-book-ptl') || str_contains($currentURL, 'create-ptl')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>PTL Consignment</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="Ptl" data-parent="#accordionExample">
        <li>
                <a href="{{$prefixurl.'orders'}}"> Order list</a>
            </li>
            <li>
                <a href="{{$prefixurl.'order-book-ptl'}}"> Book Order </a>
            </li>
            <li>
                <a href="{{$prefixurl.'create-ptl'}}"> Create LR Ptl</a>
            </li>
            <li>
                <a href="#"> LR List </a>
            </li>
        </ul>
    </li>


    <li class="menu">
        <a href="#drs" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'unverified-list') || str_contains($currentURL, 'transaction-sheet')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>DRS</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="drs" data-parent="#accordionExample">
            <li>
                <a href="{{$prefixurl.'unverified-list'}}"> Create Drs </a>
            </li>
            <li>
                <a href="{{$prefixurl.'transaction-sheet'}}"> Drs List </a>
            </li>

        </ul>
    </li>

    <li class="menu">
        <a href="#hrs" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'hrs-list') || str_contains($currentURL, 'hrs-sheet') || str_contains($currentURL, 'incoming-hrs')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>HRS</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="hrs" data-parent="#accordionExample">
            <li>
                <a href="{{$prefixurl.'hrs-list'}}"> Create Hrs </a>
            </li>
            <li>
                <a href="{{$prefixurl.'hrs-sheet'}}"> Hrs List </a>
            </li>
            <li>
                <a href="{{$prefixurl.'incoming-hrs'}}"> Incoming Hrs </a>
            </li>
            <li>
                <a href="{{$prefixurl.'outgoing-hrs'}}"> Outgoing Hrs </a>
            </li>

        </ul>
    </li>

    <li class="menu">
        <a href="#prs" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'prs') || str_contains($currentURL, 'driver-tasks') || str_contains($currentURL, 'vehicle-receivegate')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>PRS</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="prs" data-parent="#accordionExample">
            <li>
                <a href="{{$prefixurl.'prs'}}"> Create Prs </a>
            </li>
            <li>
                <a href="{{$prefixurl.'driver-tasks'}}"> Driver Task </a>
            </li>
            <li>
                <a href="{{$prefixurl.'vehicle-receivegate'}}"> Hub Receiving </a>
            </li>

        </ul>
    </li>

    <li class="menu">
        <a href="#vendors" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'vendor-list') || str_contains($currentURL, 'drs-paymentlist') || str_contains($currentURL, 'request-list') || str_contains($currentURL, 'payment-report-view') || str_contains($currentURL, 'drswise-report')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Payments</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="vendors" data-parent="#accordionExample">
            <li>
                <a href="{{$prefixurl.'vendor-list'}}"> Vendors </a>
            </li>
            <li>
                <a href="{{$prefixurl.'drs-paymentlist'}}"> Create Payments </a>
            </li>
            <li>
                <a href="{{$prefixurl.'request-list'}}"> Transaction Status </a>
            </li>
            <li>
                <a href="{{$prefixurl.'payment-report-view'}}">Payment Report -Transaction Id wise </a>
            </li>
            <li>
                <a href="{{$prefixurl.'drswise-report'}}">Payment Report - DRS id wise </a>
            </li>

        </ul>
    </li>

    <li class="menu">
        <a href="#reports" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'consignment-misreport') || str_contains($currentURL, 'consignment-report2') || str_contains($currentURL, 'postal-code') || str_contains($currentURL, 'pod-view')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Reports</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="reports" data-parent="#accordionExample">
            <li>
                <a href="{{$prefixurl.'consignment-misreport'}}"> Mis Report 1 </a>
            </li>
            <li>
                <a href="{{$prefixurl.'consignment-report2'}}"> Mis Report 2 </a>
            </li>
            <li>
                <a href="{{$prefixurl.'postal-code'}}"> Postal Code </a>
            </li>
            <li>
                <a href="{{$prefixurl.'pod-view'}}"> Pod View </a>
            </li>

        </ul>
    </li>
    <?php if($authuser->role_id == 1){ ?>
    <li class="menu">
        <a href="#users" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'users') || str_contains($currentURL, 'roles') || str_contains($currentURL, 'permissions')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>System Settings</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="users" data-parent="#accordionExample">
            <li>
                <a href="{{$prefixurl.'users'}}"> All Users </a>
            </li>
            <li>
                <a href="{{$prefixurl.'roles'}}"> All Roles </a>
            </li>
            <li>
                <a href="{{$prefixurl.'permissions'}}"> All Permissions </a>
            </li>
        </ul>
    </li>
    <?php } if($authuser->role_id == 1){ ?>
    <li class="menu">
        <a href="#forms" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'bulk-import') || str_contains($currentURL, 'branch-address')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-clipboard">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                </svg>
                <span>Settings</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu " id="forms" data-parent="#accordionExample">
            <li>
                <a href="{{url($prefix.'/bulk-import')}}"> Import Data </a>
            </li>
            <li>
                <a href="{{url($prefix.'/settings/branch-address')}}"> Branch Address </a>
            </li>

        </ul>
    </li>
    <?php } ?>

</ul>