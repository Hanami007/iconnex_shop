<?php
$ch = curl_init('http://localhost:8000/cart_handler.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "action=add&course_id=1");
$response = curl_exec($ch);
curl_close($ch);
echo "Response: " . $response . "\n";
?>
