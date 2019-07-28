<?php
class Ticket
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo->connect();
    }

    public function getPDO()
    {
        return $this->pdo;
    }

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

    public function addTicket($newRequest)
    {
        $request = $newRequest;
        $email = $request->getPost("email");
        $department = $request->getPost("department");
        $subject = $request->getPost("subject");
        $tickettext = $request->getPost("tickettext");
        $date = date('d/m/Y H:i');
        $ref = $this->getUStr();
        $sql = "INSERT INTO tickets (ref, email, department, subject, tickettext, date) 
                    VALUES (?,?,?,?,?,?);";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ref, $email, $department, $subject, $tickettext, $date]);
        return $ref;
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