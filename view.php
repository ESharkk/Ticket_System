<?php
include 'functions.php';
// Connect to MySQL using the below function
$pdo = pdo_connect_mysql();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\composer\vendor\autoload.php'; // Adjust the path to your PHPMailer autoload file

$mail = new PHPMailer(true);

// Check if the ID param in the URL exists
if (!isset($_GET['id'])) {
    exit('No ID specified!');
}
// MySQL query that selects the ticket by the ID column, using the ID GET request variable
$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
$stmt->execute([ $_GET['id'] ]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);
// Check if ticket exists
if (!$ticket) {
    exit('Invalid ticket ID!');
}


if (isset($_GET['status']) && in_array($_GET['status'], array('open', 'closed', 'resolved'))) {
    $stmt = $pdo->prepare('UPDATE tickets SET status = ? WHERE id = ?');
    $stmt->execute([ $_GET['status'], $_GET['id'] ]);
    header('Location: view.php?id=' . $_GET['id']);
    //exit;
}

// Check if the comment form has been submitted
if (isset($_POST['msg']) && !empty($_POST['msg'])) {
    // Insert the new comment into the "tickets_comments" table
    $stmt = $pdo->prepare('INSERT INTO tickets_comments (ticket_id, msg, email) VALUES (?, ?, ?)');
    $stmt->execute([ $_GET['id'], $_POST['msg'], $_POST['email']]);
    header('Location: view.php?id=' . $_GET['id']);
    exit;
}

if (isset($_GET['status']) && $_GET['status'] === 'resolved') {
	$emailStmt = $pdo->prepare('SELECT email FROM tickets WHERE id = ?');
    $emailStmt->execute([$_GET['id']]);
    $row = $emailStmt->fetch(PDO::FETCH_ASSOC);
    
// Send an email only if the status is 'resolved' and email is found
    if ($row && isset($row['email'])) {
		$to = $row['email']; // Use the retrieved email address   

	try {
		$mail->SMTPDebug = 2;									
		$mail->isSMTP();											
		$mail->Host	 = 'smtp-relay.brevo.com';					
		$mail->SMTPAuth = true;							
		$mail->Username = '4enmity@gmail.com';				
		$mail->Password = 'Q52JN1P96cbtarYf';						
		$mail->SMTPSecure = 'tls';							
		$mail->Port	 = 587;

		$mail->setFrom('4enmity@gmail.com', 'Kerim');		
		$mail->addAddress($to);
		
		$mail->isHTML(true);								
		$mail->Subject = 'Ticket Resolved';
		$mail->Body =  'Your ticket has been resolved. Thank you for using our service.';
		$mail->AltBody = 'Body in plain text for non-HTML mail clients';
		$mail->send();
		echo "Mail has been sent successfully!";
		exit;
	} catch (Exception $e) {
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
   }
}

$stmt = $pdo->prepare('SELECT * FROM tickets_comments WHERE ticket_id = ? ORDER BY created DESC');
$stmt->execute([ $_GET['id'] ]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<?=template_header('Ticket')?>

<div class="content view">

	<h2><?=htmlspecialchars($ticket['title'], ENT_QUOTES)?> <span class="<?=$ticket['status']?>">(<?=$ticket['status']?>)</span></h2>

    <div class="ticket">
		<p class="email" style="Times New Roman; font-size: 15px; color: #0000FF;" ><?=htmlspecialchars($ticket['email'], ENT_QUOTES)?></p>
        <p class="created"><?=date('F dS, G:ia', strtotime($ticket['created']))?></p>
        <p class="msg"><?=nl2br(htmlspecialchars($ticket['msg'], ENT_QUOTES))?></p>
		
    </div>

    <div class="btns">
        <a href="view.php?id=<?=$_GET['id']?>&status=closed" class="btn red">Close</a>
        <a href="view.php?id=<?=$_GET['id']?>&status=resolved" class="btn">Resolved</a>
    </div>

    <div class="comments">
        <?php foreach($comments as $comment): ?>
        <div class="comment">
            <div>
                <i class="fas fa-comment fa-2x"></i>
            </div>
            <p>
				<?=htmlspecialchars($comment['email'], ENT_QUOTES)?>
                <span><?=date('F dS, G:ia', strtotime($comment['created']))?></span>
                <?=nl2br(htmlspecialchars($comment['msg'], ENT_QUOTES))?>
            </p>
        </div>
        <?php endforeach; ?>
        <form action="" method="post">
			<input name="email" type="text" name="title" placeholder="Enter your email..." id="title" required>
            <textarea name="msg" placeholder="Enter your comment..."required></textarea>
            <input type="submit" value="Post Comment">
        </form>
    </div>

</div>

<?=template_footer()?>
