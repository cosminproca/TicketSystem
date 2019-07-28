<?php
    session_start();

	require('inc/header.inc.php');
    require('controller.php');

    // set session based on which page you're visiting from
    setViewSession();

    // unset sessions from the other pages if this one is visited
    if(isset($_SESSION["ref"])){
        unset($_SESSION["ref"]);
    }
    if(isset($_SESSION["reference"])){
        unset($_SESSION["reference"]);
    }

    if(isset($_GET["reference"]))
    {
        $getRef = $request->getQuery("reference");
        // set lists here so they won't be called erroneously
        $viewList = getViewTicket($ticketManager, $request, $getRef);
        $replyList = getReplyTickets($replyManager, $request, setViewRef($request, $getRef));
    }

    if(isset($_POST["replytext"]))
    {
        $postReplyText = $request->getPost("replytext");
        // call add reply function here so it won't be called incorrectly
        addReply($replyManager, $request, $postReplyText);
    }
?>

<main>
<div class="container-fluid">
    <div class="row">
    <div class="col-1"></div>
    <div class="col-6">
    <br>
    <?php
        // check if user visited from neither user, list or view pages or hasn't given a valid reference and redirect
        if(isset($getRef))
        {
            displayViewTicket($viewList, $replyList, $getRef);
        }
        else
        {
            echo "<h1 class='text-center'>Ticket Error</h1>";
            displayViewMode($viewList);
        }
    ?>

    </div>
    </div>
    <div class="col-1"></div>
</div>
</main>	

<?php
	require('inc/footer.inc.php');
?>