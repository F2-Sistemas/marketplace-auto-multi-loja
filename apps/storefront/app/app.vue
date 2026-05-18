<script setup lang="ts">
import { ref, computed } from 'vue'

interface StoreDetails {
  name: string
  slug: string
  theme: string
  primaryColor: string
  accentColor: string
  city: string
  state: string
  phone: string
  address: string
  whatsapp: string
  tagline: string
  logoIcon: string
  accentGradient: string
  buttonClass: string
}

// Pre-defined tenant stores
const tenants: Record<string, StoreDetails> = {
  'natal-motors': {
    name: 'Natal Motors',
    slug: 'natal-motors',
    theme: 'amber',
    primaryColor: 'from-amber-500 to-orange-600',
    accentColor: 'text-amber-400',
    city: 'Natal',
    state: 'RN',
    phone: '(84) 3222-1000',
    address: 'Av. Prudente de Morais, 4000 - Lagoa Nova',
    whatsapp: '5584999998888',
    tagline: 'Líder em Seminovos e Premium no Rio Grande do Norte',
    logoIcon: 'tabler:sun',
    accentGradient: 'from-amber-400 to-orange-500',
    buttonClass: 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20'
  },
  'sp-veiculos': {
    name: 'SP Veículos',
    slug: 'sp-veiculos',
    theme: 'red',
    primaryColor: 'from-red-600 to-rose-700',
    accentColor: 'text-red-400',
    city: 'São Paulo',
    state: 'SP',
    phone: '(11) 5500-2020',
    address: 'Av. Europa, 1500 - Jardim Europa',
    whatsapp: '5511988887777',
    tagline: 'Os esportivos e importados mais exclusivos de São Paulo',
    logoIcon: 'tabler:building-skyscraper',
    accentGradient: 'from-red-500 to-rose-600',
    buttonClass: 'bg-red-600 hover:bg-red-700 shadow-red-600/20'
  },
  'euro-select': {
    name: 'Euro Select',
    slug: 'euro-select',
    theme: 'blue',
    primaryColor: 'from-blue-600 to-indigo-700',
    accentColor: 'text-blue-400',
    city: 'Curitiba',
    state: 'PR',
    phone: '(41) 3340-9000',
    address: 'Rua General Mário Tourinho, 2200 - Seminário',
    whatsapp: '5541977776666',
    tagline: 'Alta costura automotiva: Importados Selecionados',
    logoIcon: 'tabler:crown',
    accentGradient: 'from-blue-400 to-indigo-500',
    buttonClass: 'bg-blue-600 hover:bg-blue-700 shadow-blue-600/20'
  }
}

// Simulated active tenant
const currentTenantKey = ref('natal-motors')
const currentStore = computed(() => tenants[currentTenantKey.value])

// Real-time simulated client-side vehicles list for each tenant
interface Vehicle {
  id: number
  store_slug: string
  brand: string
  model: string
  version: string
  price: number
  year: string
  mileage: number
  transmission: string
  fuel: string
  image: string
}

const allVehicles = ref<Vehicle[]>([
  {
    id: 1,
    store_slug: 'natal-motors',
    brand: 'Toyota',
    model: 'Corolla',
    version: 'GLi 2.0 Flex automatico',
    price: 85000,
    year: '2018/2019',
    mileage: 45000,
    transmission: 'automatico',
    fuel: 'flex',
    image: 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop'
  },
  {
    id: 2,
    store_slug: 'natal-motors',
    brand: 'Toyota',
    model: 'Corolla',
    version: 'Altis 2.0 Flex automatico',
    price: 120000,
    year: '2021/2022',
    mileage: 12000,
    transmission: 'automatico',
    fuel: 'flex',
    image: 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop'
  },
  {
    id: 3,
    store_slug: 'natal-motors',
    brand: 'Volkswagen',
    model: 'Polo',
    version: 'Comfortline 1.0 TSI automatico',
    price: 89000,
    year: '2022/2023',
    mileage: 18000,
    transmission: 'automatico',
    fuel: 'flex',
    image: 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?q=80&w=800&auto=format&fit=crop'
  },
  {
    id: 4,
    store_slug: 'sp-veiculos',
    brand: 'Honda',
    model: 'Civic',
    version: 'EXL 2.0 automatico',
    price: 115000,
    year: '2020/2020',
    mileage: 32000,
    transmission: 'automatico',
    fuel: 'flex',
    image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=800&auto=format&fit=crop'
  },
  {
    id: 5,
    store_slug: 'sp-veiculos',
    brand: 'Jeep',
    model: 'Compass',
    version: 'Longitude 2.0 Diesel 4x4',
    price: 145000,
    year: '2019/2020',
    mileage: 58000,
    transmission: 'automatico',
    fuel: 'diesel',
    image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=800&auto=format&fit=crop'
  },
  {
    id: 6,
    store_slug: 'euro-select',
    brand: 'BMW',
    model: '320i',
    version: 'GP 2.0 ActiveFlex automatico',
    price: 198000,
    year: '2021/2021',
    mileage: 24000,
    transmission: 'automatico',
    fuel: 'flex',
    image: 'https://images.unsplash.com/photo-1555215695-3004980ad54e?q=80&w=800&auto=format&fit=crop'
  }
])

