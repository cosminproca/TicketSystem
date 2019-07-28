<?php
    require("classes/DB.php");
    require("classes/request.php");
    require("classes/ticket.php");
    require("classes/reply.php");

    /*  DB tables: tickets, replies
        tickets columns: id, ref, department, subject, tickettext, date, status
        replies columns: id, ref, text
    */

    /*  $GLOBALS used:
        $_SESSION["view"]: used to check from which page the user came after replies
                                to maintain the different colors based on the page
        $_SESSION["viewRef"]: used for the view page after the user adds new replies

        $_SESSION["reference"]: used in the user page to display the reference after
                                adding the ticket
        $_SESSION["success"]: used to determine if the ticket was sent successfully

        $_SESSION["delete"]: used to display success an message after deleting a ticket

        $_SESSION["update"]: used to display update an message after updating a ticket

        $_SERVER["HTTP_REFERER"]: used to check from where the user visited the page
    */

    // initialize connections
    $db = new DB("localhost", "root", "",
        "ticketsystem", "utf8mb4");
    $request = new Request();
    $ticketManager = new Ticket($db);
    $replyManager = new Reply($db);

    // variable to check if post email is set
    $postEmail = null;

    // variable to get if post reply text is set
    $postReplyText = null;

    // variable to get if post status is set
    $postStatus = null;

    // variable to check if status get variable is set
    $getStatus = null;

    // variable to check if reference get variable is set
    $getRef = null;

    // variable to hold list tickets
    $ticketList = array();

    // variable to hold view ticket data
    $viewList = array();

    // variable to hold reply ticket data
    $replyList = array();

    // set view session after vising view page
    function setViewSession()
    {
        if(isset($_SERVER["HTTP_REFERER"]))
        {
            if($_SERVER["HTTP_REFERER"] == "http://localhost/TicketSystem/user.php")
            {
                $_SESSION["view"] = "user";
            }
            elseif($_SERVER["HTTP_REFERER"] == "http://localhost/TicketSystem/list.php")
            {
                $_SESSION["view"] = "list";
            }
        }
    }

    // set view reference after adding replies and refreshing the page
    function setViewRef($request, $getRef)
    {
            if(isset($getRef))
            {
                $_SESSION["viewRef"] = $request->getQuery("reference");
            }

            return $_SESSION["viewRef"];
    }

    // display success message and remove success session after showing alert
    function displaySuccess()
    {
        if(isset($_SESSION["success"]))
        {
            if($_SESSION["success"] == true)
            {
                echo "<br>";
                echo "
                    <div class='alert alert-success' role='alert'>
                    <h3 class='alert-heading'>Ticket Sent Successfully!</h3>
                    <hr>
                    <p>Reference: {$_SESSION["reference"]} </p> 
                    </div> ";
                unset($_SESSION["success"]);
            } else {
                echo "<br>";
                echo "
                    <div class='alert alert-danger' role='alert'>
                    <h3 class='alert-heading text-center'>Form Validate Error. Please enter valid data.</h3>
                    </div> ";
                unset($_SESSION["success"]);
            }
        }
    }

    // display success on successfull delete or update operations
    function displayOperationSuccessfully()
    {
        if(isset($_SESSION["delete"]))
        {
            echo "
                <div class='alert alert-success text-center w-50 mx-auto' role='alert'>
                    Ticket Deleted Successfully!
                </div>";
        }
        elseif(isset($_SESSION["update"]))
        {
            echo "
                <div class='alert alert-success text-center w-50 mx-auto' role='alert'>
                    Ticket Status Updated Successfully!
                </div>";
        }
    }

    // display list based on status
    function displayStatusMode($postStatus){
        if(isset($postStatus)){
            if($postStatus == "open")
            {
                echo "<h6 class='alert alert-success text-center'>You are viewing the Open Tickets List</h6>" ;
            }
            elseif ($postStatus == "closed")
            {
                echo "<h6 class='alert alert-dark text-center'>You are viewing the Closed Tickets List</h6>" ;
            }

            // unset delete and update after they're shown
            if(isset($_SESSION["delete"])){
                unset($_SESSION["delete"]);
            }
            if(isset($_SESSION["update"])){
                unset($_SESSION["update"]);
            }
        } else {
            echo "<h6 class='alert alert-success text-center'>You are viewing the Open Tickets List</h6>" ;
        }
    }

    // display ticket depending on where you've accessed the view page or redirect if no reference found
    function displayViewMode($viewList)
    {
        // check for viewList to redirect ticket if needed
        if(isset($_SERVER["HTTP_REFERER"]) && $viewList!=null)
        {
            if($_SERVER["HTTP_REFERER"] == "http://localhost/TicketSystem/user.php")
            {
                echo "<h3 class='alert alert-success text-center'> You are viewing the ticket in User Mode. </h3>";
            }
            elseif($_SERVER["HTTP_REFERER"] == "http://localhost/TicketSystem/list.php")
            {
                echo "<h3 class='alert alert-primary text-center'> You are viewing the ticket in Admin Mode. </h3>";
            }
            elseif($_SESSION["view"]=="user")
            {
                echo "<h3 class='alert alert-success text-center'> You are viewing the ticket in User Mode. </h3>";
            }
            elseif($_SESSION["view"]=="list")
            {
                echo "<h3 class='alert alert-primary text-center'> You are viewing the ticket in Admin Mode. </h3>";
            }
            return true;
        }
        else
        {
            echo "<h3 class='alert alert-danger text-center'> Ticket not found. Redirecting... </h3>";
            header('Refresh: 3; URL=/TicketSystem/user.php');
            return false;
        }
    }

    // display replies in chronological order
    function displayReplies($replyList)
    {
        foreach($replyList as $reply)
        {
            if(isset($_SERVER["HTTP_REFERER"]))
            {
                if ($_SERVER["HTTP_REFERER"] == "http://localhost/TicketSystem/user.php")
                {
                    echo "<div class='alert alert-success w-50 p-3 mr-auto'> Reply: {$reply["text"]}</div>";
                }
                elseif ($_SERVER["HTTP_REFERER"] == "http://localhost/TicketSystem/list.php")
                {
                    echo "<div class='alert alert-primary w-50 p-3 ml-auto'> Reply: {$reply["text"]}</div>";
                }
                elseif($_SESSION["view"]=="user")
                {
                    echo "<div class='alert alert-success w-50 p-3 mr-auto'> Reply: {$reply["text"]}</div>";
                }
                elseif($_SESSION["view"]=="list")
                {
                    echo "<div class='alert alert-primary w-50 p-3 ml-auto'> Reply: {$reply["text"]}</div>";
                }
            }
        }

    }

    // display ticket based on view mode
    function displayViewTicket($viewList, $replyList, $getRef)
    {
        echo "<h1 class='text-center'>Ticket #{$viewList["ref"]}</h1>";
        // if method returns true display data if not display error and redirect
        if(displayViewMode($viewList))
        {
            echo "
            <ul class='list-group list-group-horizontal'> 
            <li class='list-group-item'>Email: {$viewList["email"]}</li>
            <li class='list-group-item'>Subject: {$viewList["subject"]}</li>
            <li class='list-group-item'>Department: {$viewList["department"]}</li>
            <li class='list-group-item'>Status: {$viewList["status"]}</li> 
            <li class='list-group-item'>Date: {$viewList["date"]}</li>
            </ul>
            <br>
            <p class='alert alert-warning'> Text: {$viewList["tickettext"]}</p>";

            displayReplies($replyList);
            echo "
            <h3>Reply</h3>
            <br>";
            displayReplyForm($viewList, $getRef);
        }
        else
        {
            echo "<p class='alert alert-danger text-center'> Error. Please use the Send Ticket page or the Ticket List page. Redirecting... </p>";
        }
    }

    // check if ticket status is closed or open, it shows a message if it is closed and remove the reply form
    function displayReplyForm($viewList, $getRef)
    {
        if(isset($getRef))
        {
            if($viewList["status"] == 0)
            {
                echo "<div class='alert alert-dark w-50' role='alert'>
                            Ticket is closed, replying is not available.
                      </div>";
            }
            else {
                echo " 
                            <form action='view.php' class='needs-validation' method='POST'>
                                <div class='form-group'>
                                    
                                    <textarea class='form-control' name='replytext'  placeholder='Enter reply text' rows='5' required></textarea>
                                </div>
                            <button type='submit' name='sendReply'  class='btn btn-primary'>Send Reply</button>
                            </form>
                    ";
            }
        }
    }

    // set view list
    function getViewTicket($ticketManager, $request, $getRef)
    {
        $viewList = null;
        if(isset($getRef)) {
            $viewList = $ticketManager->findByRef($request->getQuery("reference"));
        }
        return $viewList;
    }

    // set reply list
    function getReplyTickets($replyManager, $request, $getRef)
    {
        $replyList = null;
        if(isset($getRef))
        {
            $replyList = $replyManager->findByRef($request->getQuery("reference"));
        }
        return $replyList;
    }

    // set ticket list based on status
    function getListStatus($ticketManager, $postStatus, $newTicketList)
    {
        $ticketList = $newTicketList;
        if(isset($postStatus))
        {
            if($postStatus == "open")
            {
                $ticketList = $ticketManager->getOpenTickets();
            }
            elseif($postStatus == "closed")
            {
                $ticketList = $ticketManager->getClosedTickets();
            }
        }
        else
        {
            $ticketList = $ticketManager->getOpenTickets();
        }
        return $ticketList;
    }

    // add reply to db and redirect
    function addReply($replyManager, $request, $postReplyText)
    {
        if(isset($postReplyText))
        {
            $replyManager->addReply($request, $_SESSION["viewRef"] );
            header("location: /TicketSystem/view.php/?reference={$_SESSION["viewRef"]}");
        }
    }

    // add ticket to db and redirect
    function addTicket($ticketManager, $request, $postEmail)
    {
        if(isset($postEmail))
        {
            if($request->getPost("email"))
            {
                $_SESSION["success"] = true;
                $_SESSION["reference"] = $ticketManager->addTicket($request);
            }
        }
    }

    // update status after clicking button and redirect
    function updateTicketStatus($ticketManager, $getStatus, $getRef)
    {
        if(isset($getStatus))
        {
            $ticketManager->updateTicketStatus($getStatus, $getRef);
            $_SESSION["update"] = "update";
            header('location: /TicketSystem/list.php');
        }
    }

    // delete ticket after clicking button and redirect
    function deleteTicket($ticketManager, $replyManager, $request, $getRef, $postStatus)
    {
        if(isset($getRef) && !isset($postStatus))
        {
            $ticketManager->deleteTickets($request->getQuery("reference"));
            $replyManager->deleteReplies($request->getQuery("reference"));
            $_SESSION["delete"] = "delete";
            header('location: /TicketSystem/list.php');
        }
    }

    // show ticket list
    function addListTickets($ticketList)
    {
        foreach($ticketList as $ticket)
        {
            echo "
                <tr>
                    <th scope='row'>{$ticket["id"]}</th>
                    <td>{$ticket["email"]}</td>
                    <td>{$ticket["subject"]}</td>
                    <td>{$ticket["department"]}</td>
                    <td>{$ticket["date"]}</td>
                    <td>
                    <div class='btn-group'>
                        <button class='btn btn-secondary dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                            Actions
                        </button>
                        <ul class='dropdown-menu'>
                            <li>
                                <a class='dropdown-item clearfix' href='/TicketSystem/view.php?reference={$ticket["ref"]}'>
                                    View
                                </a>
                            </li>
                            <li>
                                <a class='dropdown-item' onClick=\"return confirm('Are you sure you want to delete this?');\" href='/TicketSystem/list.php?reference={$ticket["ref"]}'>
                                    Delete
                                </a>
                            </li>
                            <li class='dropdown-divider'></li>
                            <li class='dropdown-submenu'>
                                <a class='dropdown-item' tabindex='-1' href='#'>Change Status</a>
                                <ul class='dropdown-menu'>
                                    <li><a class='dropdown-item' onClick=\"return confirm('Are you sure you want to change status?');\" 
                                    tabindex='-1' href='/TicketSystem/list.php?reference={$ticket["ref"]}&status={$ticket["status"]}'>Open</a></li>
                                    <li><a class='dropdown-item' onClick=\"return confirm('Are you sure you want to change status?');\" 
                                    tabindex='-1' href='/TicketSystem/list.php?reference={$ticket["ref"]}&status={$ticket["status"]}'>Closed</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    </td>
                </tr>
                " ;
        }
    }

?>