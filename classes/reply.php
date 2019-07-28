<?php
class Reply
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo->connect();
    }

    public function addReply($newRequest, $ref)
    {
        $request = $newRequest;
        $text = $request->getPost("replytext");
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