<html>
<head>
    <title>Buy cool new product</title>
</head>
<body>
<!-- Use action="/create-checkout-session.php" if your server is PHP based. -->
<form action="/create-checkout-session" method="POST">
    @csrf
    <button type="submit">Checkout</button>
</form>
<form action="/add" method="POST">
    @csrf
    <button type="submit">Checkout</button>
</form>
<form action="/api/subscribe" method="POST" >
    @csrf
    <button type="submit">Checkout</button>
</form>
</body>
</html>
