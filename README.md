# FourWeb-API

API REST que alimenta o SPA **FourWeb-Painel** (dashboard financeiro das escolas
gerenciadas pelo sistema Four Gestão). Substitui as consultas que antes eram
feitas pelo dashboard Power BI legado, expondo os mesmos cálculos em endpoints
HTTP/JSON enxutos.

A API é **read-only**: o BI é estritamente leitura sobre as views do banco
financeiro. Nenhum endpoint escreve, atualiza ou deleta dados.

---

## Stack

| Camada | Versão |
|---|---|
| PHP | **8.3+** (testado em 8.5.6) |
| Laravel | **13.8.x** |
| Banco | **PostgreSQL 14+** (AWE Cloud) |
| Composer | 2.9+ |

Dependências adicionais ficam em `composer.json`. Não usa filas, broadcasting,
nem cache externo — `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`,
`SESSION_DRIVER=file`.

---

## Banco de dados

Conecta no Postgres hospedado no **AWE Cloud Solution**:

```
DB_HOST=fourgestao.awecloudsolution.com
DB_PORT=5432
DB_DATABASE=db-Four
DB_USERNAME=FourAdm
DB_PASSWORD=<ver com o time>
DB_SCHEMA=public
DB_SSLMODE=prefer
```

> **Importante:** este é o **banco novo** (AWE Cloud). O banco legado
> `db-fourgestao.postgres.database.azure.com` (Azure) **não é mais consultado**
> nem aqui nem no Power BI atual — todas as views/tabelas foram migradas.

### Views/tabelas consultadas

| Objeto | Granularidade | Uso na API |
|---|---|---|
| `view_tb_receita_all` | 1 linha por linha de receita | totais, série mensal de receita, origens por amparo legal |
| `view_tb_nf_unica_all` | 1 linha por NF (deduplicada) | **totais e cards** de despesa, série mensal |
| `view_tb_nf_all` | 1 linha por **item** de NF | **TOP N** (empresa/grupo/natureza) e tabela detalhada da Consulta |
| `view_tb_rendimento_all` | 1 linha por rendimento | rendimentos |
| `view_tb_recursos_all` | distinct de recursos por escola/ano | filtros |
| `escolas_favoritas` | tabela | header da escola (`/api/escola/{id}`) |

A diferença entre `view_tb_nf_unica_all` e `view_tb_nf_all` é replicada
exatamente como o Power BI faz:

- **Totais e cards** usam a view "única" + `valor_total_documento`
  (cada NF entra uma vez, sem inflação)
- **TOP por empresa/grupo/natureza** e **tabela de itens** usam a view
  "all" + `valor_total_item` (preserva granularidade — itens diferentes
  de uma NF podem ter naturezas distintas)

Está documentado no código em `app/Domain/Dashboard/Repositories/DespesaRepository.php`.

---

## Arquitetura

DDD-lite em camadas — separação clara entre HTTP, regra de negócio e acesso
a dados, sem o peso de um DDD completo.

```
app/
├── Http/
│   ├── Controllers/Api/Dashboard/   ← thin: recebe Request, devolve Resource
│   │   ├── GeralController
│   │   ├── ReceitasController
│   │   ├── DespesasController
│   │   ├── ConsultaController
│   │   ├── ExtratoController
│   │   └── FiltrosController
│   ├── Controllers/Api/
│   │   └── EscolaController
│   ├── Requests/Dashboard/          ← FormRequest com regras + ->toFiltros()
│   └── Resources/Dashboard/         ← JsonResource, shape final do JSON
│
├── Domain/Dashboard/
│   ├── Contracts/                   ← Interfaces (DIP)
│   │   ├── ReceitaRepositoryInterface
│   │   ├── DespesaRepositoryInterface
│   │   ├── RendimentoRepositoryInterface
│   │   ├── ConsultaRepositoryInterface
│   │   ├── ExtratoRepositoryInterface
│   │   ├── FiltrosRepositoryInterface
│   │   └── EscolaRepositoryInterface
│   ├── DTOs/                        ← readonly value objects
│   │   ├── Periodo  (escola_id, ano, programa)
│   │   ├── FiltrosDespesas / FiltrosExtrato
│   │   ├── TotaisPorCategoria, PontoMensal, SaldoDisponivel
│   │   ├── FatiaCategorica, OrigemRecurso, ServicoDespesa, ItemExtrato
│   │   ├── ListaPaginada<T>
│   │   └── PainelGeral / PainelReceitas / PainelDespesas / Filtros / Escola
│   ├── Repositories/                ← Eloquent/Query Builder, lê das views
│   │   ├── AbstractFinanceiroRepository  (template method DRY: scopedQuery, totaisOn, serieMensalOn)
│   │   ├── ReceitaRepository
│   │   ├── DespesaRepository
│   │   ├── RendimentoRepository
│   │   ├── ConsultaRepository
│   │   ├── ExtratoRepository
│   │   ├── FiltrosRepository
│   │   └── EscolaRepository
│   └── Services/                    ← orquestração + regra de negócio
│       ├── SaldoCalculator        (receita + rendimento - despesa)
│       ├── PainelGeralService
│       ├── PainelReceitasService
│       └── PainelDespesasService
│
└── Providers/DashboardServiceProvider.php   ← bind Interface → Impl
```

### Convenções

- **Strict types em todos os arquivos** (`declare(strict_types=1);`).
- DTOs `final readonly class` — imutáveis.
- Repositórios herdam `AbstractFinanceiroRepository` quando consultam uma das
  três views financeiras unificadas; isso centraliza `scopedQuery()` (escola +
  ano + programa) e os `selectRaw` de SUM por categoria.
