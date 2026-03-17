<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['Name']);
    $email = htmlspecialchars($_POST['Email']);
    $feedback = htmlspecialchars($_POST['feedback']);

    
    $savedMessage = "Thank you, $name. Your feedback has been submitted successfully!" ;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feedback - AI Mental Healthcare System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        form { max-width: 500px; }
        input, textarea { width: 100%; padding: 8px; margin: 6px 0; }
        input[type="submit"] { width: auto; background-color: #4CAF50; color: white; border: none; cursor: pointer; padding: 10px 20px; }
        input[type="submit"]:hover { background-color: #45a049; }
        .message { background-color: #e7f3fe; padding: 10px; margin-bottom: 15px; border-left: 5px solid #2196F3; }
    </style>
</head>
<body>

    <h2>Feedback Module</h2>
    <p>We value your feedback to improve our AI Mental Healthcare System.</p>

    <?php if (!empty($savedMessage)) : ?>
        <div class="message"><?php echo $savedMessage; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="feedback">Feedback:</label>
        <textarea id="feedback" name="feedback" rows="5" required></textarea>

        <input type="submit" value="Submit Feedback">
    </form>

</body>
</html>