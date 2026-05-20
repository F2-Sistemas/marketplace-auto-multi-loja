<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvancedFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Helper to insert or update plans
        $upsertPlan = function (array $planData) {
            $plan = DB::table('plans')->where('slug', $planData['slug'])->first();
            if ($plan) {
                DB::table('plans')->where('id', $plan->id)->update(array_merge($planData, [
                    'updated_at' => now(),
                ]));
                return $plan->id;
            } else {
                return DB::table('plans')->insertGetId(array_merge($planData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        };

        // 1. Seed Plans
        $planMonthlyId = $upsertPlan([
            'name' => 'Mensal',
            'slug' => 'mensal',
            'description' => 'Ideal para pequenas revendas testando a plataforma',
            'price' => 199.00,
            'limit_vehicles' => 10,
            'features' => json_encode(['Filtros Básicos', 'Suporte por E-mail', '1 Usuário']),
        ]);

        $planSemiannualId = $upsertPlan([
            'name' => 'Semestral',
            'slug' => 'semestral',
            'description' => 'Melhor custo-benefício para lojas consolidadas',
            'price' => 999.00,
            'limit_vehicles' => 50,
            'features' => json_encode(['Filtros Avançados', 'Suporte Prioritário', '3 Usuários', 'Domínio Customizado']),
        ]);

        $planAnnualId = $upsertPlan([
            'name' => 'Anual',
            'slug' => 'anual',
            'description' => 'Plano corporativo completo sem limites',
            'price' => 1800.00,
            'limit_vehicles' => -1,
            'features' => json_encode(['Sem limites de veículos', 'Suporte 24h via WhatsApp', 'Usuários Ilimitados', 'Analytics Completo']),
        ]);

        // 2. Add Subscriptions and Billing History for existing Stores
        $stores = DB::table('stores')->get();
        $gerenteNatal = DB::table('users')->where('email', 'gerente.natal@autocar.com')->first();
        $gerenteSP = DB::table('users')->where('email', 'gerente.sp@spveiculos.com')->first();
        $adminCentral = DB::table('users')->where('role', 'admin')->first();

        foreach ($stores as $store) {
            $planId = $store->slug === 'autocar-natal' ? $planAnnualId : ($store->slug === 'sp-veiculos' ? $planSemiannualId : $planMonthlyId);
            $price = $store->slug === 'autocar-natal' ? 1800.00 : ($store->slug === 'sp-veiculos' ? 999.00 : 199.00);

            // Add Subscription record if not exists
            $subExists = DB::table('subscriptions')->where('store_id', $store->id)->exists();
            if (!$subExists) {
                DB::table('subscriptions')->insert([
                    'store_id' => $store->id,
                    'plan_id' => $planId,
                    'status' => 'active',
                    'trial_ends_at' => now()->subMonths(3),
                    'ends_at' => now()->addMonths(9),
                    'created_at' => now()->subMonths(3),
                    'updated_at' => now(),
                ]);
            }

            // Add Billing History (last 3 payments)
            $methods = ['credit_card', 'pix', 'boleto'];
            for ($i = 3; $i >= 1; $i--) {
                $paymentExists = DB::table('subscription_payments')
                    ->where('store_id', $store->id)
                    ->where('receipt_url', "https://api.rederevenda.com/receipts/rec-" . $store->id . "-" . $i . ".pdf")
                    ->exists();

                if (!$paymentExists) {
                    DB::table('subscription_payments')->insert([
                        'store_id' => $store->id,
                        'amount' => $price,
                        'payment_method' => $methods[($store->id + $i) % 3],
                        'status' => 'paid',
                        'billing_date' => now()->subMonths($i),
                        'receipt_url' => "https://api.rederevenda.com/receipts/rec-" . $store->id . "-" . $i . ".pdf",
                        'created_at' => now()->subMonths($i),
                        'updated_at' => now()->subMonths($i),
                    ]);
                }
            }
        }

        // 3. Seed Support Tickets
        if ($gerenteNatal && $adminCentral) {
            $natalStoreId = DB::table('store_users')->where('user_id', $gerenteNatal->id)->value('store_id');
            if ($natalStoreId) {
                // Ticket 1: Técnico (Aberto)
                $ticket1 = DB::table('support_tickets')
                    ->where('store_id', $natalStoreId)
                    ->where('title', 'Problema ao fazer upload da logo da loja')
                    ->first();

                if (!$ticket1) {
                    $ticket1Id = DB::table('support_tickets')->insertGetId([
                        'store_id' => $natalStoreId,
                        'user_id' => $gerenteNatal->id,
                        'title' => 'Problema ao fazer upload da logo da loja',
                        'category' => 'tecnico',
                        'priority' => 'high',
                        'status' => 'aberto',
                        'created_at' => now()->subDays(2),
                        'updated_at' => now()->subDays(1),
                    ]);

                    DB::table('support_ticket_messages')->insert([
                        [
                            'ticket_id' => $ticket1Id,
                            'user_id' => $gerenteNatal->id,
                            'message' => 'Estou tentando carregar a logo em formato PNG de 2MB mas está dando erro.',
                            'is_agent' => false,
                            'attachments' => json_encode(['https://api.rederevenda.com/uploads/errors/error-logo.png']),
                            'created_at' => now()->subDays(2),
                            'updated_at' => now()->subDays(2),
                        ]
                    ]);

                    DB::table('support_ticket_timeline')->insert([
                        [
                            'ticket_id' => $ticket1Id,
                            'user_id' => $gerenteNatal->id,
                            'action_type' => 'status_change',
                            'description' => 'Chamado criado com status Aberto',
                            'metadata' => null,
                            'created_at' => now()->subDays(2),
                            'updated_at' => now()->subDays(2),
                        ]
                    ]);
                }
            }

            // Ticket 2: Financeiro (Respondido/Resolvido)
            if ($gerenteSP) {
                $storeSpId = DB::table('store_users')->where('user_id', $gerenteSP->id)->value('store_id');
                if ($storeSpId) {
                    $ticket2 = DB::table('support_tickets')
                        ->where('store_id', $storeSpId)
                        ->where('title', 'Dúvida sobre cobrança via PIX')
                        ->first();

                    if (!$ticket2) {
                        $ticket2Id = DB::table('support_tickets')->insertGetId([
                            'store_id' => $storeSpId,
                            'user_id' => $gerenteSP->id,
                            'title' => 'Dúvida sobre cobrança via PIX',
                            'category' => 'financeiro',
                            'priority' => 'medium',
                            'status' => 'respondido',
                            'created_at' => now()->subDays(5),
                            'updated_at' => now()->subDays(4),
                        ]);

                        DB::table('support_ticket_messages')->insert([
                            [
                                'ticket_id' => $ticket2Id,
                                'user_id' => $gerenteSP->id,
                                'message' => 'Gostaria de saber se posso pagar a mensalidade do plano semestral via PIX com desconto.',
                                'is_agent' => false,
                                'attachments' => null,
                                'created_at' => now()->subDays(5),
                                'updated_at' => now()->subDays(5),
                            ],
                            [
                                'ticket_id' => $ticket2Id,
                                'user_id' => $adminCentral->id,
                                'message' => 'Olá! Sim, para pagamento via PIX do plano semestral concedemos 5% de desconto. Segue o QR Code em anexo.',
                                'is_agent' => true,
                                'attachments' => json_encode(['https://api.rederevenda.com/uploads/billing/qrcode-pix.png']),
                                'created_at' => now()->subDays(4),
                                'updated_at' => now()->subDays(4),
                            ]
                        ]);

                        DB::table('support_ticket_timeline')->insert([
                            [
                                'ticket_id' => $ticket2Id,
                                'user_id' => $gerenteSP->id,
                                'action_type' => 'status_change',
                                'description' => 'Chamado criado com status Aberto',
                                'metadata' => null,
                                'created_at' => now()->subDays(5),
                                'updated_at' => now()->subDays(5),
                            ],
                            [
                                'ticket_id' => $ticket2Id,
                                'user_id' => $adminCentral->id,
                                'action_type' => 'status_change',
                                'description' => 'Atendente respondeu ao chamado. Status alterado para Respondido.',
                                'metadata' => null,
                                'created_at' => now()->subDays(4),
                                'updated_at' => now()->subDays(4),
                            ]
                        ]);
                    }
                }
            }
        }

        // 4. Seed user favorites
        $buyer = DB::table('users')->where('email', 'comprador@gmail.com')->first();
        $vehicles = DB::table('vehicles')->take(3)->get();
        if ($buyer) {
            foreach ($vehicles as $vehicle) {
                DB::table('favorites')->insertOrIgnore([
                    'user_id' => $buyer->id,
                    'vehicle_id' => $vehicle->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

