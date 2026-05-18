<script setup lang="ts">
import { ref, computed } from 'vue'

interface Vehicle {
  id: number
  store_name: string
  store_slug: string
  brand: string
  model: string
  version: string
  price: number
  year: string
  mileage: number
  transmission: string
  fuel: string
  color: string
  image: string
  featured?: boolean
}

// Simulated active vehicles across the platform
const vehicles = ref<Vehicle[]>([
  {
    id: 1,
    store_name: 'Natal Motors',
    store_slug: 'natal-motors',
    brand: 'Toyota',
    model: 'Corolla',
    version: 'GLi 2.0 Flex automatico',
    price: 85000,
    year: '2018/2019',
    mileage: 45000,
    transmission: 'automatico',
    fuel: 'flex',
    color: 'Prata',
    image: 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop',
    featured: true
  },
  {
    id: 2,
    store_name: 'Natal Motors',
    store_slug: 'natal-motors',
    brand: 'Toyota',
    model: 'Corolla',
    version: 'Altis 2.0 Flex automatico',
    price: 120000,
    year: '2021/2022',
    mileage: 12000,
    transmission: 'automatico',
    fuel: 'flex',
    color: 'Preto',
    image: 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop',
    featured: false
  },
  {
    id: 3,
    store_name: 'SP Veículos',
    store_slug: 'sp-veiculos',
    brand: 'Honda',
    model: 'Civic',
    version: 'EXL 2.0 automatico',
    price: 115000,
    year: '2020/2020',
    mileage: 32000,
    transmission: 'automatico',
    fuel: 'flex',
    color: 'Branco Pérola',
    image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=800&auto=format&fit=crop',
    featured: true
  },
  {
    id: 4,
    store_name: 'SP Veículos',
    store_slug: 'sp-veiculos',
    brand: 'Jeep',
    model: 'Compass',
    version: 'Longitude 2.0 Diesel 4x4',
    price: 145000,
    year: '2019/2020',
    mileage: 58000,
    transmission: 'automatico',
    fuel: 'diesel',
    color: 'Cinza',
    image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=800&auto=format&fit=crop',
    featured: true
  },
  {
    id: 5,
    store_name: 'Euro Select',
    store_slug: 'euro-select',
    brand: 'BMW',
    model: '320i',
    version: 'GP 2.0 ActiveFlex automatico',
    price: 198000,
    year: '2021/2021',
    mileage: 24000,
    transmission: 'automatico',
    fuel: 'flex',
    color: 'Azul Portimão',
    image: 'https://images.unsplash.com/photo-1555215695-3004980ad54e?q=80&w=800&auto=format&fit=crop',
    featured: true
  },
  {
    id: 6,
    store_name: 'Natal Motors',
    store_slug: 'natal-motors',
    brand: 'Volkswagen',
    model: 'Polo',
    version: 'Comfortline 1.0 TSI automatico',
    price: 89000,
    year: '2022/2023',
    mileage: 18000,
    transmission: 'automatico',
    fuel: 'flex',
    color: 'Cinza Platinum',
    image: 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?q=80&w=800&auto=format&fit=crop',
    featured: false
  }
])

// Filter states
const search = ref('')
const selectedBrand = ref('')
const selectedStore = ref('')
const maxPrice = ref(250000)
const selectedTransmission = ref('')

const brands = ['Toyota', 'Honda', 'Jeep', 'BMW', 'Volkswagen']
const stores = [
  { name: 'Natal Motors', slug: 'natal-motors' },
  { name: 'SP Veículos', slug: 'sp-veiculos' },
  { name: 'Euro Select', slug: 'euro-select' }
]

// Filtered vehicles
const filteredVehicles = computed(() => {
  return vehicles.value.filter(v => {
    const matchesSearch = !search.value || 
      `${v.brand} ${v.model} ${v.version}`.toLowerCase().includes(search.value.toLowerCase())
    const matchesBrand = !selectedBrand.value || v.brand === selectedBrand.value
    const matchesStore = !selectedStore.value || v.store_slug === selectedStore.value
    const matchesPrice = v.price <= maxPrice.value
    const matchesTrans = !selectedTransmission.value || v.transmission === selectedTransmission.value
    
    return matchesSearch && matchesBrand && matchesStore && matchesPrice && matchesTrans
  })
})

