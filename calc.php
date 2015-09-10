<?php

if($_POST['submit']) {
	//P&L array declaration
	$pnl = array();
	
	//first the long stock
	if(isset($_POST['stock_purchase'])) {
		$new_pnl = long_stock($_POST['stock_purchase'], $pnl, $_POST['stock_purchase_size']);
	}
	

}	

function long_stock($purchase, $pnl, $quantity) {
	
	//first establish the price range, +/- 20%
	$min = $purchase*0.8;
	$max = $purchase*1.2;

	for($i=$min; $i<=$max; $i+=0.25) {
		$pnl["$i"] = $i;
	}
	
	//now do actual pnl calculation
	foreach($pnl as $k=>$v) {
		$pnl[$k] = ($v - $purchase)*$quantity;
	}
	
	return $pnl;
}
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="table.css">
		<style>
			#results {
				border: 2px solid black;
				padding:20px;
			}
		</style>
	</head>
	<body style="padding:20px;">
		 <form action="" method="post">
				<p>Long stock price: <input type="text" name="stock_purchase" size="5">Long stock quanity: <input type="text" name="stock_purchase_size" value="100" size="5"></p>
				<p>
				Leg 1: <select name="leg1_buy_sell">
								<option value="buy">Buy</option>
								<option value="sell">Sell</option>
						   </select>
						   <select name="leg1_call_put">
								<option value="call">Call</option>
								<option value="put">Put</option>
						   </select>
				Purchase Price: <input type="text" name="leg1_purchase" size="5">
				Purchase Quanity: <input type="text" name="leg1_quantity" size="5">
				</p>
				<p>
				Leg 2: <select name="leg2_buy_sell">
								<option value="buy">Buy</option>
								<option value="sell">Sell</option>
						   </select>
						   <select name="leg2_call_put">
								<option value="call">Call</option>
								<option value="put">Put</option>
						   </select>
				Purchase Price: <input type="text" name="leg2_purchase" size="5">
				Purchase Quanity: <input type="text" name="leg2_quantity" size="5">
				</p>
				<p>
				Leg 3: <select name="leg3_buy_sell">
								<option value="buy">Buy</option>
								<option value="sell">Sell</option>
						   </select>
						   <select name="leg3_call_put">
								<option value="call">Call</option>
								<option value="put">Put</option>
						   </select>
				Purchase Price: <input type="text" name="leg3_purchase" size="5">
				Purchase Quanity: <input type="text" name="leg3_quantity" size="5">
				</p>
				<p>
				Leg 4: <select name="leg4_buy_sell">
								<option value="buy">Buy</option>
								<option value="sell">Sell</option>
						   </select>
						   <select name="leg4_call_put">
								<option value="call">Call</option>
								<option value="put">Put</option>
						   </select>
				Purchase Price: <input type="text" name="leg4_purchase" size="5">
				Purchase Quanity: <input type="text" name="leg4_quantity" size="5">
				</p>
				<p><input type="submit" name="submit" value="Calculate"></p>
		</form>
		
		<div id="results">
			<h2>Max loss: <?php echo min($new_pnl); ?> =========== Max Profit: <?php echo max($new_pnl);?></h2> 
			<table class="pure-table pure-table-bordered">
				<thead>
					<tr>
						<td>Value</td>
						<td>Profit/Loss</td>
					</tr>
				</thead>
				<tbody>
		<?php 
			foreach($new_pnl as $k=>$v) {
				echo "<tr><td>";
				echo $k;
				echo "</td>";
				if($v < 0) {
					echo "<td style='color:red'>";
				}
				else {
					echo "<td>";
				}
				echo $v;
				echo "</td></tr>";
			}
		?>
			   </tbody>
			</table>
		</div>
	</body>
</html>
