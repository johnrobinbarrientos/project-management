<!DOCTYPE html>
<html>
<head>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
	<style>
		html,body { margin:0px; padding:0px; background:#f5f6fa; font-family: 'Roboto', sans-serif; } 
		.main { padding:10px 20px; }
		.container { 
			padding:40px 20px; padding-bottom:80px; background:#fff; max-width:650px; margin:auto; margin-top:20px; margin-bottom:20px; 
			box-shadow: 0px 1px 3px 0px rgb(54 74 99 / 5%); 
			border: 0 solid rgba(0,0,0,0.125);
			border-radius: 4px;
		}
		
		.table { width:100%; margin-bottom:20px; }
		.table th { background:#efefef; padding:3px 10px; text-align:left; }
		.table td { background:#fafafa; padding:3px 10px; text-align:left; }
		
		.btn-view-order { display:inline-block; text-align:center; background:#000; color:#fff; padding:10px 20px; border-radius:4px; text-decoration:none; }
		.btn-view-order:hover { opacity:0.8; transition:0.5s; }
	</style>
</head>
<body>
	<div class="main">
		<div class="container">
			<div style="text-align:center; padding:30px 10px;">
				<i style="font-size:30px;" class="fas fa-cart-arrow-down"></i>
			</div>
			
			<div style="text-align:center;">
				<h3 style="margin-top:-10px; color:green;">A new order has been placed.</h3>
				<table class="table">
                <tr>
                        <th>Service</th>
							<td>{{ $data['order']['service']['name'] }}</td>
						</tr>
						<tr>
							<th>Delivery Date</th>
							<td>{{ $data['order']['delivery_date'] }}</td>
						</tr>
						<tr>
							<th>Pickup Address</th>
							<td>{{ $data['order']['pickup_location'] }}</td>
						</tr>
						<tr>
							<th>Dropoff Address</th>
							<td>{{ $data['order']['dropoff_location'] }}</td>
						</tr>
				</table>
				<a class="btn-view-order"  href="{{ getenv('APP_URL') }}/orders/{{ $data['order']['id'] }}">View Order</a>
			</div>
		</div>
	</div>
</body>
</html>
