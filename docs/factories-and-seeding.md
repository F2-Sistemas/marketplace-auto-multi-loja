# Estrutura de Seeders, Factories e Geração de Dados

Para viabilizar o desenvolvimento local ágil e permitir testes de fluxo automatizados consistentes no **AutoHub Central**, o backend possui suporte completo a Factories e Seeders do Eloquent.

---

## 1. Factories Criadas

Cada model fundamental no sistema possui uma factory correspondente na pasta [database/factories](file:///projects/tiago/marketplace-multi-loja-automoveis/apps/api/database/factories):

### `BrandFactory`
* **Modelo**: `App\Models\Brand`
* **Campos Gerados**:
  * `name`: Nome da marca (ex: Toyota, Volkswagen, Honda, etc.) garantindo unicidade.
  * `slug`: Slug em minúsculo do nome da marca.
  * `logo_url`: Inicialmente nulo.

### `VehicleModelFactory`
* **Modelo**: `App\Models\VehicleModel`
* **Campos Gerados**:
  * `brand_id`: Vínculo automático à factory `BrandFactory`.
  * `name`: Nome do modelo (ex: Corolla, Polo, Civic, Compass, 320i, etc.).
  * `slug`: Slug correspondente.

### `StoreFactory`
* **Modelo**: `App\Models\Store`
* **Campos Gerados**:
  * `public_id`: UUID único aleatório.
  * `name`: Nome corporativo único gerado com o sufixo "Motors".
  * `slug`: Slug amigável.
  * `status`: Definido por padrão como `'active'`.

### `VehicleFactory`
* **Modelo**: `App\Models\Vehicle`
* **Campos Gerados**:
  * `store_id`: Vínculo a uma loja existente de forma aleatória ou via factory.
  * `brand_id`: Marca resolvida dinamicamente.
  * `vehicle_model_id`: Modelo resolvido dinamicamente de acordo com a marca selecionada.
  * `city_id`: Cidade existente resolvida dinamicamente.
  * `title`: Título dinâmico unindo Marca, Modelo e Câmbio.
  * `slug`: Slug amigável único com sufixo numérico.
  * `price`: Preço flutuante de mercado entre R$ 45.000 e R$ 250.000.
  * `year_manufacture` / `year_model`: Anos lógicos coerentes de fabricação.
  * `mileage`: Quilometragem aleatória.
  * `transmission`: Câmbio (`automatico` ou `manual`).
  * `fuel`: Tipo de combustível (`flex`, `gasolina`, `diesel`, `eletrico`).
  * `status`: `'available'`.

### `LeadFactory`
* **Modelo**: `App\Models\Lead`
* **Campos Gerados**:
  * `store_id`: Resolvido automaticamente correspondente ao veículo.
  * `vehicle_id`: Veículo existente resolvido dinamicamente.
  * `name` / `email` / `phone`: Dados completos simulados de contato do lead.
  * `message`: Texto de proposta de interesse aleatório.
  * `status`: `'pending'`.

---

## 2. Como Rodar no Ambiente Local

Para reinicializar toda a base de dados com as tabelas estruturadas e preencher todos os dados essenciais e fictícios para demonstração, utilize o comando Artisan:

```bash
# Navegue até a pasta da API
cd apps/api

# Rode o comando de limpeza de base e preenchimento de seeds
php artisan migrate:fresh --seed
```

---

## 3. Seeders Disponíveis

As classes de seeding em [database/seeders](file:///projects/tiago/marketplace-multi-loja-automoveis/apps/api/database/seeders) encarregam-se de estruturar a carga inicial na seguinte ordem de dependência:

1. **`StateSeeder`**: Insere os estados brasileiros de demonstração (ex: Rio Grande do Norte, São Paulo, Paraná).
2. **`CitySeeder`**: Associa cidades representativas a esses estados (ex: Natal, São Paulo, Curitiba).
3. **`BrandSeeder`**: Carrega as marcas automotivas principais.
4. **`VehicleModelSeeder`**: Configura modelos específicos e coerentes para cada marca.
5. **`StoreSeeder`**: Cria os tenants iniciais padrão (`natal-motors`, `sp-veiculos`, `euro-select`) com seus domínios técnicos primários resolvidos.
6. **`UserSeeder`**: Cria usuários de teste e vincula gestores locais e administradores centrais.
