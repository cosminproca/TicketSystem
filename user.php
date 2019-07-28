<?php
    session_start();

    require("inc/header.inc.php");
    require("controller.php");

    if(isset($_POST["email"]))
    {
        $postEmail = $request->getPost("email");
        // call add ticket here so it won't be called incorrectly
        addTicket($ticketManager, $request, $postEmail);
    }

    // unset sessions from the other pages if this one is visited
    if(isset($_SESSION["view"])){
        unset($_SESSION["view"]);
    }
    if(isset($_SESSION["delete"])){
        unset($_SESSION["delete"]);
    }
    if(isset($_SESSION["update"])){
        unset($_SESSION["update"]);
    }
?>

<main>
<!-- Ticket Form --->
<div class="container-fluid">
    <div class="row">
    <div class="col-1"></div>
    <div class="col-5">
    <br>
    <h1>Ticket Form</h1>
	<hr class="my-4">
	<form action="user.php" class="needs-validation" method="POST">
		<div class="form-group">
		    <label for="ticketEmail">Email address</label>
		    <input type="email" class="form-control" id="ticketEmail"
                   name="email" placeholder="Enter email" required>
            <div class="invalid-feedback">
                Please provide a valid email.
            </div>
		</div>
		<div class="form-group">
    		<label for="chooseDepartment">Choose Department</label>
    		<select class="form-control" name="department" id="chooseDepartment">
			    <option id="TD" value="Technical Department">Technical Department</option>
			    <option id="SD" value="Sales Department" >Sales Department</option>
   			 </select>
  		</div>
  		<div class="form-group">
    		<label for="ticketSubject">Ticket Subject</label>
    		<input type="text" name="subject" class="form-control" id="ticketSubject" placeholder="Enter ticket subject" required>
            <div class="invalid-feedback">
                Please provide a subject.
            </div>
        </div>
  		<div class="form-group">
    		<label for="ticketText">Ticket Text</label>
    		<textarea class="form-control" name="tickettext" id="ticketText" placeholder="Enter ticket text" rows="5" required></textarea>
            <div class="invalid-feedback">
                Please provide a text.
            </div>
        </div>
  		<button type="submit" name="sendTicket"  class="btn btn-primary">Send Ticket</button>
	</form>
    <?php
        displaySuccess();
    ?>
    </div>

    <div class="col-5">
    <br>
    <h1>View ticket</h1>
    <hr class="my-4">
    <form action="view.php" method="GET">
        <div class="form-group">
            <label for="referenceCode">Reference Code</label>
            <input type="text" name="reference" class="form-control" id="referenceCode" placeholder="Enter ticket subject" required>
        </div>
        <button type="submit" class="btn btn-primary">View Ticket</button>
    </form>
    </div>
    <div class="col-1"></div>
    </div>
</div>
</main>

<?php
    require("inc/footer.inc.php");
?>
