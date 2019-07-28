<?php
class Ticket
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo->connect();
    }

    // function for the random unique string
    function getUStr()
    {
        $length = 6;
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }
        return $token;
    }

    // test input data for hack commands
    public function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // only allow letters, numbers and white space for subject
    public function formSubjectValidate($subject)
    {
        if (!ctype_alnum($subject)) {
            echo "<p class='alert alert-danger text-center'> Only letters, numbers and white space allowed on subject input.</p>";
            return null;
        }
        return $subject;
    }

    public function addTicket($newRequest)
    {
        $request = $newRequest;
        $email = $department = $subject = $tickettext = null;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $this->test_input($request->getPost("email"));
            $department = $this->test_input($request->getPost("department"));
            $subject = $this->test_input($request->getPost("subject"));
            $tickettext = $this->test_input($request->getPost("tickettext"));
        }

        if($this->formSubjectValidate($subject) != null)
        {
            $date = date('d/m/Y H:i');
            $ref = $this->getUStr();
            $sql = "INSERT INTO tickets (ref, email, department, subject, tickettext, date) 
                    VALUES (?,?,?,?,?,?);";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$ref, $email, $department, $subject, $tickettext, $date]);
            return $ref;
        } else {
            $_SESSION["success"] = false;
        }
    }

    public function findByRef($reference)
    {
        $sql = "SELECT * FROM tickets WHERE ref = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$reference]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ticket;
    }

    public function deleteTickets($ref){
        $sql = "DELETE FROM tickets WHERE ref = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ref]);
    }

    public function getOpenTickets()
    {
        $sql = "SELECT * FROM tickets WHERE status = 1;";
        $tickets = $this->pdo->query($sql);
        return $tickets;
    }

    public function getClosedTickets()
    {
        $sql = "SELECT * FROM tickets WHERE status = 0;";
        $tickets = $this->pdo->query($sql);
        return $tickets;
    }

    public function updateTicketStatus($status, $ref)
    {
        $sql = "UPDATE tickets SET status = ? WHERE ref = ?; ";
        $stmt = $this->pdo->prepare($sql);
        if($status == 1)
        {
            $stmt->execute([0,$ref]);
        } else {
            $stmt->execute([1,$ref]);
        }
    }
}

?>