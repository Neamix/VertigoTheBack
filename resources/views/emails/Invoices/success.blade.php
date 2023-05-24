<html>
    <head>
        <style>
            body {
                background: #212121;
                font-family: sans-serif;
            }   

            p {
                margin: 0
            }

            .container {
                width: 100%;
                max-width: 500px;
                margin: 0 auto;
                padding: 0 6px;
            }

            .card {
                width: 100%;
                padding: 22px;
                border-radius: 20px;
                background: #fff;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <img src="">
            <div class="card">
                <div class="card-header">
                    <p class="invoice-title" style="font-size: 13px; color: #7a7a7a"> 	
                        Receipt from Vertigo CRM
                    </p>
                </div>
                <div class="card-body">
                    <section class="total">
                        <p class="total-amount" style="font-size:30px; font-weight: bold; margin-top: 10px">
                            ${{ $data['seats'] *  $data['value_per_seats']}}
                        </p>
                        <p class="invoice-title mt-3" style="font-size: 13px; color: #7a7a7a; margin-top: 10px"> 	
                            Paid on {{date('M d Y')}}
                        </p>
                    </section>
                    <section class="details" style="border-top: 1px solid #e3e3e3; margin: 22px 0px;padding: 10px 0">
                        <div class="download-links">
                            <a href="{{ route('stripe.invoice',['company' => $data['company'],'invoiceID' => $data['invoiceID']]) }}" style="font-size: 13px;color: 7a7a7a;text-decoration: none;padding: 0 20px;">Download Receipt</a>
                        </div>
                        <table style="width: 100%; margin: 10px 0">
                            <tr style="width: 100%;  margin-top: 4px">
                                <td style="font-size: 13px;color: #7a7a7a;display:block;padding-top: 7px;font-weight:bold">  Receipt number  </td>
                                <td style="font-size: 13px;text-align:end">{{ $data['inovice_number'] }} </td>
                            </tr>
                            <tr></tr>
                            <tr style="width: 100%; margin-top: 4px">
                                <td style="font-size: 13px;color: #7a7a7a;font-weight:bold"> Payment method </td>
                                <td style="font-size: 13px;text-align:end"> {{$data['credit_pm_type']}}-{{$data['credit_pm_last_four']}} </td>
                            </tr>
                        </table>
                    </section>
                </div>
            </div>
            <div class="card" style="margin-top: 10px">
                <div class="card-header">
                    <p class="invoice-title" style="font-size: 13px; color: #7a7a7a"> 	
                        Receipt #{{ $data['inovice_number'] }}
                    </p>
                </div>
                <div class="card-body">
                    <section class="details" style="border-top: 1px solid #e3e3e3; margin: 22px 0px;padding: 10px 0">
                        <table style="width: 100%; margin: 10px 0">
                            <tr style="width: 100%;  margin-top: 4px">
                                <td style="font-size: 13px;color: #7a7a7a;font-weight:bold"> Price per seat </td>
                                <td style="font-size: 13px;text-align:end"> ${{ $data['value_per_seats'] }} </td>
                            </tr>
                            <tr></tr>
                            <tr style="width: 100%;  margin-top: 4px">
                                <td style="font-size: 13px;color: #7a7a7a;display:block;padding: 7px 0;font-weight:bold">  Number of seats  </td>
                                <td style="font-size: 13px;text-align:end"> {{ $data['seats'] }} Seats </td>
                            </tr>
                            <tr></tr>
                            <tr style="width: 100%;  margin-top: 4px">
                                <td style="font-size: 13px;color: #7a7a7a;display:block;padding: 7px 0;font-weight:bold">  Total  </td>
                                <td style="font-size: 13px;text-align:end"> ${{ $data['seats'] *  $data['value_per_seats']}} </td>
                            </tr>
                        </table>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>