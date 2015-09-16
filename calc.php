<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

if($_POST['submit'] and $_POST['market_price']) {
	//P&L array declaration
	$pnl = array();
	$new_pnl = array();
	
	//construct the p&l array
	$pnl = make_array($_POST['market_price']);
	
	//first the long stock
	if(isset($_POST['stock_purchase']) and $_POST['stock_purchase'] != '') {
		$new_pnl = long_stock($_POST['stock_purchase'], $pnl, $_POST['stock_purchase_size']);
	}
	else {
		$new_pnl = $pnl;
	}
	
	//now each leg - TODO make it dynamic
	if(isset($_POST['leg1_strike']) and $_POST['leg1_strike'] != '') {
		//calculate the premium first
		$premium = $_POST['leg1_purchase'] * $_POST['leg1_quantity'] * 100;
		
		if($_POST['leg1_call_put'] == 'put') {
			$new_pnl = put_option($premium, $new_pnl, $_POST['leg1_buy_sell'], $_POST['leg1_strike'], $_POST['leg1_quantity']);
		}
		elseif($_POST['leg1_call_put'] == 'call') {
			$new_pnl = call_option($premium, $new_pnl, $_POST['leg1_buy_sell'], $_POST['leg1_strike'], $_POST['leg1_quantity']);
		}
	}
	

}	
function make_array($market_price) {

	//first establish the price range, +/- 20%
	$min = $market_price*0.8;
	$max = $market_price*1.2;

	for($i=$min; $i<=$max; $i+=0.25) {
		$pnl["$i"] = $i*100;
	}
	return $pnl;
}
function long_stock($purchase, $pnl, $quantity) {
	
	//now do actual pnl calculation
	foreach($pnl as $k=>$v) {
		$pnl[$k] = $v - ($puchase*$quantity);
	}
	
	return $pnl;
}
function put_option($premium, $pnl, $buysell, $strike, $quantity) {
	
	foreach($pnl as $k=>$v) {
		if($buysell == 'buy') {
			if($strike > $k) {
				//excerise
				$pnl[$k] = 0 - $v + ($strike*$quantity*100) - $premium;
			}
			else {
				//no excerise, just lose premium
				$pnl[$k] = 0 - $premium;
			}
			
		}
		elseif($buysell == 'sell') {
			if($strike > $k) {
				//excerised
				$pnl[$k] = 0 - ($strike*$quantity*100) + $v + $premum;
			}
			else {
				//no excerise, just earn premium
				$pnl[$k] = ($premium);
			}
		}
	}
	
	return $pnl;
}
function call_option($premium, $pnl, $buysell, $strike, $quantity) {
	
	foreach($pnl as $k=>$v) {
		if($buysell == 'buy') {
			if($strike < $k) {
				//excerise
				$pnl[$k] = ($v - ($strike*100*$quantity)) - $premium;
			}
			else {
				//no excerise, just lose premium
				$pnl[$k] = 0 - $premium;
			}
			
		}
		elseif($buysell == 'sell') {
			if($strike < $k) {
				//excerised
				$pnl[$k] = 0 - $v + ($strike*$quantity*100) + $premium;
			}
			else {
				//no excerise, just earn premium
				$pnl[$k] = ($premium);
			}
		}
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
				<p>Market Share Price: <input type="text" name="market_price" size="5" value="<?php echo isset($_POST['market_price']) ? $_POST['market_price'] : '' ?>"></p>
				<p>Long stock price: <input type="text" name="stock_purchase" size="5" value="<?php echo isset($_POST['stock_purchase']) ? $_POST['stock_purchase'] : '' ?>">Long stock quanity: <input type="text" name="stock_purchase_size" size="5" value="<?php echo isset($_POST['stock_purchase_size']) ? $_POST['stock_purchase_size'] : '' ?>"></p>
				<p>
				Leg 1: <select name="leg1_buy_sell">
								<option value="buy" <?php if($_POST['leg1_buy_sell'] == 'buy') { echo "selected"; } ?>>Buy</option>
								<option value="sell" <?php if($_POST['leg1_buy_sell'] == 'sell') { echo "selected"; } ?>>Sell</option>
						   </select>
						   <select name="leg1_call_put">
								<option value="call" <?php if($_POST['leg1_call_put'] == 'call') { echo "selected"; } ?>>Call</option>
								<option value="put" <?php if($_POST['leg1_call_put'] == 'put') { echo "selected"; } ?>>Put</option>
						   </select>
				Strike Price: <input type="text" name="leg1_strike" size="5" value="<?php echo isset($_POST['leg1_strike']) ? $_POST['leg1_strike'] : ''?>">
				Purchase Price: <input type="text" name="leg1_purchase" size="5" value="<?php echo isset($_POST['leg1_purchase']) ? $_POST['leg1_purchase'] : ''?>">
				Purchase Quanity: <input type="text" name="leg1_quantity" size="5" value="<?php echo isset($_POST['leg1_quantity']) ? $_POST['leg1_quantity'] : ''?>">
				</p>
				<p>
				Leg 2: <select name="leg2_buy_sell">
								<option value="buy" <?php if($_POST['leg2_buy_sell'] == 'buy') { echo "selected"; } ?>>Buy</option>
								<option value="sell" <?php if($_POST['leg2_buy_sell'] == 'sell') { echo "selected"; } ?>>Sell</option>
						   </select>
						   <select name="leg2_call_put">
								<option value="call" <?php if($_POST['leg2_call_put'] == 'call') { echo "selected"; } ?>>Call</option>
								<option value="put" <?php if($_POST['leg2_call_put'] == 'put') { echo "selected"; } ?>>Put</option>
						   </select>
				Strike Price: <input type="text" name="leg2_strike" size="5" value="<?php echo isset($_POST['leg2_strike']) ? $_POST['leg2_strike'] : ''?>">
				Purchase Price: <input type="text" name="leg2_purchase" size="5" value="<?php echo isset($_POST['leg2_purchase']) ? $_POST['leg2_purchase'] : ''?>">
				Purchase Quanity: <input type="text" name="leg2_quantity" size="5" value="<?php echo isset($_POST['leg2_quantity']) ? $_POST['leg2_quantity'] : ''?>">
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
