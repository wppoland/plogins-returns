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

Pozwól klientom zgłaszać zwroty i zwroty pieniędzy z poziomu konta oraz zarządzaj zgłoszeniami RMA w panelu administracyjnym.

== Description ==

Returns dodaje do WooCommerce prosty, samoobsługowy proces zwrotu (RMA). W sekcji <strong>Moje konto → Zamówienia</strong> klient otwiera żądanie zwrotu dla kwalifikującego się zamówienia: wybiera produkty, ustawia ilość, wskazuje powód i dodaje opcjonalną notatkę. Żądanie jest zapisywane jako prywatny rekord, wysyłane do Ciebie e-mailem i otrzymuje status, który klient może śledzić na swoim koncie.

Każde żądanie sprawdzasz i obsługujesz w wp-admin, w sekcji <strong>WooCommerce → Żądania zwrotu</strong>, przenosząc je kolejno przez statusy: zażądano, zatwierdzono, odrzucono lub ukończono. Status, który ustawisz, jest statusem widocznym dla klienta na jego koncie.

To wtyczka do obsługi żądań i statusów: nie przenosi pieniędzy. Zwroty pieniędzy realizuj na zwykłym ekranie zamówienia WooCommerce; rekord zwrotu przechowuje żądanie i jego status w jednym miejscu.

Kod źródłowy i zgłoszenia błędów znajdziesz na https://github.com/wppoland/plogins-returns.

= Documentation and links =

* <strong>Dokumentacja</strong> - https://plogins.com/pl/plogins-returns/docs/
* <strong>Strona wtyczki</strong> - https://plogins.com/pl/plogins-returns/
* <strong>Kod źródłowy</strong> - https://github.com/wppoland/plogins-returns
* <strong>Zgłoszenia błędów i propozycje funkcji</strong> - https://github.com/wppoland/plogins-returns/issues


= Features =

* Akcja „Poproś o zwrot” dla kwalifikujących się zamówień w sekcji Moje konto (lista zamówień i widok pojedynczego zamówienia).
* Selektor produktów z ilością dla każdej pozycji, listą rozwijaną z powodami i opcjonalną notatką.
* Weryfikacja własności: o zwrot zamówienia może poprosić wyłącznie zalogowany właściciel tego zamówienia.
* Konfigurowalne statusy kwalifikujących się zamówień oraz okno zwrotu (w dniach).
* Każde żądanie jest zapisywane jako prywatny, niestandardowy typ treści i wysyłane e-mailem do administratora sklepu.
* Ekran zarządzania w panelu administracyjnym z przepływem statusów: zażądano, zatwierdzono, odrzucono, ukończono.
* Lista statusów widoczna dla klienta w sekcji Moje konto, dzięki czemu kupujący mogą śledzić swoje zwroty.
* Dostępny kod HTML z responsywnym układem; style w sklepie dziedziczą kolory Twojego motywu, więc pasują do jasnych i ciemnych motywów bez dodatkowej pracy.
* Gotowe do tłumaczenia (dołączony plik POT) i czysta deinstalacja.
* Zgodne z HPOS oraz blokami koszyka/kasy.

== Installation ==

1. Wgraj wtyczkę do `/wp-content/plugins/returns` lub zainstaluj przez Wtyczki → Dodaj nową.
2. Włącz ją. WooCommerce musi być zainstalowane i aktywne.
3. Przejdź do <strong>WooCommerce → Zwroty</strong>, aby wybrać kwalifikujące się statusy zamówień oraz okno zwrotu.
4. Klienci mogą teraz otworzyć zwrot z poziomu <strong>Moje konto → Zamówienia</strong> dla dowolnego kwalifikującego się zamówienia.

== Frequently Asked Questions ==

= Does it require WooCommerce? =

Tak. WooCommerce musi być zainstalowane i aktywne.

= Which orders can be returned? =

Zamówienia w statusach wybranych w WooCommerce → Zwroty (domyślnie Zrealizowane i W trakcie realizacji), w ustawionym przez Ciebie oknie zwrotu. Ustaw okno na 0, aby usunąć limit czasu.

= Does it issue refunds automatically? =

Nie. Ta wersja MVP rejestruje żądanie i śledzi jego status. Zwroty pieniędzy realizuj na zwykłym ekranie zamówienia WooCommerce; rekord zwrotu pozostaje zsynchronizowany z ustawionym przez Ciebie statusem.

= Where do return requests go? =

Każde zgłoszenie jest wysyłane e-mailem do administratora sklepu i zapisywane jako prywatny rekord „Żądanie zwrotu” w menu WooCommerce w wp-admin.

= Can a customer return the same order twice? =

Nie. Gdy dla zamówienia istnieje już żądanie zwrotu, akcja jest ukrywana, a zamiast niej wyświetlane jest powiadomienie.


= Does this plugin work on WordPress Multisite? =

Tak. Ta wtyczka jest zgodna z WordPress Multisite. Włącz ją dla całej sieci lub w pojedynczych witrynach; każda witryna zachowuje własne ustawienia i dane.

== Screenshots ==

1. Akcja „Poproś o zwrot” przy zamówieniu w sekcji Moje konto.
2. Formularz żądania zwrotu: selektor produktów, powód i notatka.

== External Services ==

Returns nie łączy się z żadnymi usługami zewnętrznymi. Nie wysyła żadnych danych poza Twoją witrynę i nie ładuje żadnych skryptów, czcionek ani interfejsów API innych firm. Każde żądanie zwrotu jest przechowywane lokalnie w WordPressie jako prywatny, niestandardowy typ treści `returns_rma` (z meta wpisu `_returns_*` dla zamówienia, klienta, pozycji, powodu, notatki i statusu), a konfiguracja wtyczki znajduje się w opcjach `returns_settings` i `returns_db_version`. E-mail z powiadomieniem dla administratora jest wysyłany przez wbudowaną pocztę WordPress Twojej witryny (`wp_mail`), więc do dostarczania używana jest konfiguracja poczty zapewniana już przez Twój serwer lub wtyczkę SMTP.

== Translations ==

Plogins Returns zawiera polskie, niemieckie i hiszpańskie tłumaczenia interfejsu wtyczki. Domena tekstowa to `plogins-returns`, dzięki czemu paczki językowe z WordPress.org mogą również nadpisywać lub rozszerzać dołączone tłumaczenia.

== Changelog ==

= 1.0.2 =
* Dodano dołączone polskie, niemieckie i hiszpańskie tłumaczenia interfejsu wtyczki.

= 1.0.1 =
* Pierwsza stabilna wersja.

= 0.1.3 =
* Zmieniono nazwę na Plogins Returns for WooCommerce, aby uzyskać bardziej charakterystyczną nazwę wtyczki.

= 0.1.2 =
* Pomocnik `Returns\Support\Refunds` z akcją `returns/order_refund` dla automatyzacji zwrotów pieniędzy PRO.

= 0.1.1 =
* `Returns\Support\Reasons` z filtrami `returns/reasons` i `returns/reason_label` dla analityki i rozszerzeń PRO.

= 0.1.0 =
* Pierwsze wydanie: samoobsługowe żądania zwrotu z sekcji Moje konto, selektor produktów z powodem i notatką, weryfikacja własności, konfigurowalne kryteria kwalifikacji i okno, e-mail do sprzedawcy, prywatny rekord żądania zwrotu oraz przepływ statusów w panelu administracyjnym.
