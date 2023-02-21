<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <style>
    p {
        margin-bottom: 0px !important;
        font-size: 0.9rem;
        line-height: 1.4rem;
    }
    </style>
</head>

<body>
    <p>
        Dear Client,<br /><br />
        Your order has been picked by our agent and is ready for shipping. Please see the details of the same
        below<br /><br />
        LR Number : <span>{{$Lr_No}}</span><br />
        Consignor : <span>{{$consignor}}</span><br />
        Consignee Name : <span>{{$consignee_name}}</span><br />
        Consignee PIN : <span>{{$consignee_pin}}</span><br />
        Client : <span>{{$client}}</span><br />
        Net Weight : <span>{{$net_weigth}}</span><br />
        No of Cases : <span>{{$cases}}</span><br /><br />
        Copy of Consignment Note is attached for your reference<br><br />
        Auto Email from Eternity</p>
</body>

</html>