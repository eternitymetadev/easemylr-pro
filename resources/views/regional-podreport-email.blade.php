<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

</head>

<body>
    <p>
        Dear Business Client, <br /> <br />
        Please find the attached Daily POD Report. This report has been generated by our system and includes data from the last 45 days up to the {{$formattedDate}} {{$current_time}}.<br />
        Thank you.<br /><br />
        Best regards,<br />
        <img src="{{asset('assets/ridr.png')}}" style="height: 30px; object-fit:contain;"/>
    </p>

</body>

</html>