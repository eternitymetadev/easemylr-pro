
            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a>
            <?php
                $url = URL::to('/');
                $string = request()->route()->getPrefix();
                $getprefix = str_replace('/', '', $string);
                $segment   = Request::segment(2);
                $prefixurl = $url.'/'.$getprefix.'/';
                $authuser = Auth::user();
                $permissions = App\Models\UserPermission::where('user_id',$authuser->id)->pluck('permisssion_id')->ToArray();
                $submenusegment = Request::segment(3);

                if($authuser->role_id == 6 || $authuser->role_id == 7) { 
                    $disable = 'disable_n';
                } else{
                    $disable = '';
                }
                ?>

    <div class="nav-logo align-self-center">
    <a class="navbar-brand <?php echo $disable; ?>" href="{{$prefixurl.'dashboard'}}"><img alt="logo" src="{{asset('assets/img/LOGO_Frowarders.jpg')}}"> <span class="navbar-brand-name"></span></a>
    </div>
   

<ul class="navbar-item topbar-navigation">
    
    <!--  BEGIN TOPBAR  -->
    <div class="topbar-nav header navbar" role="banner">
        <nav id="topbar">
            <ul class="navbar-nav theme-brand flex-row  text-center">
                <li class="nav-item theme-logo">
                    <a href="index.html">
                        <img src="assets/img/90x90.jpg" class="navbar-logo" alt="logo">
                    </a>
                </li>
                <li class="nav-item theme-text">
                    <!-- <a href="{{$prefixurl.'dashboard'}}" class="nav-link"> CORK </a> -->
                </li>
            </ul>

            <ul class="list-unstyled menu-categories" id="topAccordion">
            <?php 
            if(!empty($permissions)){
                if(in_array('2', $permissions))
                {
                ?>
                <li class="menu single-menu">
                    <a href="{{$prefixurl.'locations'}}">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            
                            <span>Branch Locations</span>
                        </div>
                    </a>
                </li>
                <?php }
                } if(!empty($permissions)){
                    if(in_array('3', $permissions))
                    {
                ?>
                <li class="menu single-menu">
                    <a href="{{$prefixurl.'consigners'}}" >
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cpu"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                            
                            <span>Consigners</span>
                        </div>
                    </a>
                </li>
                <?php }
                } if(!empty($permissions)){
                    if(in_array('4', $permissions))
                    {
                ?>
                <li class="menu single-menu">
                    <a href="{{$prefixurl.'consignees'}}">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-box"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            <span>Consignee</span>
                        </div>
                    </a>
                </li>
                <?php }
                }if(!empty($permissions)){
                    if(in_array('5', $permissions))
                    {
                ?>
                <li class="menu single-menu">
                    <a href="{{$prefixurl.'drivers'}}">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                            
                            <span>Drivers</span>
                        </div>
                    </a>
                </li>
                <?php }
                } if(!empty($permissions)){
                    if(in_array('6', $permissions))
                    {?>
                <li class="menu single-menu">
                    <a href="{{$prefixurl.'vehicles'}}">
                        <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                            <span>Vehicles</span>
                        </div>
                    </a>
                </li>
                <?php }
                }if($authuser->role_id !=6 && $authuser->role_id !=7){
                if(!empty($permissions)){
                    if(in_array('7', $permissions))
                    { ?>
                <li class="menu single-menu">
                   <a href="#forms" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                            
                            <span>Consignments</span>
                        </div>
                        
                    </a>
                    <ul class="collapse submenu list-unstyled animated fadeInUp" id="forms"  data-parent="#topAccordion">
                        <li>
                            <a href="{{$prefixurl.'orders'}}">Order Booking </a>
                        </li>
                        <li>
                            <a href="{{$prefixurl.'consignments/create'}}">Create Consignment </a>
                        </li>
                        <li>
                            <a href="{{$prefixurl.'consignments'}}"> Consignments List </a>
                        </li>
                        <li>
                            <a href="{{$prefixurl.'unverified-list'}}"> Create DRS</a>
                        </li>
                        <li>
                            <a href="{{$prefixurl.'transaction-sheet'}}"> Download DRS </a>
                        </li>
                        <li>
                            <a href="{{$prefixurl.'bulklr-view'}}"> Bulk Lr Download </a>
                        </li>
                       
                    </ul>
                </li>
                <?php }
            }}if($authuser->role_id == 2 || $authuser->role_id ==3 || $authuser->role_id ==5){ ?> 
            <li class="menu single-menu">
                    <a href="#payments" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                            
                            <span> Payment Request</span>
                        </div>
                        <!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg> -->
                    </a>
                    <ul class="collapse submenu list-unstyled animated fadeInUp" id="payments"  data-parent="#topAccordion">
                         <li>
                            <a href="{{$prefixurl.'vendor-list'}}">Vendor Module </a>
                        </li>
                        <li>
                            <a href="{{$prefixurl.'drs-paymentlist'}}"> Create payment </a>
                        </li> <li>
                            <a href="{{$prefixurl.'request-list'}}"> Transactions Status </a>
                        </li>
                        <?php if($authuser->role_id == 2 || $authuser->role_id ==5 || $authuser->role_id ==3){?>
                        <li>
                            <a href="{{$prefixurl.'payment-report-view'}}"> Report </a>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="{{$prefixurl.'drswise-report'}}"> Drs Wise Report </a>
                        </li>
                    </ul>
                </li>
            <?php } ?>
                <li class="menu single-menu">
                    <a href="#tables" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                            
                            <span> Reports </span>
                        </div>
                        <!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg> -->
                    </a>
                    <ul class="collapse submenu list-unstyled animated fadeInUp" id="tables"  data-parent="#topAccordion">
                        
                    <?php if($authuser->role_id !=6){ ?>
                        <li>
                            <a href="{{$prefixurl.'consignment-misreport'}}"> MIS Report 1</a>
                        </li>
                    <?php } if($authuser->role_id !=6 && $authuser->role_id !=7){ ?>
                        <li>
                            <a href="{{$prefixurl.'consignment-report2'}}"> MIS Report 2</a>
                        </li>
                    <?php } ?>
                    <?php if($authuser->role_id ==5){ ?>
                        <li>
                            <a href="{{$prefixurl.'client-report'}}"> Nurture Client Report</a>
                        </li>
                    <?php } ?>
                        <?php if($authuser->role_id == 1){ ?>
                        <li>
                            <a href="{{$prefixurl.'admin-report1'}}"> Admin Report 1</a>
                        </li>
                        <li>
                            <a href="{{$prefixurl.'admin-report2'}}"> Admin Report 2</a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                  
                <li class="menu single-menu menu-extras">
                <?php if($authuser->role_id==1){ ?>
                    <a href="#more" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-plus-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-chevron-down">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </a>
                    <?php } ?>
                    <ul class="collapse submenu list-unstyled animated fadeInUp" id="more" data-parent="#topAccordion">
                    <?php 
                    if(!empty($permissions)){
                        if(in_array('1', $permissions))
                        {
                    ?>
                        <li>
                            <a href="{{$prefixurl.'users'}}">Users List</a>
                        </li>
                        <?php }
                        }
                        ?>
                        <li>
                            <a href="{{url($prefix.'/bulk-import')}}"> Import Data </a>
                        </li>
                        <li>
                            <a href="{{url($prefix.'/settings/branch-address')}}"> Branch Address </a>
                        </li>
                        <li>
                            <a href="{{$prefixurl.'clients'}}"> Clients</a>
                        </li>
                        <!-- <li>
                            <a href="{{url('roles')}}"> Roles</a>
                        </li> -->
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
    <!--  END TOPBAR  -->

</ul>

<ul class="navbar-item flex-row ml-auto">
    
</ul>

<ul class="navbar-item flex-row nav-dropdowns">
   

    <li class="nav-item dropdown message-dropdown">
       
    </li>

    <li class="nav-item dropdown notification-dropdown">
       
    </li>

    <li class="nav-item dropdown user-profile-dropdown order-lg-0 order-1">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="user-profile-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media">
                <img src="{{asset('newasset/assets/img/90x90.jpg')}}" class="img-fluid" alt="admin-profile">
            </div>
        </a>
        <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
            <div class="user-profile-section">
                <div class="media mx-auto">
                    <div class="media-body">
                        <h5>{{ucfirst($authuser->name ?? '-')}}</h5>
                    </div>
                </div>
            </div>
            <!-- <div class="dropdown-item">
                <a href="user_profile.html">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> <span>Profile</span>
                </a>
            </div> -->
            <div class="dropdown-item">
                <a class="" href="{{route
                ('logout')}}" onclick="event.preventDefault
                (); document.getElementById('logout-form').submit();">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="feather feather-log-out"><path
                d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline
                points="16 17 21 12 16 7"></polyline><line x1="21" y1="12"
                x2="9" y2="12"></line></svg> Sign Out</a> 
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf </form>
            </div>
           
        </div>

    </li>
</ul>