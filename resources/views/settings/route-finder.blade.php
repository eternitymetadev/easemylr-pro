@extends('layouts.main')
@section('content')

<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
          
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #f9b808;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #routeList {
            margin-top: 20px;
        }
    </style>

<div class="container">
        <h1>Route Finder</h1>
        <form id="routeForm">
            <div class="form-group">
                <label for="startingPincode">Starting Pincode:</label>
                <input type="text" id="startingPincode" name="startingPincode" placeholder="Enter starting pincode">
            </div>
            <div class="form-group">
                <label for="endingPincode">Ending Pincode:</label>
                <input type="text" id="endingPincode" name="endingPincode" placeholder="Enter ending pincode">
            </div>
            <button type="submit" id="findRouteBtn">Find Route</button>
        </form>
        <div id="routeList"></div>
    </div>
@endsection
@section('js')
<script>
 $('#routeForm').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission

        var startingPincode = $('#startingPincode').val();
        var endingPincode = $('#endingPincode').val();

        // Make an AJAX request to find routes
        $.ajax({
        url: "find-route",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response) {
                $('#routeList').html(response);
            },
            error: function() {
                $('#routeList').html('<p>Error occurred. Please try again.</p>');
            }
    });
    });
</script>
@endsection