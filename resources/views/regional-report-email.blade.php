<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

</head>

<body>
    <p>
        Dear {{$client_name}}, <br /> <br />
        Please find attached MTD Basis Daily MIS Report, Report is generated by our system. The data included in the report is compiled as of {{$current_time}}.<br />
        Thank you.<br /><br />
        Best regards,<br />
        <img src="{{asset('assets/ridr.png')}}" />
    </p>

</body>

</html>