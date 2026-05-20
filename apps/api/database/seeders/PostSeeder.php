<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'GWM Poer híbrida é flagrada em testes finais no interior de SP',
                'slug' => 'gwm-poer-hibrida-flagrada-testes-sp',
                'summary' => 'Nova picape média da GWM foi flagrada rodando em testes em estradas paulistas antes de seu lançamento oficial.',
                'content' => "A GWM está acelerando os testes da nova picape média Poer híbrida no Brasil. O modelo foi flagrado camuflado rodando na região de Iracemápolis (SP), onde a marca chinesa está preparando sua linha de montagem nacional.

O visual chama a atenção pela robustez e o conjunto ótico em LED atualizado. Espera-se que a motorização traga o sistema híbrido plug-in da linha Haval H6, associando um motor a combustão turbo com um ou dois propulsores elétricos, totalizando mais de 300 cv de potência combinada.

### O que esperar da nova picape?
* **Desempenho:** Torque imediato proporcionado pelos motores elétricos.
* **Consumo:** Promete médias acima de 15 km/l em ciclo misto.
* **Capacidade:** Caçamba reforçada e capacidade de carga superior a 1 tonelada.

A previsão de lançamento é para o segundo semestre de 2026, posicionando a GWM de forma competitiva no concorrido segmento de picapes médias, que hoje conta com Toyota Hilux, Ford Ranger e Chevrolet S10.",
                'image_url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=600&auto=format&fit=crop',
                'category' => 'Notícias',
                'views_count' => 1450,
                'status' => 'published',
                'related_links' => [
                    [
                        'title' => 'Toyota Hilux vs Ford Ranger: Comparativo de Picapes',
                        'url' => '/noticias/toyota-hilux-vs-ford-ranger-comparativo'
                    ],
                    [
                        'title' => 'GWM confirma início de montagem em Iracemápolis',
                        'url' => '/noticias/gwm-confirma-inicio-montagem-iracemapolis'
                    ]
                ],
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Fiat Strada vs Chevrolet Montana: Qual é a melhor para o dia a dia?',
                'slug' => 'fiat-strada-vs-chevrolet-montana-comparativo',
                'summary' => 'Colocamos frente a frente as duas picapes compactas mais vendidas do país para analisar consumo, conforto e capacidade.',
                'content' => "Se você está na dúvida entre a Fiat Strada Ultra Turbo e a Chevrolet Montana Premier, preparamos este comparativo detalhado focando na usabilidade urbana e no trabalho leve diário.

Tanto a Strada quanto a Montana trazem propostas ligeiramente diferentes, embora atuem em faixas de preço muito próximas nas suas configurações mais completas.

### Comparação Técnica

| Característica | Fiat Strada Ultra Turbo | Chevrolet Montana Premier |
| --- | --- | --- |
| Motor | 1.0 Turbo Flex | 1.2 Turbo Flex |
| Potência | 130 cv | 133 cv |
| Câmbio | CVT (7 marchas sim.) | Automático de 6 marchas |
| Caçamba | 844 litros | 874 litros |

### Conclusão e Veredicto
Se o seu foco principal é o custo de manutenção e a revenda super aquecida, a Fiat Strada continua sendo a escolha lógica de mercado. Por outro lado, para quem prioriza o espaço interno, o conforto de rodagem semelhante ao de um SUV e recursos adicionais de tecnologia, a Chevrolet Montana justifica o investimento extra.",
                'image_url' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?q=80&w=600&auto=format&fit=crop',
                'category' => 'Comparativos',
                'views_count' => 3120,
                'status' => 'published',
                'related_links' => [
                    [
                        'title' => 'Teste completo: Fiat Strada Turbo Flex 2026',
                        'url' => '/noticias/teste-completo-fiat-strada-turbo-flex'
                    ]
                ],
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Como economizar combustível na cidade: 5 dicas cruciais',
                'slug' => 'como-economizar-combustivel-cidade-dicas',
                'summary' => 'Com a escalada dos preços dos combustíveis, pequenas mudanças nos hábitos de condução podem render até 20% de economia.',
                'content' => "Dirigir nos grandes centros urbanos exige atenção constante e, infelizmente, costuma elevar bastante o consumo de combustível. Contudo, adotando técnicas simples de direção defensiva e econômica, você pode reduzir consideravelmente os seus gastos mensais no posto.

Aqui estão 5 dicas fáceis que fazem toda a diferença:

1. **Evite acelerações bruscas:** Ganhe velocidade de forma gradual.
2. **Mantenha a calibragem em dia:** Pneus murchos aumentam o atrito com o asfalto.
3. **Use o freio motor:** Antecipe as paradas nos semáforos tirando o pé do acelerador.
4. **Desligue o ar-condicionado em dias amenos:** O compressor consome potência do motor.
5. **Não carregue peso desnecessário:** Limpe o porta-malas de ferramentas e tralhas pesadas.",
                'image_url' => 'https://images.unsplash.com/photo-1486006920555-c77dce18193b?q=80&w=600&auto=format&fit=crop',
                'category' => 'Dicas',
                'views_count' => 980,
                'status' => 'published',
                'related_links' => [],
                'published_at' => now()->subHours(12),
            ],
        ];

        foreach ($posts as $postData) {
            Post::updateOrCreate(['slug' => $postData['slug']], $postData);
        }
    }
}

