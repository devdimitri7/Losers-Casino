# Loser Casino — v2 (ak baz done)

## Sa ki chanje parapò ak vèsyon 1 la

- Balans jwè a kounye a estoke nan **MySQL**, pa nan sesyon PHP sèlman.
- Vrè **kont itilizatè** ak modpas hache (`password_hash` / `password_verify`).
- Chak mize pwoteje ak yon **tranzaksyon DB + verrouillage (`FOR UPDATE`)** pou anpeche
  yon jwè fè de mize anmenmtan sou menm balans lan.
- Tablo "gwo genyen yo" sou paj akèy la kounye a soti **dirèkteman nan baz done a**,
  pa yon lis ekri an dur nan HTML.
- Tab `transactions` kenbe istorik chak depo/genyen/pèdi — itil pou odit ak analiz.

## Enstale l lokalman

1. Enstale PHP 8+, MySQL/MariaDB, ak yon sèvè web (Apache/Nginx), oswa itilize Docker.
2. Kreye baz done a :
   ```bash
   mysql -u root -p < db.sql
   ```
3. Defini varyab anviwònman yo (oswa modifye `config.php` pou tès lokal) :
   ```bash
   export DB_HOST=127.0.0.1
   export DB_NAME=loser_casino
   export DB_USER=root
   export DB_PASS=motdepas_ou
   ```
4. Lanse sèvè PHP entegre a pou tès rapid :
   ```bash
   php -S localhost:8000
   ```
5. Ale sou `http://localhost:8000/casino.php`, enskri w, epi jwe.

## Pwochen etap pou fè l tounen yon vrè pwodwi

1. **Lisans jwèt aza** nan jiridiksyon kote w ap opere a — sa se etap ki dwe fèt
   anvan nenpòt lòt bagay si w ap manyen lajan reyèl.
2. **Founisè peman ki gen lisans** (pou depo/retrè) — ranplase blòk "depo similé"
   nan `roulette.php` ak yon apèl API/webhook ki soti nan founisè sa a.
3. **KYC/AML** — verifye idantite ak laj itilizatè yo anvan yo ka retire lajan.
4. **RNG sètifye** pa yon lab endepandan si w vle sètifikasyon jwèt jis.
5. **HTTPS obligatwa** an pwodiksyon, ak yon sèvè ki separe de baz done a.
6. **Backup otomatik** baz done a ak yon plan reprann apre dezas.
7. **Limit responsab** : limit depo pa jou, opsyon otoeksklizyon pou jwè yo.

San etap 1-3 yo, aplikasyon an dwe rete yon demo/pòtfolyo — pa yon operasyon
finansye reyèl.

## Estrikti fichye

```
config.php          -> konfigirasyon (koneksyon DB, sekirite sesyon)
db.sql               -> schema baz done a
includes/db.php      -> koneksyon PDO + verifikasyon koneksyon itilizatè
register.php         -> kreyasyon kont
login.php            -> koneksyon
logout.php           -> dekoneksyon
casino.php           -> paj akèy
roulette.php         -> jwèt la
casino.css           -> estil
```
