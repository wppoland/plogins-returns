=== Plogins Returns - Returns and RMA for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, returns, rma, refund, return request
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Erfordert Plugins: woocommerce
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Ermögliche Kunden, Rücksendungen/Rückerstattungen über ihr Konto anzufordern und RMAs im Admin zu verwalten.

== Description ==

Returns fügt WooCommerce einen einfachen RMA-Ablauf (Self-Service Return) hinzu. Über <strong>Mein Konto → Bestellungen</strong> öffnet ein Kunde eine Rückgabeanfrage für eine berechtigte Bestellung: sie
Wähle die Artikel aus, lege eine Menge fest, wähle einen Grund und füge optional eine Notiz hinzu. Die
Die Anfrage wird als privater Datensatz gespeichert, per E-Mail an Sie gesendet und mit einem Status versehen
Der Kunde kann von seinem Konto aus folgen.

Du überprüfen und verwalten jede Anfrage in wp-admin unter <strong>WooCommerce → Rückgabeanfragen</strong> und bewegen jede einzelne in die Stufen „Angefordert“, „Genehmigt“, „Abgelehnt“ oder „Abgeschlossen“.
Welchen Status Sie auch festlegen, der Status wird dem Kunden in seinem Konto angezeigt.

Dies ist ein Anfrage- und Status-Plugin: Es bewegt kein Geld. Bearbeite etwaige Rückerstattungen
im normalen WooCommerce-Bestellbildschirm; Der Rückgabedatensatz speichert die Anforderung und
seinen Status an einem Ort.

Quellcode und Fehlerberichte live unter https://github.com/wppoland/plogins-returns.

= Documentation and links =

* <strong>Dokumentation</strong> - https://plogins.com/de/plogins-returns/docs/
* <strong>Plugin-Seite</strong> - https://plogins.com/de/plogins-returns/
* <strong>Quellcode</strong> – https://github.com/wppoland/plogins-returns
* <strong>Fehlerberichte und Funktionsanfragen</strong> – https://github.com/wppoland/plogins-returns/issues


= Features =

* Aktion „Rückgabe anfordern“ für berechtigte Bestellungen in „Mein Konto“ (Auftragsliste und Einzelbestellansicht).
* Artikelauswahl mit Stückzahl pro Artikel, einem Dropdown-Menü mit Gründen und einer optionalen Notiz.
* Besitzüberprüft: Nur der eingeloggte Besitzer einer Bestellung kann eine Rückgabe dafür beantragen.
* Konfigurierbare berechtigte Bestellstatus und ein Rückgabefenster (in Tagen).
* Jede Anfrage wird als privater benutzerdefinierter Beitragstyp gespeichert und per E-Mail an den Shop-Administrator gesendet.
* Admin-Verwaltungsbildschirm mit einem Status-Workflow: angefordert, genehmigt, abgelehnt, abgeschlossen.
* Kundenorientierte Statusliste in „Mein Konto“, damit Käufer ihre Retouren verfolgen können.
* Barrierefreies Markup mit responsivem Layout; Storefront-Stile übernehmen die Farben Ihres Themes, sodass sie ohne zusätzlichen Aufwand in helle oder dunkle Themes passen.
* Übersetzungsbereit (POT enthalten) und saubere Deinstallation.
* Kompatibel mit HPOS und Warenkorb-/Kassenblöcken.

== Installation ==

1. Lade das Plugin nach „/wp-content/plugins/returns“ hoch oder installiere es über Plugins → Neu hinzufügen.
2. Aktiviere es. WooCommerce muss installiert und aktiv sein.
3. Gehe zu <strong>WooCommerce → Retouren<strong>, um die zulässigen Bestellstatus und das Rückgabefenster auszuwählen. 4. Kunden können jetzt über </strong>Mein Konto → Bestellungen</strong> eine Rücksendung für jede berechtigte Bestellung eröffnen.

== Frequently Asked Questions ==

= Does it require WooCommerce? =

Ja. WooCommerce muss installiert und aktiv sein.

= Which orders can be returned? =

Bestellungen in den Status, die du unter WooCommerce → Retouren (abgeschlossen und) auswählen
Standardmäßige Verarbeitung), innerhalb des von dir festgelegten Rückgabefensters. Stelle das Fenster auf 0 bis ein
die Frist aufheben.

= Does it issue refunds automatically? =

Nein. Dieser MVP zeichnet die Anfrage auf und verfolgt ihren Status. Bearbeite etwaige Rückerstattungen im
normaler WooCommerce-Bestellbildschirm; Der Rückgabedatensatz bleibt mit dem Status synchron
Du hast eingestellt.

= Where do return requests go? =

Jede Einreichung wird per E-Mail an den Store-Administrator gesendet und als private „Rücksendung“ gespeichert
Request“-Eintrag im WooCommerce-Menü in wp-admin.

= Can a customer return the same order twice? =

Nein. Sobald für eine Bestellung eine Rückgabeanfrage vorliegt, wird die Aktion ausgeblendet und ein Hinweis angezeigt
wird stattdessen angezeigt.


= Does this plugin work on WordPress Multisite? =

Ja. Dieses Plugin ist mit WordPress Multisite kompatibel. Aktiviere es im Netzwerk oder auf einzelnen Websites. Jede Site behält ihre eigenen Einstellungen und Daten.

== Screenshots ==

1. Die Aktion „Rückgabe anfordern“ für eine Bestellung in „Mein Konto“.
2. Das Rückgabeantragsformular: Artikelauswahl, Grund und Hinweis.

== External Services ==

Retouren stellen keine Verbindung zu externen Diensten her. Es sendet keine Daten von deiner Website und lädt keine Skripte, Schriftarten oder APIs von Drittanbietern. Jede Rückgabeanfrage wird lokal in WordPress als privater benutzerdefinierter Beitragstyp „returns_rma“ gespeichert (mit dem Beitrags-Meta „_returns_*“ für Bestellung, Kunde, Artikel, Grund, Notiz und Status), und die Konfiguration des Plugins befindet sich in den Optionen „returns_settings“ und „returns_db_version“. Die Administrator-Benachrichtigungs-E-Mail wird über die eigene WordPress-Mail („wp_mail“) deiner Website gesendet, sodass für die Zustellung das E-Mail-Setup verwendet wird, das dein Server oder SMTP-Plugin bereits bereitstellt.

== Changelog ==

= 1.0.1 =
* Erste stabile Version.

= 0.1.3 =
* Umbenannt in „Plogins Returns for WooCommerce“, um einen eindeutigeren Plugin-Namen zu erhalten.

= 0.1.2 =
* „Returns\Support\Refunds“-Helfer mit der Aktion „returns/order_refund“ für die PRO-Rückerstattungsautomatisierung.

= 0.1.1 =
* „Returns\Support\Reasons“ mit den Filtern „returns/reasons“ und „returns/reason_label“ für PRO-Analysen und Erweiterungen.

= 0.1.0 =
* Erstveröffentlichung: Self-Service-Rückgabeanfragen über „Mein Konto“, Artikelauswahl mit Grund und Notiz, Eigentumsüberprüfungen, konfigurierbare Berechtigung und Zeitfenster, Händler-E-Mail, ein privater Rückgabeanfragedatensatz und ein Administratorstatus-Workflow.