- Casts explícitos no Builder por causa da heterogeneidade dos types nas views:
  `view_tb_receita_all.id_escola` é `text`, mas `view_tb_nf_unica_all.id_escola`
  é `integer`. Cada subclasse declara `escolaIdIsText()` e `anoIsText()`.
- Endpoints sem regra de negócio (ex: `/filtros`, `/consulta`, `/extrato`,
  `/escola/{id}`) **não** têm Service — Controller injeta Repository direto.
  Service só existe quando há orquestração (combinação de 3 repos +
  SaldoCalculator nos painéis Geral, Receitas, Despesas).

---

## Endpoints

Base URL: `/api`. Todos os endpoints são **GET** e respondem **JSON** com
envelope `{ "data": ... }`.

| Endpoint | Params obrigatórios | Opcionais | Descrição |
|---|---|---|---|
| `/escola/{id}` | `id` (path) | — | Razão social, CNPJ, diretor, município, INEP |
| `/filtros` | `escola_id` | `ano`, `programa` | Anos, recursos, empresas, naturezas, grupos disponíveis |
| `/dashboard/geral` | `escola_id` | `ano`, `programa` | Cards + séries mensais + saldos |
| `/dashboard/receitas` | `escola_id` | `ano`, `programa` | Cards, séries, origem por amparo legal, saldo |
| `/dashboard/despesas` | `escola_id` | `ano`, `programa`, `empresas[]`, `naturezas[]`, `categorias[]`, `meses[]`, `top`, `page`, `per_page` | Cards, gauges %, TOP N, série mensal, NFs paginadas |
| `/dashboard/consulta` | `escola_id` | mesmos de `/despesas` | Tabela detalhada de NFs (1 linha por item, view_tb_nf_all) — espelha CONSULTA DESPESAS do PBI |
| `/dashboard/extrato` | `escola_id` | `ano`, `programa`, `tipos[]`, `busca`, `page`, `per_page` | Extrato unificado (receita+despesa+rendimento) com saldo acumulado (window function) |

### Parâmetros comuns

- **`escola_id`** (int, obrigatório): `id` em `escolas_favoritas`.
- **`ano`** (int 2000-2100, opcional): default = ano corrente.
- **`programa`** (string, opcional): default `PROGEFE`. Filtra todas as views
  por `programa` — replica o slicer **RECURSO** do Power BI, que é obrigatório
  em todas as 4 páginas do dashboard.

### Erros

- **422 Unprocessable Content** — validação falhou (ex: `meses[]=13`)
- **404 Not Found** — escola não encontrada
- **500** — erro inesperado

Todos os erros em `/api/*` respondem JSON (ver `bootstrap/app.php` →
`shouldRenderJsonWhen`). Mensagens em **pt_BR** (ver `lang/pt_BR/validation.php`).

---

## Setup local

### Pré-requisitos

- **PHP 8.3+** com extensões: `pdo_pgsql`, `pgsql`, `openssl`, `mbstring`,
  `curl`, `fileinfo`, `intl`, `zip`, `sodium`, `bcmath`, `gd`.
- **Composer 2.x**

No Windows + Chocolatey:

```powershell
choco install php -y --ignore-dependencies
choco install composer -y --ignore-dependencies
```

Habilitar as extensões no `php.ini` (descomentar as linhas `extension=*`)
e apontar `curl.cainfo` / `openssl.cafile` para um `cacert.pem` da Mozilla.

### Instalação

```bash
git clone https://github.com/foursistema/FourWeb-API.git
cd FourWeb-API
composer install
cp .env.example .env
php artisan key:generate
```

Edite o `.env` com as credenciais do Postgres (peça pro time se você ainda
não tiver). Mínimo necessário:

```env
APP_NAME="Four Gestao SPA API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=pt_BR
APP_TIMEZONE=America/Sao_Paulo

DB_CONNECTION=pgsql
DB_HOST=fourgestao.awecloudsolution.com
DB_PORT=5432
DB_DATABASE=db-Four
DB_USERNAME=FourAdm
DB_PASSWORD=...
DB_SCHEMA=public
DB_SSLMODE=prefer
```

> **Nunca** commit o `.env` — ele já está no `.gitignore`.

### Rodando

```bash
php artisan serve
# http://127.0.0.1:8000
```

Testar:

```bash
curl "http://127.0.0.1:8000/api/escola/786"
curl "http://127.0.0.1:8000/api/dashboard/geral?escola_id=786&ano=2024&programa=PROGEFE"
```

---

## CORS

Liberado em `config/cors.php` para `paths: ['api/*']`, `allowed_origins: ['*']`,
`allowed_methods: ['*']`. O SPA é embedado em sites públicos de escolas e no
próprio app desktop FourGestão (via WebView2), então precisa ser irrestrito.

Se em produção quiser fechar pra domínios específicos, edite `allowed_origins`.

---

## Para devs novos

- Comece lendo `routes/api.php` — todos os endpoints estão lá em uma tela.
- Cada controller é minúsculo (~15 linhas): recebe `Request`, chama Service ou
  Repository, devolve `Resource`. Pra entender o que cada um faz, vá direto
  na `Service` ou `Repository` correspondente.
- A regra de saldo (`Receita + Rendimento − Despesa`) está em
  `app/Domain/Dashboard/Services/SaldoCalculator.php`. Replica a medida DAX
  `RESULT_*_GERAL` do Power BI original.
- Mensagens de validação em pt_BR estão em `lang/pt_BR/validation.php`.
- Não há autenticação — qualquer um com `escola_id` consegue os dados daquela
  escola. Isso é **intencional**: o SPA será embedado em sites públicos onde
  o `escola_id` vem na querystring.

---

## Licença

Proprietário — Four Assessoria, Consultoria e Distribuidora LTDA.
