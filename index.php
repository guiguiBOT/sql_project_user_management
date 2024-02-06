<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet SQL</title>
    <link rel="stylesheet" href="./assets/style/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Acme&family=Indie+Flower&family=Rajdhani:wght@500&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <H1>Interface de gestion des utilisateurs</H1>
    </header>
    <?php
    $servername = 'localhost';
    $username = 'root';
    $password = 'root';
    try {
        $db = new PDO("mysql:host=$servername;dbname=sql_project", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->beginTransaction();
        if (isset($_POST['delete'])) {
            $user_idToDelete = $_POST['delete'];
            $requeteSQL = $db->prepare("DELETE FROM users WHERE user_id = $user_idToDelete");
            $requeteSQL->execute();
        }
        if (isset($_POST['confirm'])) {
            $userIdToUpdate = $_POST['user_id'];
            $userNameToUpdate = $_POST['nom'];
            $userFirstnametoUpdate = $_POST['prenom'];
            $usermailToUpdate = $_POST['mail'];
            $userCodePostalToUpdate = $_POST['code_postal'];
            if (checkForm($userNameToUpdate, $userFirstnametoUpdate, $usermailToUpdate, $userCodePostalToUpdate)) {
                $requeteSQL = $db->prepare("UPDATE users SET nom = '$userNameToUpdate', prenom = '$userFirstnametoUpdate', mail = '$usermailToUpdate', code_postal = '$userCodePostalToUpdate' WHERE user_id = $userIdToUpdate");
                $requeteSQL->execute();
            }
        }
        if (isset($_POST['ajouter'])) {
            $userNameToAdd = $_POST['nom'];
            $userFirstnameToAdd = $_POST['prenom'];
            $userMailToAdd = $_POST['mail'];
            $userCodePostalToAdd = $_POST['code_postal'];
            if (checkForm($userNameToAdd, $userFirstnameToAdd, $userMailToAdd, $userCodePostalToAdd)) {
                $requeteSQL = $db->prepare("INSERT INTO users(nom, prenom, mail, code_postal) VALUES('$userNameToAdd', '$userFirstnameToAdd', '$userMailToAdd', '$userCodePostalToAdd')");
                $requeteSQL->execute();
            }
        }
        $requeteSQL = $db->prepare("SELECT * FROM users");
        $requeteSQL->execute();
        $tableauRequete = $requeteSQL->fetchAll();
        $db->commit();
        $db = null;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        $db->rollback();
    }
    ?>
    <div id="mainContainer">
        <div id="tableContainer">
            <table>
                <tr id="tableHeading">
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Mail</th>
                    <th>Code postal</th>
                </tr>
                <?php for ($i = 0; $i < count($tableauRequete); $i++) { ?>
                    <tr>
                        <form method="POST">
                            <?php
                            if (isset($_POST['update'])) {
                                if ($_POST['update'] == $tableauRequete[$i]['user_id']) { ?>
                                    <td class="tdUpdate"><input type="text" name="user_id" value="<?php echo $tableauRequete[$i]['user_id'] ?>"></td>
                                    <td class="tdUpdate"><input type="text" name="nom" value="<?php echo $tableauRequete[$i]['nom']; ?>"></td>
                                    <td class="tdUpdate"><input type="text" name="prenom" value="<?php echo $tableauRequete[$i]['prenom']; ?>"></td>
                                    <td class="tdUpdate"><input type="text" name="mail" value="<?php echo $tableauRequete[$i]['mail']; ?>"></td>
                                    <td class="tdUpdate"><input type="text" name="code_postal" value="<?php echo $tableauRequete[$i]['code_postal']; ?>"></td>
                                    <td class="tdButton">
                                        <form method="POST"><button type="submit" name="confirm">Confirmer</button></form>
                                    </td>
                                    <td class="tdButton">
                                        <form method="POST"><button type="submit" name="delete" value="<?php echo $tableauRequete[$i]['user_id'] ?>">Supprimer</button></form>
                                    </td>
                                <?php } ?>
                            <?php } else { ?>
                                <td class="idCol"><?php echo $tableauRequete[$i]['user_id'] ?></td>
                                <td class="nameCol"><?php echo $tableauRequete[$i]['nom']; ?></td>
                                <td class="firstnameCol"><?php echo $tableauRequete[$i]['prenom']; ?></td>
                                <td class="mailCol"><?php echo $tableauRequete[$i]['mail']; ?></td>
                                <td class="postCol"><?php echo $tableauRequete[$i]["code_postal"]; ?></td>
                                <td class="tdButton">
                                    <button type="submit" name="update" value="<?php echo $tableauRequete[$i]['user_id'] ?>">Modifier</button>
                                </td>
                                <td class="tdButton">
                                    <button type="submit" name="delete" value="<?php echo $tableauRequete[$i]['user_id'] ?>">Supprimer</button>
                                </td>
                            <?php } ?>
                        <?php } ?>
                        </form>
                    </tr>
            </table>
            <?php
            if (!isset($_POST['update'])) { ?>
                <div id="addFormContainer">
                    <form method="POST">
                        <p>AJOUTER UN UTILISATEUR</p>
                        <input type="text" name="nom" placeholder="Nom">
                        <input type="text" name="prenom" placeholder="Prénom">
                        <input type="text" name="mail" placeholder="Mail">
                        <input type="text" name="code_postal" placeholder="Code postal">
                        <button type="submit" name="ajouter">Ajouter</button>
                        <input class="errorClass" type="text" value="<?php if (isset($_SESSION)) {
                                                                            print_r($_SESSION['errorlog']);
                                                                        } ?>" <?php if (isset($_SESSION)) {
                                                                                echo 'id="errorArea"';
                                                                            } ?>>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
<?php
function checkForm($name, $firstname, $mail, $postcode)
{
    $regex0 = '/^[\p{L}.-]{1,50}$/u';
    $regex1 = '/^[\p{L}.-]{1,50}$/u';
    $regex2 = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    $regex3 = '/^[0-9]{5}$/';
    if (preg_match($regex0, $name) && preg_match($regex1, $firstname) && preg_match($regex2, $mail) && preg_match($regex3, $postcode)) {
        return true;
    } else if (!preg_match($regex0, $name)) {
        $errorLog = "! erreur sur le nom !";
        $_SESSION['errorlog'] = $errorLog;
    } else if (!preg_match($regex1, $firstname)) {
        $errorLog = "! erreur sur le Prénom !";
        $_SESSION['errorlog'] = $errorLog;
    } else if (!preg_match($regex2, $mail)) {
        $errorLog = "! erreur sur le mail !";
        $_SESSION['errorlog'] = $errorLog;
    } else if (!preg_match($regex3, $postcode)) {
        $errorLog = "! erreur sur le code postal !";
        $_SESSION['errorlog'] = $errorLog;
    }
}
?>