// Selected vehicle modal
const selectedVehicle = ref<Vehicle | null>(null)
const leadForm = ref({
  name: '',
  phone: '',
  message: ''
})
const showLeadSuccess = ref(false)

const openVehicleDetails = (vehicle: Vehicle) => {
  selectedVehicle.value = vehicle
  leadForm.value.message = `Olá! Gostaria de receber mais informações sobre o ${vehicle.brand} ${vehicle.model} (${vehicle.year}) anunciado por R$ ${vehicle.price.toLocaleString('pt-BR')}.`
  showLeadSuccess.value = false
}

const closeDetails = () => {
  selectedVehicle.value = null
}

const submitLead = () => {
  showLeadSuccess.value = true
  setTimeout(() => {
    showLeadSuccess.value = false
    closeDetails()
  }, 2500)
}

const formatPrice = (value: number) => {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 font-['Outfit'] antialiased">
    <!-- TOP NAVIGATION BAR -->
    <header class="sticky top-0 z-40 bg-slate-900/80 backdrop-blur-xl border-b border-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-amber-400 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
            <iconify-icon icon="tabler:steering-wheel" class="text-2xl animate-pulse"></iconify-icon>
          </div>
          <div>
            <span class="font-extrabold text-2xl tracking-tight bg-gradient-to-r from-indigo-400 to-amber-400 bg-clip-text text-transparent">AutoHub</span>
            <span class="text-[10px] uppercase font-bold text-indigo-400 block tracking-widest leading-none">Central</span>
          </div>
        </div>

        <nav class="hidden md:flex items-center gap-6">
          <a href="#" class="text-slate-300 hover:text-white transition font-medium text-sm">Início</a>
          <a href="#veiculos" class="text-slate-300 hover:text-white transition font-medium text-sm">Catálogo</a>
          <span class="h-4 w-px bg-slate-800"></span>
          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400">Ver Lojas:</span>
            <a href="http://natal-motors.rederevenda.com:3000" target="_blank" class="px-3 py-1 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition text-xs font-semibold border border-slate-700">Natal Motors</a>
            <a href="http://sp-veiculos.rederevenda.com:3000" target="_blank" class="px-3 py-1 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition text-xs font-semibold border border-slate-700">SP Veículos</a>
          </div>
        </nav>

        <div class="flex items-center gap-4">
          <a href="/admin" target="_blank" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white font-semibold text-sm transition shadow-lg shadow-indigo-600/20">
            <iconify-icon icon="tabler:dashboard" class="text-lg"></iconify-icon>
            <span>Painel Admin</span>
          </a>
        </div>
      </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative py-20 overflow-hidden border-b border-slate-800">
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(99,102,241,0.15),transparent_50%)]"></div>
      <div class="absolute -top-40 -left-40 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl"></div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 text-xs font-bold uppercase tracking-wider border border-indigo-500/20">Plataforma Multi-Loja Líder</span>
        <h1 class="mt-6 text-4xl sm:text-6xl font-extrabold tracking-tight text-white">
          A maior rede de revendas<br />
          <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-amber-400 bg-clip-text text-transparent">premium do Brasil</span>
        </h1>
        <p class="mt-6 text-lg text-slate-400 max-w-2xl mx-auto">
          Explore o estoque integrado de dezenas de lojas qualificadas, encontre o veículo perfeito com segurança e fale direto com o consultor.
        </p>

        <!-- PORTAL METRICS -->
        <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
          <div class="bg-slate-800/40 border border-slate-800/80 backdrop-blur-md rounded-2xl p-6 text-center">
            <div class="text-3xl font-extrabold text-white">2.500+</div>
            <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-semibold">Veículos Ativos</div>
          </div>
          <div class="bg-slate-800/40 border border-slate-800/80 backdrop-blur-md rounded-2xl p-6 text-center">
            <div class="text-3xl font-extrabold text-white">85+</div>
            <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-semibold">Lojas Parceiras</div>
          </div>
          <div class="bg-slate-800/40 border border-slate-800/80 backdrop-blur-md rounded-2xl p-6 text-center">
            <div class="text-3xl font-extrabold text-white">26</div>
            <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-semibold">Estados Atendidos</div>
          </div>
          <div class="bg-slate-800/40 border border-slate-800/80 backdrop-blur-md rounded-2xl p-6 text-center">
            <div class="text-3xl font-extrabold text-indigo-400 flex items-center justify-center gap-1">
              <span>99.8%</span>
              <iconify-icon icon="tabler:shield-check" class="text-xl"></iconify-icon>
            </div>
            <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-semibold">Satisfação Garantida</div>
          </div>
        </div>
      </div>
    </section>

    <!-- SEARCH & FILTER SECTION -->
    <section id="veiculos" class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-slate-800/30 border border-slate-800 backdrop-blur-md rounded-3xl p-6 md:p-8">
        <h2 class="text-xl font-bold text-white flex items-center gap-2 mb-6">
          <iconify-icon icon="tabler:adjustments-horizontal" class="text-indigo-400 text-2xl"></iconify-icon>
          <span>Filtre e Encontre Seu Carro</span>
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          <!-- Text Search -->
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
              <iconify-icon icon="tabler:search"></iconify-icon>
            </span>
            <input 
              v-model="search"
              type="text" 
              placeholder="Digite marca, modelo..." 
              class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-500 focus:outline-none transition-all"
            />
          </div>

          <!-- Brand Select -->
          <div class="relative">
            <select 
              v-model="selectedBrand"
              class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none appearance-none transition-all cursor-pointer"
            >
              <option value="">Todas as Marcas</option>
              <option v-for="b in brands" :key="b" :value="b">{{ b }}</option>
            </select>
            <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 pointer-events-none">
              <iconify-icon icon="tabler:chevron-down"></iconify-icon>
            </span>
          </div>

          <!-- Store Select -->
          <div class="relative">
            <select 
              v-model="selectedStore"
              class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none appearance-none transition-all cursor-pointer"
            >
              <option value="">Todas as Lojas</option>
              <option v-for="s in stores" :key="s.slug" :value="s.slug">{{ s.name }}</option>
            </select>
            <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 pointer-events-none">
              <iconify-icon icon="tabler:chevron-down"></iconify-icon>
            </span>
          </div>

          <!-- Transmission Select -->
          <div class="relative">
            <select 
              v-model="selectedTransmission"
              class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none appearance-none transition-all cursor-pointer"
            >
              <option value="">Câmbio (Todos)</option>
              <option value="automatico">Automático</option>
              <option value="manual">Manual</option>
            </select>
            <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 pointer-events-none">
              <iconify-icon icon="tabler:chevron-down"></iconify-icon>
            </span>
          </div>

          <!-- Price Max Filter -->
          <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-3 flex flex-col justify-center">
            <div class="flex justify-between text-xs font-medium mb-1 text-slate-400">
              <span>Até:</span>
              <span class="text-amber-400 font-semibold">{{ formatPrice(maxPrice) }}</span>
            </div>
            <input 
              v-model.number="maxPrice"
              type="range" 
              min="50000" 
              max="250000" 
              step="5000"
              class="w-full h-1 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-indigo-500"
            />
          </div>
        </div>
      </div>

      <!-- RESULTS COUNT & ACTIVE FILTER BADGES -->
      <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-slate-400">
          Mostrando <span class="text-white font-semibold">{{ filteredVehicles.length }}</span> veículos disponíveis
        </p>

        <button 
          v-if="search || selectedBrand || selectedStore || selectedTransmission || maxPrice < 250000"
          @click="search = ''; selectedBrand = ''; selectedStore = ''; selectedTransmission = ''; maxPrice = 250000"
          class="flex items-center gap-1.5 text-xs text-amber-400 hover:text-amber-300 font-semibold transition"
        >
          <iconify-icon icon="tabler:x" class="text-sm"></iconify-icon>
          <span>Limpar Filtros</span>
        </button>
      </div>

      <!-- VEHICLES GRID -->
      <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="v in filteredVehicles" 
          :key="v.id" 
          class="group bg-slate-800/30 hover:bg-slate-800/60 border border-slate-800/80 hover:border-slate-700/80 rounded-2xl overflow-hidden transition-all duration-300 flex flex-col hover:-translate-y-1 shadow-lg hover:shadow-indigo-500/5"
        >
          <!-- Vehicle Image -->
          <div class="relative aspect-video overflow-hidden">
            <img 
              :src="v.image" 
              :alt="`${v.brand} ${v.model}`" 
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
              <span class="px-2.5 py-1 rounded-lg bg-slate-900/90 backdrop-blur text-[10px] font-bold text-amber-400 border border-slate-700 uppercase">
                {{ v.store_name }}
              </span>
            </div>
            <div v-if="v.featured" class="absolute top-3 right-3">
              <span class="px-2.5 py-1 rounded-lg bg-indigo-600 text-[10px] font-bold text-white shadow shadow-indigo-600/40 uppercase">
                Destaque
              </span>
            </div>
          </div>

          <!-- Vehicle Details -->
          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-2 text-xs font-semibold text-indigo-400 tracking-wider uppercase">
                <span>{{ v.brand }}</span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>
                <span>{{ v.year }}</span>
              </div>
              <h3 class="text-lg font-bold text-white mt-2 group-hover:text-indigo-300 transition-colors">
                {{ v.model }}
              </h3>
              <p class="text-slate-400 text-xs mt-1">{{ v.version }}</p>

              <!-- Specifications Badges -->
              <div class="flex flex-wrap gap-2 mt-4">
                <span class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-900/50 border border-slate-800 text-[11px] text-slate-300">
                  <iconify-icon icon="tabler:road" class="text-indigo-400"></iconify-icon>
                  {{ v.mileage.toLocaleString('pt-BR') }} km
                </span>
                <span class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-900/50 border border-slate-800 text-[11px] text-slate-300">
                  <iconify-icon icon="tabler:settings" class="text-indigo-400"></iconify-icon>
                  Câmbio {{ v.transmission }}
                </span>
                <span class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-900/50 border border-slate-800 text-[11px] text-slate-300">
                  <iconify-icon icon="tabler:droplet" class="text-indigo-400"></iconify-icon>
                  {{ v.fuel }}
                </span>
              </div>
            </div>

            <!-- Price & Action -->
            <div class="mt-6 pt-5 border-t border-slate-800 flex items-center justify-between">
              <div>
                <span class="text-[10px] text-slate-400 block uppercase tracking-wider font-semibold">Valor Especial</span>
                <span class="text-xl font-extrabold text-amber-400">{{ formatPrice(v.price) }}</span>
              </div>
              <button 
                @click="openVehicleDetails(v)"
                class="flex items-center gap-1 px-4 py-2 rounded-xl bg-slate-700 hover:bg-indigo-600 text-white font-semibold text-xs transition"
              >
                <span>Ver Detalhes</span>
                <iconify-icon icon="tabler:arrow-up-right"></iconify-icon>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- EMPTY STATE -->
      <div v-if="filteredVehicles.length === 0" class="mt-16 text-center py-20 bg-slate-850 rounded-3xl border border-slate-800">
        <iconify-icon icon="tabler:search-off" class="text-5xl text-slate-600 animate-bounce"></iconify-icon>
        <h3 class="text-lg font-bold text-slate-300 mt-4">Nenhum veículo encontrado</h3>
        <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">
          Tente ajustar seus termos de busca ou filtros para ver outras opções de carros incríveis.
        </p>
      </div>
    </section>

    <!-- FLOATING DETAILS MODAL (Glassmorphic) -->
    <div 
      v-if="selectedVehicle" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
      @click.self="closeDetails"
    >
      <div class="bg-slate-900/90 border border-slate-800 max-w-2xl w-full rounded-3xl overflow-hidden shadow-2xl relative animate-in fade-in zoom-in duration-300">
        <button 
          @click="closeDetails"
          class="absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-slate-950/60 border border-slate-800 flex items-center justify-center text-slate-300 hover:text-white hover:bg-slate-800 transition"
        >
          <iconify-icon icon="tabler:x" class="text-lg"></iconify-icon>
        </button>

        <div class="relative aspect-video">
          <img :src="selectedVehicle.image" :alt="selectedVehicle.model" class="w-full h-full object-cover" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
          <div class="absolute bottom-4 left-6">
            <span class="px-2.5 py-1 rounded bg-indigo-600 text-[10px] font-bold text-white uppercase">{{ selectedVehicle.store_name }}</span>
            <h2 class="text-2xl font-extrabold text-white mt-1">{{ selectedVehicle.brand }} {{ selectedVehicle.model }}</h2>
            <p class="text-indigo-300 text-xs mt-0.5">{{ selectedVehicle.version }}</p>
          </div>
        </div>

        <div class="p-6 md:p-8 space-y-6">
          <!-- Quick Specs Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-950/40 p-4 rounded-2xl border border-slate-800/80">
            <div>
              <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Preço Anunciado</span>
              <span class="text-lg font-bold text-amber-400">{{ formatPrice(selectedVehicle.price) }}</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Ano Modelo</span>
              <span class="text-sm font-bold text-white">{{ selectedVehicle.year }}</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Quilometragem</span>
              <span class="text-sm font-bold text-white">{{ selectedVehicle.mileage.toLocaleString('pt-BR') }} km</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Combustível</span>
              <span class="text-sm font-bold text-white uppercase">{{ selectedVehicle.fuel }}</span>
            </div>
          </div>

          <!-- Lead Contact Form -->
          <div class="border-t border-slate-800 pt-6">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 mb-4">
              <iconify-icon icon="tabler:mail" class="text-indigo-400 text-lg"></iconify-icon>
              <span>Fale direto com a concessionária</span>
            </h3>

            <div v-if="showLeadSuccess" class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center gap-2 animate-in fade-in duration-200">
              <iconify-icon icon="tabler:circle-check" class="text-xl"></iconify-icon>
              <span class="text-sm font-semibold">Proposta enviada com sucesso! A loja entrará em contato em breve.</span>
            </div>

            <form v-else @submit.prevent="submitLead" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Seu Nome</label>
                  <input 
                    v-model="leadForm.name"
                    required
                    type="text" 
                    placeholder="Ex: Carlos Silva"
                    class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none transition-all"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">WhatsApp / Telefone</label>
                  <input 
                    v-model="leadForm.phone"
                    required
                    type="tel" 
                    placeholder="Ex: (84) 99999-9999"
                    class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none transition-all"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Mensagem de Interesse</label>
                <textarea 
                  v-model="leadForm.message"
                  required
                  rows="3"
                  class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none transition-all resize-none"
                ></textarea>
              </div>

              <button 
                type="submit"
                class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-bold text-sm text-white flex items-center justify-center gap-2 active:scale-[0.99] transition shadow-lg shadow-indigo-600/20"
              >
                <iconify-icon icon="tabler:brand-whatsapp" class="text-xl"></iconify-icon>
                <span>Enviar Proposta via AutoHub</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- MAIN FOOTER -->
    <footer class="bg-slate-950 border-t border-slate-800 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
        <div>
          <span class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-indigo-400 to-amber-400 bg-clip-text text-transparent">AutoHub Marketplace</span>
          <p class="text-xs text-slate-500 mt-1">© 2026 AutoHub S.A. Todos os direitos reservados. Feito no Brasil.</p>
        </div>
        
        <div class="flex items-center gap-4">
          <a href="#" class="text-slate-400 hover:text-white transition text-xs font-medium">Termos de Uso</a>
          <span class="h-3 w-px bg-slate-800"></span>
          <a href="#" class="text-slate-400 hover:text-white transition text-xs font-medium">Política de Privacidade</a>
          <span class="h-3 w-px bg-slate-800"></span>
          <a href="/admin" target="_blank" class="text-amber-400 hover:text-amber-300 transition text-xs font-bold">Administração</a>
        </div>
      </div>
    </footer>
  </div>
</template>
