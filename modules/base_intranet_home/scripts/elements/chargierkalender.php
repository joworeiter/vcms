<?php
/*
This file is part of VCMS.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();


/*
* actions
*/

if(isset($_REQUEST["chargierkalenderchangeanmeldenstate"]) && $_REQUEST["chargierkalenderchangeanmeldenstate"] != "" && isset($_REQUEST['chargierveranstaltungid']) && $_REQUEST['chargierveranstaltungid'] != ""){
	$stmt = $libDb->prepare("SELECT * FROM mod_chargierkalender_veranstaltung WHERE id=:id");
	$stmt->bindValue(':id', $_REQUEST['chargierveranstaltungid'], PDO::PARAM_INT);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	// event in future?
	if(@date("Y-m-d H:i:s") <= $row["datum"]){
		if($_REQUEST["chargierkalenderchangeanmeldenstate"] == "anmelden"){
			$stmt = $libDb->prepare("INSERT IGNORE INTO mod_chargierkalender_teilnahme (chargierveranstaltung, mitglied) VALUES (:chargierveranstaltung, :mitglied)");
			$stmt->bindValue(':chargierveranstaltung', $_REQUEST['chargierveranstaltungid'], PDO::PARAM_INT);
			$stmt->bindValue(':mitglied', $libAuth->getId(), PDO::PARAM_INT);
			$stmt->execute();
		} else {
			$stmt = $libDb->prepare("DELETE FROM mod_chargierkalender_teilnahme WHERE chargierveranstaltung=:chargierveranstaltung AND mitglied=:mitglied");
			$stmt->bindValue(':chargierveranstaltung', $_REQUEST['chargierveranstaltungid'], PDO::PARAM_INT);
			$stmt->bindValue(':mitglied', $libAuth->getId(), PDO::PARAM_INT);
			$stmt->execute();
		}
	}
}


/*
* output
*/

$stmtCount = $libDb->prepare('SELECT COUNT(*) AS number FROM mod_chargierkalender_veranstaltung WHERE datum >= NOW()');
$stmtCount->execute();
$stmtCount->bindColumn('number', $count);
$stmtCount->fetch();

// if there are entries
if($count > 0){
	echo '<h2>Chargierkalender</h2>';

	$stmt = $libDb->prepare('SELECT * FROM mod_chargierkalender_veranstaltung WHERE datum >= NOW() ORDER BY datum LIMIT 0,4');
	$stmt->execute();

	echo '<div class="row">';

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		echo '<div class="col-sm-6">';
		echo '<hr />';

		$stmt2 = $libDb->prepare("SELECT COUNT(*) AS number FROM mod_chargierkalender_teilnahme WHERE mitglied=:mitglied AND chargierveranstaltung=:chargierveranstaltung");
		$stmt2->bindValue(':mitglied', $libAuth->getId(), PDO::PARAM_INT);
		$stmt2->bindValue(':chargierveranstaltung', $row['id'], PDO::PARAM_INT);
		$stmt2->execute();
		$stmt2->bindColumn('number', $angemeldet);
		$stmt2->fetch();

		echo '<b>' .$libTime->formatDateTimeString($row['datum'], 2). '</b> - ';
		echo '<a href="index.php?pid=intranet_chargierkalender_kalender&amp;semester=' .$libTime->getSemesterEinesDatums($row['datum']). '#t' .$row['id']. '">';
		echo $libVerein->getVereinNameString($row['verein']);
		echo '</a>';

		echo '<form action="index.php?pid=intranet_home" method="post" class="form-inline">';
		echo '<input type="hidden" name="chargierveranstaltungid" value="' .$row['id']. '" />';

		if($angemeldet){
			echo '<input type="hidden" name="chargierkalenderchangeanmeldenstate" value="abmelden" />';
			$libForm->printSubmitButtonInline('Abmelden');
		} else {
			echo '<input type="hidden" name="chargierkalenderchangeanmeldenstate" value="anmelden" />';
			$libForm->printSubmitButtonInline('Anmelden');
		}

		echo ' ';

		if($angemeldet){
			echo '<img src="styles/icons/calendar/attending.svg" alt="angemeldet" class="icon_small" /> angemeldet';
		} else {
			echo '<img src="styles/icons/calendar/notattending.svg" alt="abgemeldet" class="icon_small" /> nicht angemeldet';
		}

		echo '</form>';
		echo '</div>';
	}

	echo '</div>';
}
?>