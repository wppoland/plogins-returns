=== Plogins Returns - Returns and RMA for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, returns, rma, refund, return request
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Wymaga wtyczek: woocommerce
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Pozwól klientom żądać zwrotów pieniędzy ze swojego konta i zarządzać RMA w panelu administracyjnym.

== Description ==

Zwroty dodają do WooCommerce prosty, samoobsługowy proces zwrotu (RMA). W <strong>Moje konto → Zamówienia</strong> klient otwiera prośbę o zwrot kwalifikującego się zamówienia: oni
wybierz produkty, ustal ilość, wybierz powód i dodaj opcjonalną notatkę. The
prośba zostanie zapisana jako prywatny rekord, wysłana do Ciebie e-mailem i otrzyma status
klient może śledzić na swoim koncie.

Sprawdzasz i zarządzasz każdym żądaniem w wp-admin w sekcji <strong>WooCommerce → Żądania zwrotu</strong>, przechodząc przez żądane, zatwierdzone, odrzucone lub zakończone.
Niezależnie od ustawionego statusu, klient widzi go na swoim koncie.

To jest wtyczka żądania i statusu: nie przenosi pieniędzy. Przetwórz dowolny zwrot pieniędzy
na normalnym ekranie zamówienia WooCommerce; w rejestrze zwrotów znajduje się żądanie i
jego status w jednym miejscu.

Kod źródłowy i raporty o błędach są dostępne na https://github.com/wppoland/plogins-returns.

= Documentation and links =

* <strong>Dokumentacja</strong> - https://plogins.com/pl/plogins-returns/docs/
* <strong>Strona wtyczki</strong> - https://plogins.com/pl/plogins-returns/
* <strong>Kod źródłowy</strong> - https://github.com/wppoland/plogins-returns
* <strong>Raporty o błędach i prośby o nowe funkcje</strong> - https://github.com/wppoland/plogins-returns/issues


= Features =

* Akcja „Poproś o zwrot” w przypadku kwalifikujących się zamówień na Moim koncie (lista zamówień i widok pojedynczego zamówienia).
* Selektor przedmiotów z liczbą poszczególnych pozycji, listą powodów i opcjonalną notatką.
* Sprawdzenie własności: tylko zalogowany właściciel zamówienia może poprosić o jego zwrot.
* Konfigurowalne statusy kwalifikujących się zamówień i okno zwrotu (w dniach).
* Każde żądanie jest zapisywane jako prywatny, niestandardowy typ postu i wysyłane e-mailem do administratora sklepu.
* Ekran zarządzania administratorem z przepływem pracy dotyczącym statusu: zażądano, zatwierdzono, odrzucono, ukończono.
* Lista statusów widoczna dla klienta na Moim koncie, dzięki czemu kupujący mogą śledzić swoje zwroty.
* Dostępne znaczniki z responsywnym układem; style witryn sklepowych dziedziczą kolory motywu, więc pasują do jasnych lub ciemnych motywów bez dodatkowej pracy.
* Gotowe do tłumaczenia (w tym POT) i czystej dezinstalacji.
* Kompatybilny z HPOS i blokami koszyka/kasy.

== Installation ==

1. Prześlij wtyczkę do `/wp-content/plugins/returns` lub zainstaluj poprzez Wtyczki → Dodaj nową.
2. Aktywuj. WooCommerce musi być zainstalowany i aktywny.
3. Przejdź do <strong>WooCommerce → Zwroty<strong>, aby wybrać kwalifikujące się statusy zamówień i okno zwrotu. 4. Klienci mogą teraz otworzyć zwrot w </strong>Moje konto → Zamówienia</strong> dowolnego kwalifikującego się zamówienia.

== Frequently Asked Questions ==

= Does it require WooCommerce? =

Tak. WooCommerce musi być zainstalowany i aktywny.

= Which orders can be returned? =

Zamówienia w statusach wybranych w WooCommerce → Zwroty (zakończone i
Domyślne przetwarzanie) w ustawionym przez Ciebie oknie zwrotu. Ustaw okno na 0, aby
usunąć limit czasu.

= Does it issue refunds automatically? =

Nie. Ten MVP rejestruje żądanie i śledzi jego status. Przetwarzaj wszelkie zwroty pieniędzy w
normalny ekran zamówienia WooCommerce; rekord zwrotu pozostaje zsynchronizowany ze statusem
ustawiłeś.

= Where do return requests go? =

Każde zgłoszenie jest wysyłane e-mailem do administratora sklepu i zapisywane jako prywatny plik „Zwrot
Żądanie” w menu WooCommerce w wp-admin.

= Can a customer return the same order twice? =

Nie. Gdy pojawi się prośba o zwrot zamówienia, czynność zostanie ukryta i pojawi się powiadomienie
zamiast tego jest pokazywany.


= Does this plugin work on WordPress Multisite? =

Tak. Ta wtyczka jest kompatybilna z WordPress Multisite. Aktywuj go w sieci lub aktywuj na poszczególnych stronach; każda witryna przechowuje własne ustawienia i dane.

== Screenshots ==

1. Akcja „Zażądaj zwrotu” przy zamówieniu na Moim Koncie.
2. Formularz żądania zwrotu: wybór przedmiotu, powód i uwaga.

== External Services ==

Zwroty nie łączą się z żadnymi usługami zewnętrznymi. Nie wysyła żadnych danych z Twojej witryny i nie ładuje żadnych skryptów, czcionek ani interfejsów API innych firm. Każde żądanie zwrotu jest przechowywane lokalnie w WordPressie jako prywatny, niestandardowy typ postu `returns_rma` (z meta postu `_returns_*` dla zamówienia, klienta, artykułów, przyczyny, notatki i statusu), a konfiguracja wtyczki jest dostępna w opcjach `returns_settings` i `returns_db_version`. E-mail z powiadomieniem administratora jest wysyłany za pośrednictwem poczty WordPress Twojej witryny (`wp_mail`), więc dostawa korzysta z dowolnej konfiguracji poczty, którą zapewnia Twój serwer lub wtyczka SMTP.

== Changelog ==

= 1.0.1 =
* Pierwsza stabilna wersja.

= 0.1.3 =
* Zmieniono nazwę na Plogins Returns for WooCommerce, aby uzyskać bardziej charakterystyczną nazwę wtyczki.

= 0.1.2 =
* Pomocnik `Zwroty\Wsparcie\Zwroty` z akcją `zwroty/zwrot_zamówienia` dla automatyzacji zwrotów PRO.

= 0.1.1 =
* `Zwroty\Wsparcie\Powody` z filtrami `zwroty/powody` i `zwroty/label_powodu` dla analiz i rozszerzeń PRO.

= 0.1.0 =
* Wersja pierwsza: samoobsługowe prośby o zwrot z Mojego konta, narzędzie do wybierania produktów z powodem i uwagą, weryfikacja własności, konfigurowalne uprawnienia i okno, e-mail sprzedawcy, prywatny rekord żądań zwrotu i przepływ pracy dotyczący statusu administratora.
