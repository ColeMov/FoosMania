<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>All-Time Foosball</title>
        <style>
        body {
            background-color: darkgreen;
            font-family: Arial, sans-serif;
            margin: 20px;
            color: white;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th,td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .redHead {
            color: white;
            background-color: darkred;
        }

        .blueHead {
            color: white;
            background-color: darkblue;
        }

        form {
            margin-top: 20px;
        }

        input {
            padding: 5px;
        }
    </style>
</head>
<body>

<?php
// Function to update leaderboard data
function updateLeaderboard($winner, $loser)
{
    $leaderboardFile = 'leaderboard.json';
    $leaderboard = [];

    // Check if the leaderboard file exists and is not empty
    if (file_exists($leaderboardFile) && filesize($leaderboardFile) > 0) {
        $leaderboard = json_decode(file_get_contents($leaderboardFile), true);
    }

    // Update or add winner's data
    if (array_key_exists($winner, $leaderboard)) {
        $leaderboard[$winner]['wins']++;
    } else {
        $leaderboard[$winner] = [
            'wins' => 1,
            'losses' => 0,
        ];
    }

    // Update or add loser's data
    if (array_key_exists($loser, $leaderboard)) {
        $leaderboard[$loser]['losses']++;
    } else {
        $leaderboard[$loser] = [
            'wins' => 0,
            'losses' => 1,
        ];
    }

    // Sort the leaderboard based on win/loss ratio (descending order)
    uasort($leaderboard, function ($a, $b) {
        $ratioA = ($a['losses'] == 0) ? INF : $a['wins'] / $a['losses'];
        $ratioB = ($b['losses'] == 0) ? INF : $b['wins'] / $b['losses'];

        return $ratioB - $ratioA;
    });

    // Save updated leaderboard to a JSON file
    file_put_contents($leaderboardFile, json_encode($leaderboard, JSON_PRETTY_PRINT));

    // Display the updated leaderboard
    displayLeaderboard($leaderboard);
}

// Function to display the leaderboard
function displayLeaderboard($leaderboard)
{
    echo '<h2>Leaderboard</h2>';
    echo '<table>';
    echo '<tr><th class = redHead>Player</th><th class = blueHead >Win/Loss Ratio</th><th class = redHead >Wins</th><th class = blueHead >Losses</th></tr>';
    
    foreach ($leaderboard as $player => $stats) {
        echo '<tr>';
        echo '<td>' . $player . '</td>';
        echo '<td>' . sprintf("%.2f", ($stats['losses'] == 0) ? INF : $stats['wins'] / $stats['losses']) . '</td>';
        echo '<td>' . $stats['wins'] . '</td>';
        echo '<td>' . $stats['losses'] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get game data from the form, checking if keys exist
    $winner = isset($_POST['winner']) ? $_POST['winner'] : '';
    $loser = isset($_POST['loser']) ? $_POST['loser'] : '';

    // Update the leaderboard with the new game data
    updateLeaderboard($winner, $loser);
}
?>

<!-- Form to submit new game data -->
<h2>Enter Game Data</h2>
<form method="post">
    <label for="winner">Winner:</label>
    <input type="text" name="winner" required>
    <label for="loser">Loser:</label>
    <input type="text" name="loser" required>
    <button type="submit">Submit</button>
</form>
</body>

</html>



