<?php
session_start();

$cards = array(1, 2, 3, 4, 5, 6, 7, 8);
$cards = array_merge($cards, $cards); // duplicate the cards for pairs

// Reset the game or initialize the game state
if (!isset($_SESSION['cards']) || isset($_POST['reset'])) {
    shuffle($cards);
    $_SESSION['cards'] = $cards;
    $_SESSION['flipped'] = [];
    $_SESSION['matched'] = [];
    $_SESSION['attempts'] = 0;
    $_SESSION['matches'] = 0;
}

// Handle card flipping
if (isset($_POST['flip'])) {
    $index = $_POST['flip'];
    if (!in_array($index, $_SESSION['matched']) && !in_array($index, $_SESSION['flipped']) && count($_SESSION['flipped']) < 2) {
        $_SESSION['flipped'][] = $index; // Flip the card
    }
}

// After two cards have been flipped, check for a match
if (count($_SESSION['flipped']) == 2) {
    $flipped_cards = array_map(function ($index) {
        return $_SESSION['cards'][$index];
    }, $_SESSION['flipped']);
    
    if ($flipped_cards[0] == $flipped_cards[1]) {
        $_SESSION['matched'] = array_merge($_SESSION['matched'], $_SESSION['flipped']);
        $_SESSION['matches']++;
    } else {
        // If no match, reset flipped cards after a short delay
        // Use output buffering and a redirect for a short delay effect.
        header("Refresh: 1; url=index.php"); // refresh after 1 second
    }
    // Reset the flipped cards array
    $_SESSION['flipped'] = [];
    $_SESSION['attempts']++;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Memory Game</h1>
    <p>Attempts: <?php echo $_SESSION['attempts']; ?></p>
    <p>Matches: <?php echo $_SESSION['matches']; ?>/8</p>

    <form method="POST">
        <button type="submit" name="reset" class="reset-btn">Reset Game</button>
    </form>

    <div class="board">
        <?php
        foreach ($_SESSION['cards'] as $index => $card) {
            $is_flipped = in_array($index, $_SESSION['flipped']) || in_array($index, $_SESSION['matched']);
            echo "<form method='POST' class='card'>";
            echo "<button type='submit' name='flip' value='$index' class='card-button'";
            if ($is_flipped) {
                echo " disabled";
            }
            echo ">";
            // Only show the card number if it's flipped or matched
            echo $is_flipped ? $card : '';
            echo "</button>";
            echo "</form>";
        }
        ?>
    </div>

</div>

</body>
</html>
