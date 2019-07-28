<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ticket System</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="/TicketSystem/css/style.css" rel="stylesheet">
    <script>
        $(".dropdown-toggle").on("click", function () {
            // make sure it is not shown:
            if (!$(this).parent().hasClass("show")) {
                $(this).click();
            }
        });
        $(".btn-group, .dropdown").on("mouseleave", function () {
            // make sure it is shown:
            if ($(this).hasClass("show")){
                $(this).children('.dropdown-toggle').first().click();
            }
        });
    </script>
</head>

<body>
<!-- Navigation -->
<header>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
	<div class=container-fluid>
		<button class="navbar-toggler" type="button" data-toggle="collapse" 
		data-target="#navbarResponsive">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
			<ul class="navbar-nav text-center">
				<li class="nav-item">
					<a class="nav-link active" href="/TicketSystem/user.php">Send Ticket</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/TicketSystem/list.php">Ticket List</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/TicketSystem/view.php">View Ticket</a>
				</li>
			</ul>
		</div>
	</div>
</nav>
</header>

