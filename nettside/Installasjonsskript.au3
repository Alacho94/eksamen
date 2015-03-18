#include <GUIConstantsEx.au3>
#include <ButtonConstants.au3>
#include <File.au3>
#include <ie.au3>

; Lag gui for installasjonsskriptet
Local $hGUI = GUICreate("Installasjonsskript", 300, 210)
Local $iDomainLabel = GUICtrlCreateLabel("URL:", 10, 10, 73)
Local $iDomain = GUICtrlCreateInput("domenenavn.domene", 90, 7, 200, 20)
Local $iHostLabel = GUICtrlCreateLabel("Host:", 10, 40, 73)
Local $iHost = GUICtrlCreateInput("localhost", 90, 37, 200, 20)
Local $iDatabaseLabel = GUICtrlCreateLabel("Databasenavn:", 10, 70, 73)
Local $iDatabase = GUICtrlCreateInput("PJ2100", 90, 67, 200, 20)
Local $iuserNameLabel = GUICtrlCreateLabel("Brukernavn:", 10, 100, 73)
Local $iUsername = GUICtrlCreateInput("root", 90, 97, 200, 20)
Local $ipasswordLabel = GUICtrlCreateLabel("Passord:", 10, 130, 73)
Local $iPassword = GUICtrlCreateInput("root", 90, 127, 200, 20)
Local $iOpprettDB = GUICtrlCreateCheckbox("Opprett database", 10, 170, 100)
Local $iOK = GUICtrlCreateButton("Start installasjon", 130, 170, 150, 25, $BS_DEFPUSHBUTTON)

GUICtrlSetState($iOpprettDB, $GUI_CHECKED)
GUICtrlSetTip($iDomain, "Skriv domenenavn og domene til nettsiden" & @CRLF & "Eksempel: immortaltools.com")
GUICtrlSetTip($iHost, "Skriv riktig host, vanligvis er denne localhost")
GUICtrlSetTip($iDatabase, "Velg eksisterende eller nytt databasenavn")
GUICtrlSetTip($iuserName, "Bruker med tilgang til database")
GUICtrlSetTip($ipassword, "Passord for database tilgang")
GUICtrlSetTip($iOpprettDB, "Opprett ny database med valgt databasenavn?" & @CRLF & "Hvis ikke valgt, gjøres installasjon på eksisterende datbase")
GUISetState(@SW_SHOW, $hGUI)

; Loop til gui lukkes
While 1
	Switch GUIGetMsg()
		Case $GUI_EVENT_CLOSE
			Exit
		Case $iOK
			; Hent verdier fra gui
			Local $dNavn = GUICtrlRead ($iDomain)
			Local $dbHost = GUICtrlRead ($iHost)
			Local $dbNavn = GUICtrlRead ($iDatabase)
			Local $uNavn = GUICtrlRead ($iuserName)
			Local $uPass = GUICtrlRead ($iPassword)
			Local $opprettDB = GUICtrlRead($iOpprettDB)
			Local $success = True

			; Sjekk og gi beskjed om noe er fylt ut feil
			If $dNavn = "domenenavn.domene" Or $dNavn = "" Then errorMsg ("Du må skrive URL til nettsiden")
			If $dbNavn = "" Then errorMsg ("Du må velge et databasenavn")
			If $uNavn = "" Then errorMsg ("Du må skrive brukernavnet for tilgang til database")
			If $uPass = "" Then errorMsg ("Du må skrive passord for tilgang til database")

			If $success = True Then ExitLoop
	EndSwitch
WEnd

; Konfigurer databasefilen db.php
Local $sFile = @ScriptDir & "\core\database\db.php"
replaceInFile($sFile, 2, '    $host = "' & $dbHost & '";')
replaceInFile($sFile, 3, '    $db = "' & $dbNavn & '";')
replaceInFile($sFile, 4, '    $bruker = "' & $uNavn & '";')
replaceInFile($sFile, 5, '    $pass = "' & $uPass & '";')

; Konfigurer database setup fila setupDB.php dersom bruker har valgt å opprette tabell
If $opprettDB = 1 Then
	Local $sFile = @ScriptDir & "\setupDB.php"
	replaceInFile($sFile, 2, "    $createDB = true;")
	replaceInFile($sFile, 5, '            $dbh = new PDO("mysql:host=' & $dbHost & '", "' & $uNavn & '", "' & $uPass & '"); // login med bruker')
	replaceInFile($sFile, 6, '            $dbh->exec("CREATE DATABASE IF NOT EXISTS `' & $dbNavn & '`;")  // opprett database')
EndIf

$msg = MsgBox(1, "Suksess", "Konfigurasjon fullført. Last opp innholdet i mappen 'nettside' til root på domene. Denne er ofte domene.domenavn/public_html." & @CRLF _
		& "For eksempel immortaltools.com/public_html." & @CRLF & @CRLF & "Når du har lastet opp, trykk ok for å kjøre databaseoppsettet")
If $msg <> 1 Then Exit	; Avbryt hvis bruker trykker cancel.

ShellExecute("http://" & $dNavn & "/setupDB.php")	; Åpne scriptet for oppsett av database
Sleep(3000)
MsgBox(0, "Suksess", "Installasjon fullført. Slett filen setupDB.php fra nettsiden.")

; Endre databasefilen db.php
Func replaceInFile($sFile, $line, $replace)
	Local $replaceString = _FileWriteToLine($sFile, $line, $replace, 1)
	If $replaceString = 0 Then
		errorMsg("Kunne ikke endre filen: " & $sFile & " Error: " & @error)
		Exit
	EndIf
EndFunc


Func errorMsg($msg)
	MsgBox(16, "Feil", $msg)
	$success = false
EndFunc