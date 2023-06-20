@extends('layouts.main')
@section('content')

    <form method="POST" action="{{url($prefix.'/employees/upload-csv')}}" id="importfiles" enctype="multipart/form-data">
        @csrf 
        <div class="row">
            <div class="col-lg-4 col-md-3 col-sm-12">
                <h4 class="win-h4">Browse Employees Sheet</h4>
            </div>
            <div class="col-lg-4 col-md-9 col-sm-12">
                <input type="file" name="employeesfile" id="employeefile" class="employeefile"> 
            </div>
        </div>

        <button type="submit" name="" class="mt-4 mb-4 btn btn-primary">Submit</button>
        <div class="spinner-border loader" style= "display:none;"></div>
        <a class="btn btn-primary" href="{{url($prefix.'/dashboard') }}"> Back</a>
    </form>

            
@endsection