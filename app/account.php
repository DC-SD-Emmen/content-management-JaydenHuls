<?php
ob_start(); // Start output buffering
session_start(); // start een sessie in deze pagina
require 'classes/databaseConnection.php';  // De Databaseconnection-klasse
require 'classes/userdata.php';      // De userdata-klasse

$database = new Database();
$user = new User($database);

// Controleer of de gebruiker is ingelogd en als dat niet zo is stuurt hij de gebruiker naar de loginpagina
// dit zorgt ervoor dat alleen gebruikers die zijn ingelogd deze pagina kunnen zien
if (!isset($_SESSION['user_id'])) {
    header("Location: loginpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_data = $user->getUser($user_id);

// controleert of de form is verstuurt via een post methode en het checkt of de update knop is aangeklikt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // door de userdata-klasse aan te roepen, kan de gebruiker zijn gegevens wijzigen
    // de user_id controleert de gegevens van de gebruiker die is ingelogd
    // als de new username en new password anders zijn dan de huidige gegevens, dan kan de gebruiker zijn gegevens wijzigen
    // de current password is om te controleren of de gebruiker het juiste huidige wachtwoord heeft ingevoerd
    $message = $user->updateUser(
        $user_id,
        $_POST['new_username'],
        $_POST['new_password'],
        $_POST['current_password']
    );
    // als de gegevens zijn gewijzigd, dan wordt er een message gegeven dat de gegevens zijn gewijzigd
    echo "<p>$message</p>";
}

// controleert of de form is verstuurt via een post methode en het checkt of de delete knop is aangeklikt
// als de delete knop is aangeklikt, dan wordt de gebruiker verwijderd uit de database
// de sessie wordt afgebroken en de gebruiker word naar de loginpagina gestuurd
// de exit zorgt ervoor dat er na deze code geen code meer kan worden uitgevoerd
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    if ($user->deleteUser($user_id)) {
        session_destroy();
        header("Location: loginpage.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <title>Account Beheer</title>
</head>
<body>
    <!-- Gebruikersnaam tonen (met foutafhandeling als er geen gebruikersnaam is) -->
    <h2>Welkom, <?php echo htmlspecialchars($user_data['Username'] ?? "gebruiker"); ?>!</h2>

    <!-- Formulier voor wijzigen van gebruikersnaam en wachtwoord -->
    <h3>Gebruikersnaam & Wachtwoord wijzigen</h3>
    <form id="update-form" method="post">
        Nieuwe gebruikersnaam: <input type="text" name="new_username" required><br>
        Nieuw wachtwoord: <input type="password" name="new_password" required><br>
        Huidig wachtwoord: <input type="password" name="current_password" required><br>
        <button type="submit" name="update">Wijzig gegevens</button>
    </form>

    <!-- Formulier voor account verwijderen -->
    <h3>Account verwijderen</h3>
    <form id="delete-form" method="post">
        <button type="submit" name="delete" onclick="return confirm('Weet je zeker dat je je account wilt verwijderen?')">Verwijder mijn account</button>
    </form>

    <!-- Knop om terug te keren naar index.php -->
    <h3>Terug naar Home</h3>
    <form id="home-form" method="get" action="index.php">
        <button type="submit">Ga terug naar Home</button>
    </form>
</body>
</html>

<?php
ob_end_flush(); // Stuur de output naar de browser
