<?php
class Reply
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo->connect();
    }

    // test input data for hack commands
    public function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function addReply($newRequest, $ref)
    {
        $request = $newRequest;
        $text = null;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $text = $this->test_input($request->getPost("replytext"));
        }

        $sql = "INSERT INTO replies (ref, text) VALUES (?,?);";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ref, $text]);
    }

    public function findByRef($reference)
    {
        $sql = "SELECT * FROM replies WHERE ref='$reference';";
        $ticket = $this->pdo->query($sql);
        return $ticket;
    }

    public function deleteReplies($ref){
        $sql = "DELETE FROM replies WHERE ref = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ref]);
    }

}

?>