const activeVehicles = computed(() => {
  return allVehicles.value.filter(v => v.store_slug === currentStore.value.slug)
})

// Search states
const searchQuery = ref('')
const filteredVehicles = computed(() => {
  return activeVehicles.value.filter(v => {
    return !searchQuery.value || 
      `${v.brand} ${v.model} ${v.version}`.toLowerCase().includes(searchQuery.value.toLowerCase())
  })
})

const selectedVehicle = ref<Vehicle | null>(null)
const leadForm = ref({ name: '', phone: '', message: '' })
const showLeadSuccess = ref(false)

const openVehicleDetails = (vehicle: Vehicle) => {
  selectedVehicle.value = vehicle
  leadForm.value.message = `Olá! Gostaria de falar com o consultor de vendas da ${currentStore.value.name} sobre o ${vehicle.brand} ${vehicle.model} anunciado por R$ ${vehicle.price.toLocaleString('pt-BR')}.`
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
  <div class="min-h-screen bg-slate-950 text-slate-100 font-['Outfit'] antialiased">
    
    <!-- DEMO TENANT TOGGLER (Floating Dashboard) -->
    <div class="fixed bottom-6 right-6 z-50 bg-slate-900/90 border border-slate-800 backdrop-blur-xl p-4 rounded-2xl shadow-2xl max-w-xs">
      <span class="text-[10px] font-extrabold uppercase text-indigo-400 tracking-wider flex items-center gap-1 mb-2">
        <iconify-icon icon="tabler:cpu" class="animate-spin"></iconify-icon>
        <span>Simulador de Tenant (Domínio)</span>
      </span>
      <p class="text-xs text-slate-400 mb-3 leading-snug">
        Altere o tenant ativo para testar a personalização visual, cores e sitemap dinâmicos do storefront.
      </p>
      <div class="space-y-1.5">
        <button 
          v-for="(t, key) in tenants" 
          :key="key"
          @click="currentTenantKey = key"
          :class="[
            'w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold border transition',
            currentTenantKey === key 
              ? 'bg-indigo-600/10 border-indigo-500/30 text-indigo-300' 
              : 'bg-slate-950/40 border-slate-800 text-slate-400 hover:text-slate-200'
          ]"
        >
          <span class="flex items-center gap-1.5">
            <iconify-icon :icon="t.logoIcon"></iconify-icon>
            {{ t.name }}
          </span>
          <span class="text-[9px] font-bold text-slate-500 uppercase">{{ t.city }}</span>
        </button>
      </div>
    </div>

    <!-- MAIN BRAND HEADER -->
    <header class="sticky top-0 z-40 bg-slate-950/90 backdrop-blur-xl border-b border-slate-850">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        
        <!-- DYNAMIC LOGO -->
        <div class="flex items-center gap-3">
          <div :class="['w-10 h-10 rounded-xl bg-gradient-to-tr flex items-center justify-center text-white shadow-lg', currentStore.primaryColor]">
            <iconify-icon :icon="currentStore.logoIcon" class="text-2xl"></iconify-icon>
          </div>
          <div>
            <span class="font-extrabold text-2xl tracking-tight text-white">{{ currentStore.name }}</span>
            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-widest leading-none">
              Exclusividade & Confiabilidade
            </span>
          </div>
        </div>

        <!-- HEADER INFO -->
        <div class="hidden lg:flex items-center gap-6">
          <div class="flex items-center gap-2">
            <iconify-icon icon="tabler:phone" class="text-indigo-400 text-lg"></iconify-icon>
            <div>
              <span class="text-[10px] text-slate-500 block uppercase font-bold leading-none">Ligue agora</span>
              <span class="text-xs text-slate-300 font-semibold">{{ currentStore.phone }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <iconify-icon icon="tabler:map-pin" class="text-indigo-400 text-lg"></iconify-icon>
            <div>
              <span class="text-[10px] text-slate-500 block uppercase font-bold leading-none">Localização</span>
              <span class="text-xs text-slate-300 font-semibold">{{ currentStore.city }} - {{ currentStore.state }}</span>
            </div>
          </div>
        </div>

        <!-- WHATSAPP CTA -->
        <a 
          :href="`https://wa.me/${currentStore.whatsapp}`" 
          target="_blank"
          :class="['flex items-center gap-2 px-4 py-2 rounded-xl text-white font-bold text-sm transition shadow-lg', currentStore.buttonClass]"
        >
          <iconify-icon icon="tabler:brand-whatsapp" class="text-lg"></iconify-icon>
          <span>WhatsApp Loja</span>
        </a>
      </div>
    </header>

    <!-- DYNAMIC BANNER HERO -->
    <section class="relative py-24 overflow-hidden border-b border-slate-900">
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(99,102,241,0.08),transparent_50%)]"></div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="px-3 py-1 rounded-full bg-slate-900 border border-slate-800 text-slate-400 text-xs font-bold uppercase tracking-wider">
          Vitrine Autorizada de Automóveis
        </span>
        <h1 class="mt-6 text-4xl sm:text-6xl font-extrabold tracking-tight text-white">
          Bem-vindo à<br />
          <span :class="['bg-gradient-to-r bg-clip-text text-transparent', currentStore.primaryColor]">{{ currentStore.name }}</span>
        </h1>
        <p class="mt-6 text-lg text-slate-400 max-w-2xl mx-auto font-medium">
          {{ currentStore.tagline }}
        </p>

        <!-- SEARCH BOX -->
        <div class="mt-10 max-w-lg mx-auto relative">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
            <iconify-icon icon="tabler:search"></iconify-icon>
          </span>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="O que você está procurando hoje?" 
            class="w-full bg-slate-900 border border-slate-800 focus:border-indigo-500 rounded-2xl py-4 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-500 focus:outline-none transition-all"
          />
        </div>
      </div>
    </section>

    <!-- VEHICLES SECTION -->
    <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8 border-b border-slate-900 pb-4">
        <div>
          <h2 class="text-xl font-bold text-white">Nosso Estoque Exclusivo</h2>
          <p class="text-xs text-slate-500 mt-1">Todos os veículos passam por laudo cautelar 100% aprovado</p>
        </div>
        
        <span class="text-xs text-slate-400">
          Encontrados: <strong class="text-white">{{ filteredVehicles.length }}</strong>
        </span>
      </div>

      <!-- VEHICLE GRID -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="v in filteredVehicles" 
          :key="v.id" 
          class="group bg-slate-900/40 hover:bg-slate-900/80 border border-slate-900 hover:border-slate-800 rounded-2xl overflow-hidden transition-all duration-300 flex flex-col hover:-translate-y-1 shadow-lg hover:shadow-indigo-500/5"
        >
          <!-- Vehicle Image -->
          <div class="relative aspect-video overflow-hidden">
            <img 
              :src="v.image" 
              :alt="`${v.brand} ${v.model}`" 
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
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
              <p class="text-slate-500 text-xs mt-1">{{ v.version }}</p>

              <!-- Specifications Badges -->
              <div class="flex flex-wrap gap-2 mt-4">
                <span class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-950 text-[11px] text-slate-400 border border-slate-850">
                  <iconify-icon icon="tabler:road"></iconify-icon>
                  {{ v.mileage.toLocaleString('pt-BR') }} km
                </span>
                <span class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-950 text-[11px] text-slate-400 border border-slate-850">
                  <iconify-icon icon="tabler:settings"></iconify-icon>
                  {{ v.transmission }}
                </span>
                <span class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-950 text-[11px] text-slate-400 border border-slate-850">
                  <iconify-icon icon="tabler:droplet"></iconify-icon>
                  {{ v.fuel }}
                </span>
              </div>
            </div>

            <!-- Price & Action -->
            <div class="mt-6 pt-5 border-t border-slate-900 flex items-center justify-between">
              <div>
                <span class="text-[10px] text-slate-500 block uppercase tracking-wider font-semibold">Preço Especial</span>
                <span class="text-xl font-extrabold text-amber-400">{{ formatPrice(v.price) }}</span>
              </div>
              <button 
                @click="openVehicleDetails(v)"
                class="flex items-center gap-1 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-750 text-white font-semibold text-xs transition"
              >
                <span>Falar com Consultor</span>
                <iconify-icon icon="tabler:chevron-right"></iconify-icon>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- EMPTY STATE -->
      <div v-if="filteredVehicles.length === 0" class="mt-16 text-center py-20 bg-slate-900/20 rounded-3xl border border-slate-900">
        <iconify-icon icon="tabler:search-off" class="text-5xl text-slate-700 animate-bounce"></iconify-icon>
        <h3 class="text-lg font-bold text-slate-400 mt-4">Nenhum automóvel encontrado</h3>
        <p class="text-slate-500 text-sm mt-2">Tente digitar outros termos de pesquisa.</p>
      </div>
    </section>

    <!-- FLOATING DETAILS MODAL -->
    <div 
      v-if="selectedVehicle" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
      @click.self="closeDetails"
    >
      <div class="bg-slate-900 border border-slate-800 max-w-2xl w-full rounded-3xl overflow-hidden shadow-2xl relative animate-in fade-in zoom-in duration-300">
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
            <h2 class="text-2xl font-extrabold text-white">{{ selectedVehicle.brand }} {{ selectedVehicle.model }}</h2>
            <p class="text-slate-400 text-xs mt-0.5">{{ selectedVehicle.version }}</p>
          </div>
        </div>

        <div class="p-6 md:p-8 space-y-6">
          <!-- Quick Specs Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-950/60 p-4 rounded-2xl border border-slate-850">
            <div>
              <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Preço</span>
              <span class="text-lg font-bold text-amber-400">{{ formatPrice(selectedVehicle.price) }}</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Ano</span>
              <span class="text-sm font-bold text-white">{{ selectedVehicle.year }}</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Quilometragem</span>
              <span class="text-sm font-bold text-white">{{ selectedVehicle.mileage.toLocaleString('pt-BR') }} km</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Câmbio</span>
              <span class="text-sm font-bold text-white uppercase">{{ selectedVehicle.transmission }}</span>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="border-t border-slate-800 pt-6">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 mb-4">
              <iconify-icon icon="tabler:message" class="text-indigo-400 text-lg"></iconify-icon>
              <span>Envie sua Proposta para o Whatsapp</span>
            </h3>

            <div v-if="showLeadSuccess" class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center gap-2 animate-in fade-in duration-200">
              <iconify-icon icon="tabler:circle-check" class="text-xl"></iconify-icon>
              <span class="text-sm font-semibold">Redirecionando para o consultor da {{ currentStore.name }} no WhatsApp...</span>
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
                    class="w-full bg-slate-950 border border-slate-850 focus:border-indigo-500 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none transition-all"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">WhatsApp / Telefone</label>
                  <input 
                    v-model="leadForm.phone"
                    required
                    type="tel" 
                    placeholder="Ex: (84) 99999-9999"
                    class="w-full bg-slate-950 border border-slate-850 focus:border-indigo-500 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none transition-all"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Mensagem</label>
                <textarea 
                  v-model="leadForm.message"
                  required
                  rows="3"
                  class="w-full bg-slate-950 border border-slate-850 focus:border-indigo-500 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none transition-all resize-none"
                ></textarea>
              </div>

              <button 
                type="submit"
                :class="['w-full py-3 rounded-xl font-bold text-sm text-white flex items-center justify-center gap-2 active:scale-[0.99] transition shadow-lg', currentStore.buttonClass]"
              >
                <iconify-icon icon="tabler:brand-whatsapp" class="text-xl"></iconify-icon>
                <span>Falar via WhatsApp</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-950 border-t border-slate-900 py-16 text-slate-500 text-xs text-center">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="flex items-center justify-center gap-2 text-slate-400">
          <iconify-icon :icon="currentStore.logoIcon"></iconify-icon>
          <span class="font-extrabold text-sm tracking-tight">{{ currentStore.name }}</span>
        </div>
        <p class="max-w-md mx-auto leading-relaxed">
          {{ currentStore.address }}<br />
          Telefone: {{ currentStore.phone }} | WhatsApp: {{ currentStore.phone }}
        </p>
        <div class="h-px bg-slate-900 max-w-sm mx-auto"></div>
        <p>© 2026 {{ currentStore.name }} - Todos os direitos reservados. Portal Parceiro AutoHub.</p>
      </div>
    </footer>
  </div>
</template>
