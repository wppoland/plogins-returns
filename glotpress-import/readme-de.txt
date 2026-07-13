=== Plogins Returns - Returns and RMA for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, returns, rma, refund, return request
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Requires Plugins: woocommerce
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lass deine Kundschaft Rücksendungen und Rückerstattungen über ihr Konto anfordern und verwalte RMAs im Adminbereich.

== Description ==

Returns fügt WooCommerce einen einfachen Self-Service-Rückgabeablauf (RMA) hinzu. Über <strong>Mein Konto → Bestellungen</strong> öffnet ein Kunde eine Rückgabeanfrage für eine berechtigte Bestellung: Er wählt die Artikel aus, legt eine Menge fest, wählt einen Grund und fügt optional eine Notiz hinzu. Die Anfrage wird als privater Datensatz gespeichert, dir per E-Mail zugeschickt und erhält einen Status, den der Kunde von seinem Konto aus verfolgen kann.

Du prüfst und verwaltest jede Anfrage in wp-admin unter <strong>WooCommerce → Rückgabeanfragen</strong> und bewegst jede einzelne durch die Stufen „Angefordert“, „Genehmigt“, „Abgelehnt“ oder „Abgeschlossen“. Welchen Status du auch festlegst – genau diesen Status sieht der Kunde in seinem Konto.

Dies ist ein Anfrage- und Status-Plugin: Es bewegt kein Geld. Wickle jede Rückerstattung im normalen WooCommerce-Bestellbildschirm ab; der Rückgabedatensatz hält die Anfrage und ihren Status an einem Ort.

Quellcode und Fehlerberichte findest du unter https://github.com/wppoland/plogins-returns.

= Documentation and links =

* <strong>Dokumentation</strong> - https://plogins.com/de/plogins-returns/docs/
* <strong>Plugin-Seite</strong> - https://plogins.com/de/plogins-returns/
* <strong>Quellcode</strong> - https://github.com/wppoland/plogins-returns
* <strong>Fehlerberichte und Funktionswünsche</strong> - https://github.com/wppoland/plogins-returns/issues


= Features =

* Aktion „Rückgabe anfordern“ für berechtigte Bestellungen in „Mein Konto“ (Bestellliste und Einzelbestellansicht).
* Artikelauswahl mit Menge pro Artikel, einem Dropdown mit Gründen und einer optionalen Notiz.
* Besitzprüfung: Nur der eingeloggte Besitzer einer Bestellung kann eine Rückgabe dafür anfordern.
* Konfigurierbare berechtigte Bestellstatus und ein Rückgabefenster (in Tagen).
* Jede Anfrage wird als privater benutzerdefinierter Beitragstyp gespeichert und per E-Mail an den Shop-Administrator gesendet.
* Verwaltungsbildschirm im Adminbereich mit einem Status-Workflow: angefordert, genehmigt, abgelehnt, abgeschlossen.
* Kundenseitige Statusliste in „Mein Konto“, damit Käufer ihre Rücksendungen verfolgen können.
* Barrierefreies Markup mit responsivem Layout; die Shop-Stile erben die Farben deines Themes, sodass sie ohne zusätzlichen Aufwand zu hellen oder dunklen Themes passen.
* Übersetzungsbereit (POT enthalten) und saubere Deinstallation.
* Kompatibel mit HPOS und Warenkorb-/Kassenblöcken.

== Installation ==

1. Lade das Plugin nach `/wp-content/plugins/returns` hoch oder installiere es über Plugins → Installieren.
2. Aktiviere es. WooCommerce muss installiert und aktiv sein.
3. Gehe zu <strong>WooCommerce → Retouren</strong>, um die berechtigten Bestellstatus und das Rückgabefenster auszuwählen.
4. Kunden können jetzt über <strong>Mein Konto → Bestellungen</strong> für jede berechtigte Bestellung eine Rücksendung öffnen.

== Frequently Asked Questions ==

= Does it require WooCommerce? =

Ja. WooCommerce muss installiert und aktiv sein.

= Which orders can be returned? =

Bestellungen in den Status, die du unter WooCommerce → Retouren auswählst (standardmäßig „Abgeschlossen“ und „In Bearbeitung“), innerhalb des von dir festgelegten Rückgabefensters. Setze das Fenster auf 0, um die Frist aufzuheben.

= Does it issue refunds automatically? =

Nein. Dieses MVP erfasst die Anfrage und verfolgt ihren Status. Wickle jede Rückerstattung im normalen WooCommerce-Bestellbildschirm ab; der Rückgabedatensatz bleibt mit dem von dir festgelegten Status synchron.

= Where do return requests go? =

Jede Einreichung wird per E-Mail an den Shop-Administrator gesendet und als privater Datensatz „Rückgabeanfrage“ im WooCommerce-Menü in wp-admin gespeichert.

= Can a customer return the same order twice? =

Nein. Sobald für eine Bestellung eine Rückgabeanfrage existiert, wird die Aktion ausgeblendet und stattdessen ein Hinweis angezeigt.


= Does this plugin work on WordPress Multisite? =

Ja. Dieses Plugin ist mit WordPress Multisite kompatibel. Aktiviere es netzwerkweit oder auf einzelnen Websites; jede Website behält ihre eigenen Einstellungen und Daten.

== Screenshots ==

1. Die Aktion „Rückgabe anfordern“ bei einer Bestellung in „Mein Konto“.
2. Das Rückgabeanfrageformular: Artikelauswahl, Grund und Notiz.

== External Services ==

Returns verbindet sich mit keinen externen Diensten. Es sendet keine Daten von deiner Website und lädt keine Skripte, Schriftarten oder APIs von Drittanbietern. Jede Rückgabeanfrage wird lokal in WordPress als privater benutzerdefinierter Beitragstyp `returns_rma` gespeichert (mit dem Beitrags-Meta `_returns_*` für Bestellung, Kunde, Artikel, Grund, Notiz und Status), und die Konfiguration des Plugins liegt in den Optionen `returns_settings` und `returns_db_version`. Die Benachrichtigungs-E-Mail an den Administrator wird über die eigene WordPress-Mail (`wp_mail`) deiner Website gesendet, sodass die Zustellung das E-Mail-Setup nutzt, das dein Server oder SMTP-Plugin bereits bereitstellt.

== Translations ==

Plogins Returns enthält polnische, deutsche und spanische Übersetzungen für die Plugin-Oberfläche. Die Textdomain ist `plogins-returns`, sodass Sprachpakete von WordPress.org diese mitgelieferten Übersetzungen ebenfalls überschreiben oder erweitern können.

== Changelog ==

= 1.0.2 =
* Mitgelieferte polnische, deutsche und spanische Übersetzungen für die Plugin-Oberfläche hinzugefügt.

= 1.0.1 =
* Erste stabile Version.

= 0.1.3 =
* Umbenannt in Plogins Returns for WooCommerce für einen unverwechselbareren Plugin-Namen.

= 0.1.2 =
* `Returns\Support\Refunds`-Helfer mit der Aktion `returns/order_refund` für die PRO-Rückerstattungsautomatisierung.

= 0.1.1 =
* `Returns\Support\Reasons` mit den Filtern `returns/reasons` und `returns/reason_label` für PRO-Analysen und Erweiterungen.

= 0.1.0 =
* Erstveröffentlichung: Self-Service-Rückgabeanfragen über „Mein Konto“, Artikelauswahl mit Grund und Notiz, Besitzprüfungen, konfigurierbare Berechtigung und Zeitfenster, Händler-E-Mail, ein privater Rückgabeanfrage-Datensatz und ein Status-Workflow im Adminbereich.
