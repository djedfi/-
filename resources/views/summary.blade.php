<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Receipt AA Motors</title>

		<style>
			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 14px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 14px;
				line-height: 25px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
                text-align: center;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}
		</style>
	</head>

	<body>
        @if(count($loan) > 0)
            @foreach($loan as $key=>$item)
                @php
                    $full_name          = $item->name_customer;
                    $cellphone          = $item->cellphone;
                    $email              = $item->email;
                    $model_car          = $item->info_car;
                    $vin                = $item->vin;
                    $tfinance           = '$US '.number_format($item->total_financed,2,'.',',');
                    $balance            = '$US '.number_format($item->balance,2,'.',',');
                    $precio             = '$US '.number_format($item->precio,2,'.',',');
                    $loan_date          = $item->loan_date;
                    $start_date         = $item->start_payment;

                @endphp
            @endforeach
        @elseif (count($loan) === 0)
            @php
            $date_payment       = 'NO DATA';
            $invoice_number     = 'NO DATA';
            $full_name          = 'NO DATA';
            $cellphone          = 'NO DATA';
            $email              = 'NO DATA';

            @endphp
        @endif


		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="4">
						<table>
							<tr>
								<td class="title">
									<img src="https://aamotorsla.com/wp-content/uploads/2022/02/logo-recortado-removebg-preview.png" style="width: 100%; max-width: 200px" /><br />
                                    {{$name_company}}<br />
									{{$address_company_p}}<br />
									{{$address_company_s}}<br />
                                    {{$cellphone_company}}
								</td>

								<td>
                                    <br /><br /><br />
                                    Total to Finance: {{$tfinance}}<br />
									Date Loan: {{$loan_date}}<br />
									Starting Payment: {{$start_date}}<br />
                                    Balance: {{$balance}}<br />
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="4">
						<table>
							<tr>
								<td>
									Full Name: {{$full_name}}<br />
                                    Cellphone Number: {{$cellphone}}<br />
                                    EMail: {{$email}}
								</td>

								<td>
									Model Car: {{$model_car}}<br />
									VIN: {{$vin}}<br />
									Vehicle Price: {{$precio}}
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="heading">
					<td style="width:50%">DESCRIPTION</td>
					<td style="width:10%">DATE<BR>PAYMENT</td>
                    <td style="width:20%">AMOUNT DUE</td>
                    <td style="width:20%">BALANCE</td>
				</tr>
                @foreach($payments as $key=>$item)
                <tr class="details">
					<td style="width:50%">{{$item->description}}</td>
					<td style="width:10%">{{$item->date_payment}}</td>
                    <td style="width:20%; text-align:center">{{'$US '.number_format($item->monto,2,'.',',')}}</td>
                    <td style="width:20%; text-align:center">{{'$US '.number_format($item->balance,2,'.',',')}}</td>
				</tr>

                @endforeach
			</table>
		</div>
	</body>
</html>
