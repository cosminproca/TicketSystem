# TicketSystem
Ticket System

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
