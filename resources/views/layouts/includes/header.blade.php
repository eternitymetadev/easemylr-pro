<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <?php header("Access-Control-Allow-Origin:*"); ?>
    <title>Eternity Forwarders | {{$title ?? ''}} </title> 
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon.jpg')}}"/>
    <!-- <link href="{{asset('assets/css/loader.css')}}" rel="stylesheet" type="text/css" />
    <script src="{{asset('assets/js/loader.js')}}"></script> -->
     <!-- BEGIN GLOBAL MANDATORY STYLES -->
     <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
    <link href="{{asset('newasset/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('newasset/assets/css/plugins.css')}}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{asset('newasset/plugins/apex/apexcharts.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('newasset/assets/css/dashboard/dash_2.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/widgets/modules-widgets.css')}}">    
   

    <link rel="stylesheet" href="{{asset('assets/css/jquery.toast.css')}}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" />
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/select2/select2.min.css')}}">
    <link href="{{asset('newasset/assets/css/components/tabs-accordian/custom-tabs.css')}}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{asset('assets/css/sumoselect.min.css')}}" type="text/css" />

    <!-- gmap script -->
    <script defer src="https://maps.googleapis.com/maps/api/js?libraries=places&language=en&key={{config('services.googleMap_key.map_key')}}"
            type="text/javascript"></script>
            
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

           
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/table/datatable/dt-global_style.css')}}"> -->
    <!-- END PAGE LEVEL CUSTOM STYLES -->
