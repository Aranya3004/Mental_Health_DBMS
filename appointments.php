<?php

$appointments = [
    ['name' => 'Alice', 'doctor' => 'Dr. Khan', 'date' => '2025-12-22', 'time' => '10:00 AM'],
    ['name' => 'Bob', 'doctor' => 'Dr. Rahman', 'date' => '2025-12-23', 'time' => '02:00 PM']
];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newAppointment = [
        'name' => htmlspecialchars($_POST['name']),
        'doctor' => htmlspecialchars($_POST['doctor']),
        'date' => htmlspecialchars($_POST['date']),
        'time' => htmlspecialchars($_POST['time'])
    ];
    // Simulate saving
    $appointments[] = $newAppointment;
    $message = "Appointment booked successfully for " . $newAppointment['name'] . " with " . $newAppointment['doctor'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments - AI Mental Healthcare System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        form { max-width: 500px; margin-bottom: 20px; }
        input, select { width: 100%; padding: 8px; margin: 6px 0; }
        input[type="submit"] { width: auto; background-color: #4CAF50; color: white; border: none; cursor: pointer; padding: 10px 20px; }
        input[type="submit"]:hover { background-color: #45a049; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .message { background-color: #e7f3fe; padding: 10px; margin-bottom: 15px; border-left: 5px solid #2196F3; }
    </style>
</head>
<body>

<h2>Appointments Module</h2>
<p>Book your therapy session with our doctors or counsellors.</p>

<?php if(!empty($message)) : ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="post" action="">
    <label for="name">Your Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="doctor">Select Doctor:</label>
    <select id="doctor" name="doctor" required>
        <option value="Dr. Khan">Dr. Khan</option>
        <option value="Dr. Rahman">Dr. Rahman</option>
        <option value="Dr. Ahmed">Dr. Ahmed</option>
    </select>

    <label for="date">Date:</label>
    <input type="date" id="date" name="date" required>

    <label for="time">Time:</label>
    <input type="time" id="time" name="time" required>

    <input type="submit" value="Book Appointment">
</form>

<h3>Scheduled Appointments</h3>
<table>
    <tr>
        <th>Name</th>
        <th>Doctor</th>
        <th>Date</th>
        <th>Time</th>
    </tr>
    <?php foreach($appointments as $appt) : ?>
    <tr>
        <td><?php echo $appt['name']; ?></td>
        <td><?php echo $appt['doctor']; ?></td>
        <td><?php echo $appt['date']; ?></td>
        <td><?php echo $appt['time']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>