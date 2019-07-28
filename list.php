<?php
    session_start();

    require("inc/header.inc.php");
    require('controller.php');

    // unset sessions from the other pages if this one is visited
    if(isset($_SESSION["view"])){
        unset($_SESSION["view"]);
    }
    if(isset($_SESSION["viewRef"])){
        unset($_SESSION["viewRef"]);
    }
    if(isset($_SESSION["reference"])){
        unset($_SESSION["reference"]);
    }

    if(isset($_POST["status"]))
    {
        $postStatus = $request->getPost("status");
    }

    if(isset($_GET["reference"]))
    {
        $getRef = $request->getQuery("reference");
        // set ticket list based on status and get reference given by delete or update
        $ticketList = getListStatus($ticketManager, $postStatus, $ticketList);
    }
    else
    {
        // set ticket list based on status
        $ticketList = getListStatus($ticketManager, $postStatus, $ticketList);
    }

    if(isset($_GET["status"]))
    {
        $getStatus = $request->getQuery("status");
        // update ticket if get status is provided by update buttons
        updateTicketStatus($ticketManager, $getStatus, $getRef);
    } else {
        // make sure delete ticket won't be called at the same time with update
        deleteTicket($ticketManager, $replyManager, $request, $getRef, $postStatus);
    }



?>

<main>
    <div class="container">
        <br>
        <h1 class="text-center ">Ticket List</h1>
        <?php
            displayOperationSuccessfully();
        ?>
        <hr class="my-4">
        <form action="list.php" method="POST">
        <div class="form-group row">
            <div class="col-1">
            <label for="status">Status: </label>
            </div>
            <div class="col-3">
            <select class="form-control" name="status" id="status">
                <option value="open">Open</option>
                <option value="closed">Closed</option>
            </select>
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-primary">Show List</button>
            </div>
            <div class="col-6">
                <?php
                    displayStatusMode($postStatus);
                ?>
            </div>
        </div>
        </form>
        <table class="table" id="ticketList">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Email</th>
                <th scope="col">Subject</th>
                <th scope="col">Department</th>
                <th scope="col">Date</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
                addListTickets($ticketList);
            ?>
            </tbody>
        </table>
    </div>
</main>

<?php
    require("inc/footer.inc.php");
?>
