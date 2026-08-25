<?php

namespace App\Controllers;

use App\Core\Controller;

class ServicesController extends Controller {

    private function services() {
        $wa = 'https://wa.me/556732537898?text=Oi!%20Quero%20um%20or%C3%A7amento%20de%20';
        return [
            'cozinha-planejada' => [
                'slug' => 'cozinha-planejada',
                'titulo' => 'Cozinha Planejada — Execução Técnica',
                'h1' => 'Cozinha <span>Planejada</span>',
                'eyebrow' => 'Cozinhas',
                'subtitle' => 'Projeto executado 100% em MDF, com ferragens com amortecimento e acabamento em melamina ou laca. Medição no local, projeto 3D e instalação inclusa.',
                'descricao' => 'A cozinha é um dos ambientes com maior exigência técnica: umidade, gordura, calor e peso de utensílios exigem especificação correta de materiais e ferragens. Projetamos cada módulo em MDF com especificação de espessura conforme a função da peça, com ferragens de fechamento suave e sistema de extração total nas gavetas.',
                'imagem' => BASE_URL . '/assets/img/cozinha/IMG-20250507-WA0041.jpg',
                'eyebrow' => 'Cozinhas',
                'wa_message' => 'cozinha%20planejada',
                'prazo' => '35 a 45 dias úteis',
                'materiais' => [
                    ['nome' => 'MDF 18 mm', 'detalhe' => 'estrutura dos armários baixos e altos, prateleiras e painéis traseiros'],
                    ['nome' => 'MDF 18 mm', 'detalhe' => 'frentes de portas e gavetas, nichos e painéis decorativos'],
                    ['nome' => 'Melamina BP / Laca', 'detalhe' => 'acabamento superficial resistente à umidade e manchas'],
                    ['nome' => 'Borda PVC 2 mm', 'detalhe' => 'selamento de laterais expostas, evita infiltração de umidade'],
                ],
                'processo' => [
                    'Visita técnica: medição a laser, mapeamento de hidráulica e elétrica, levantamento de eletrodomésticos',
                    'Projeto 3D com renders + projeto executivo com lista de peças e plano de corte',
                    'Aprovação do cliente antes do início da produção',
                    'Corte CNC e usinagem com furação posicionada (tolerância 0,2 mm)',
                    'Acabamento de bordas e pré-montagem com inspeção de qualidade',
                    'Entrega e instalação por equipe treinada, com regulagem de ferragens',
                ],
                'beneficios' => [
                    'Todo o móvel em MDF: estrutura e frentes com o mesmo padrão de qualidade',
                    'Ferragens com amortecimento (soft-close) e corrediças de extração total',
                    'Projeto aprovado antes de fabricar — sem surpresas',
                    'Acabamento em melamina ou laca, conforme o estilo do ambiente',
                    'Instalação inclusa com regulagem individual de portas e gavetas',
                ],
                'aplicacoes' => [
                    ['icone' => 'fa-home', 'titulo' => 'Cozinhas integradas', 'descricao' => 'Armários baixos, altos, ilha e bancada em um projeto unificado.'],
                    ['icone' => 'fa-blender', 'titulo' => 'Áreas gourmet', 'descricao' => 'Bancadas amplas, nichos para eletroportáteis e iluminação embutida.'],
                    ['icone' => 'fa-store', 'titulo' => 'Cozinhas de apartamento', 'descricao' => 'Soluções compactas com aproveitamento de cada centímetro.'],
                ],
                'faq' => [
                    ['p' => 'Qual o prazo para uma cozinha planejada?', 'r' => 'Em média 35 a 45 dias úteis após aprovação do projeto, incluindo medição, fabricação e instalação. Projetos mais complexos (ilha + painel + armários) podem chegar a 50 dias.'],
                    ['p' => 'Qual espessura de MDF usamos na cozinha?', 'r' => 'Na LC Soluções em Móveis usamos MDF 18 mm em todas as peças da cozinha: estrutura, frentes, prateleiras e painéis. Essa espessura garante resistência para suportar o peso de utensílios e panelas. Em áreas com umidade, indicamos MDF com bordas seladas com fita PVC ou melamina hidrófuga.'],
                    ['p' => 'As ferragens têm amortecimento?', 'r' => 'Sim. Trabalhamos com corrediças telescópicas de extração total e dobradiças com amortecor (soft-close). O fechamento suave evita batidas e aumenta a vida útil tanto do móvel quanto das ferragens.'],
                    ['p' => 'O orçamento é gratuito?', 'r' => 'Sim. A visita técnica, a medição a laser, o projeto 3D e o orçamento são gratuitos e sem compromisso. Você só avança se o projeto e o valor estiverem de acordo com o esperado.'],
                ],
            ],
            'closet-e-quarto-planejado' => [
                'slug' => 'closet-e-quarto-planejado',
                'titulo' => 'Closet e Quarto Planejado — Execução Técnica',
                'h1' => 'Closet e <span>Quarto Planejado</span>',
                'eyebrow' => 'Dormitórios',
                'subtitle' => 'Closets com caixaria em MDF, portas em MDF e organização interna com gaveteiros, sapateiras e iluminação LED. Projeto 3D antes da fabricação.',
                'descricao' => 'O closet planejado é um sistema de organização personalizado: medimos o espaço, definimos o fluxo de roupas (arrumação, uso diário, guardados) e projetamos cada gaveta, prateleira e haste. A estrutura usa MDF para estabilidade, com frentes em MDF e iluminação LED opcional. O resultado é um ambiente onde tudo tem lugar definido.',
                'imagem' => BASE_URL . '/assets/img/quarto/quarto1.jpeg',
                'eyebrow' => 'Dormitórios',
                'wa_message' => 'closet%20planejado',
                'prazo' => '30 a 40 dias úteis',
                'materiais' => [
                    ['nome' => 'MDF 15/18 mm', 'detalhe' => 'estrutura dos módulos, prateleiras e divisórias internas'],
                    ['nome' => 'MDF 18 mm', 'detalhe' => 'portas de correr ou de abrir, frentes de gavetas e detalhes decorativos'],
                    ['nome' => 'Melamina / Laca', 'detalhe' => 'acabamento superficial em cores ou madeirados'],
                    ['nome' => 'Iluminação LED', 'detalhe' => 'perfis embutidos em nichos e cabideiros (opcional)'],
                ],
                'processo' => [
                    'Visita técnica: medição do ambiente, levantamento de roupas e acessórios a guardar',
                    'Projeto 3D com organização interna (gavetas, prateleiras, haste, sapateiras)',
                    'Aprovação do layout e dos materiais antes de fabricar',
                    'Corte CNC, furação posicionada e acabamento de bordas',
                    'Pré-montagem com verificação de funcionamento das ferragens',
                    'Instalação no local com regulagem de portas e nivelamento',
                ],
                'beneficios' => [
                    'Organização interna projetada para o seu uso real (roupas, sapatos, acessórios)',
                    'Portas de correr ou de abrir, conforme a metragem e o fluxo do ambiente',
                    'Iluminação LED embutida opcional (nichos, cabideiros, espelhos)',
                    'Acabamento em melamina ou laca, na cor que combina com o quarto',
                    'Sapateiras, gaveteiros e divisórias incluídos no projeto',
                ],
                'aplicacoes' => [
                    ['icone' => 'fa-bed', 'titulo' => 'Closets completos', 'descricao' => 'Sistemas de arrumação com gavetas, prateleiras, haste e sapateiras.'],
                    ['icone' => 'fa-child', 'titulo' => 'Quartos infantis', 'descricao' => 'Projetos que acompanham o crescimento: cama, escrivaninha e nichos.'],
                    ['icone' => 'fa-concierge-bell', 'titulo' => 'Suítes master', 'descricao' => 'Closet integrado à suíte, com iluminação e espelhos.'],
                ],
                'faq' => [
                    ['p' => 'Quanto custa um closet planejado?', 'r' => 'Depende do tamanho, dos materiais e da complexidade da organização interna. Após a visita técnica, apresentamos orçamento detalhado item por item, sem surpresas.'],
                    ['p' => 'Fazem quarto de criança?', 'r' => 'Sim. Projetamos quartos infantis com cama, escrivaninha, nichos e organização que acompanham o crescimento. O projeto pode incluir pontos para adaptações futuras.'],
                    ['p' => 'O projeto 3D é gratuito?', 'r' => 'Sim. Você recebe o projeto 3D com organização interna detalhada e o orçamento gratuitamente. Só inicia a produção após aprovação.'],
                ],
            ],
            'sala-e-painel-planejado' => [
                'slug' => 'sala-e-painel-planejado',
                'titulo' => 'Sala e Painel Planejado — Execução Técnica',
                'h1' => 'Sala e <span>Painel Planejado</span>',
                'eyebrow' => 'Salas',
                'subtitle' => 'Painéis para TV com estrutura em MDF e frentes em MDF, estantes e racks sob medida. Passagem de fiação oculta e iluminação LED embutida.',
                'descricao' => 'O painel de TV é mais do que um suporte: é o elemento central da sala. Projetamos painéis com estrutura em MDF para suportar o peso da TV e dos equipamentos, e frentes em MDF com acabamento em laca ou texturizado. A passagem de fiação é interna, com pontos de tomada estrategicamente posicionados.',
                'imagem' => BASE_URL . '/assets/img/sala/painel1.jpeg',
                'eyebrow' => 'Salas',
                'wa_message' => 'painel%20para%20sala',
                'prazo' => '30 a 40 dias úteis',
                'materiais' => [
                    ['nome' => 'MDF 18 mm', 'detalhe' => 'estrutura do painel, suportes e prateleiras'],
                    ['nome' => 'MDF 18 mm', 'detalhe' => 'frentes decorativas, nichos e detalhes de acabamento'],
                    ['nome' => 'Melamina / Laca', 'detalhe' => 'acabamento em cores ou texturas que combinam com o ambiente'],
                    ['nome' => 'Perfis de LED', 'detalhe' => 'iluminação embutida indireta (opcional)'],
                ],
                'processo' => [
                    'Visita técnica: medição da parede, posição da TV, pontos de tomada e equipamentos',
                    'Projeto 3D com renders e projeto executivo com detalhamento de fiação',
                    'Aprovação do cliente — layout, cores e posicionamento de cada elemento',
                    'Corte CNC com usinagem para nichos e passagem de cabos',
                    'Acabamento de bordas e instalação de perfis de LED (se solicitado)',
                    'Instalação no local com fixação na estrutura da parede e nivelamento',
                ],
                'beneficios' => [
                    'Passagem de fiação oculta — sem fios aparentes',
                    'Estrutura calculada para suportar o peso da TV e equipamentos',
                    'Nichos para TV, caixas de som, consoles e objetos decorativos',
                    'Acabamento em laca, melamina ou texturizado, conforme o estilo',
                    'Integração com home theater e iluminação ambiente',
                ],
                'aplicacoes' => [
                    ['icone' => 'fa-tv', 'titulo' => 'Painéis de TV', 'descricao' => 'Suporte estrutural para TVs de 32 a 85 polegadas, com fiação oculta.'],
                    ['icone' => 'fa-book', 'titulo' => 'Estantes e racks', 'descricao' => 'Prateleiras e nichos para livros, objetos e equipamentos.'],
                    ['icone' => 'fa-film', 'titulo' => 'Home theaters', 'descricao' => 'Painéis integrados com caixas de som, console e iluminação cênica.'],
                ],
                'faq' => [
                    ['p' => 'O painel esconde os fios da TV?', 'r' => 'Sim. O projeto inclui passagem de fiação interna e pontos de tomada estrategicamente posicionados. O resultado é um visual limpo, sem fios aparentes.'],
                    ['p' => 'Fazem painel com lareira?', 'r' => 'Sim. Intregamos lareiras elétnicas a gás (modelos aprovados para instalação embutida) ao projeto do painel. O dimensionamento técnico é feito na fase de projeto.'],
                    ['p' => 'Qual o prazo?', 'r' => 'Em média 30 a 40 dias úteis após aprovação do projeto. Painéis com iluminação LED embutida ou detalhes em laca podem exigir prazo adicional de 5 a 7 dias.'],
                ],
            ],
            'home-office-planejado' => [
                'slug' => 'home-office-planejado',
                'titulo' => 'Home Office Planejado — Execução Técnica',
                'h1' => 'Home Office <span>Planejado</span>',
                'eyebrow' => 'Escritório',
                'subtitle' => 'Mesa, prateleiras e nichos sob medida para home office. Estrutura em MDF, acabamento em MDF e passagem de cabos oculta.',
                'descricao' => 'O home office exige ergonomia e organização: altura de mesa adequada, espaço para monitor, teclado e documentos, além de prateleiras para livros e equipamentos. Projetamos cada peça com base nas dimensões do usuário e do ambiente, com estrutura em MDF e acabamento em MDF. A fiação de computador, monitor e iluminação passa por canaletas embutidas.',
                'imagem' => BASE_URL . '/assets/img/escritorio/IMG-20250507-WA0063.jpg',
                'eyebrow' => 'Escritório',
                'wa_message' => 'home%20office%20planejado',
                'prazo' => '25 a 35 dias úteis',
                'materiais' => [
                    ['nome' => 'MDF 18 mm', 'detalhe' => 'estrutura da mesa, prateleiras e painéis de fundo'],
                    ['nome' => 'MDF 18 mm', 'detalhe' => 'frentes de gavetas, painéis decorativos e detalhes de acabamento'],
                    ['nome' => 'Melamina / Laca', 'detalhe' => 'acabamento superficial em tons neutros ou madeirados'],
                    ['nome' => 'Canaletas embutidas', 'detalhe' => 'passagem de cabos de energia e dados (opcional)'],
                ],
                'processo' => [
                    'Visita técnica: medição do espaço, levantamento de equipamentos e necessidades de armazenamento',
                    'Projeto 3D com definição de altura de mesa, profundidade e posicionamento de tomadas',
                    'Aprovação do projeto antes da fabricação',
                    'Corte CNC e usinagem com furação para organização de cabos',
                    'Acabamento de bordas e montagem com verificação de estabilidade',
                    'Instalação no local com nivelamento da mesa e fixação na parede (quando necessário)',
                ],
                'beneficios' => [
                    'Mesa na altura e profundidade ideais para o seu trabalho',
                    'Prateleiras e nichos para livros, objetos e equipamentos',
                    'Passagem de cabos oculta — sem fios aparentes na mesa',
                    'Aproveitamento de cantos e espaços reduzidos',
                    'Design que separa visualmente o trabalho do descanso',
                ],
                'aplicacoes' => [
                    ['icone' => 'fa-laptop', 'titulo' => 'Home office residencial', 'descricao' => 'Mesas e estantes para apartamentos e casas.'],
                    ['icone' => 'fa-building', 'titulo' => 'Escritórios comerciais', 'descricao' => 'Bancadas, painéis e estantes para escritórios e coworkings.'],
                    ['icone' => 'fa-graduation-cap', 'titulo' => 'Estudos e leitura', 'descricao' => 'Bancadas com altura adequada para leitura e escrita prolongada.'],
                ],
                'faq' => [
                    ['p' => 'Fazem home office para apartamento pequeno?', 'r' => 'Sim. Projetamos soluções compactas: bancadas estreitas, nichos verticais e painéis multifuncionais que aproveitam cada metro quadrado sem apertar o espaço.'],
                    ['p' => 'Dá para esconder a fiação?', 'r' => 'Sim. O projeto inclui canaletas embutidas e furação posicionada para passar cabos de energia, dados e monitoramento. O visual fica limpo, sem fios aparentes.'],
                    ['p' => 'Qual a altura ideal da mesa?', 'r' => 'Padrão: 75 cm do piso até a superfície. Para usuários mais altos (>1,90 m), recomendamos 78–80 cm. Para pé-direito alto com cadeira alta, avaliamos durante a visita técnica.'],
                ],
            ],
            'moveis-comerciais' => [
                'slug' => 'moveis-comerciais',
                'titulo' => 'Móveis Comerciais e Corporativos — Execução Técnica',
                'h1' => 'Móveis <span>Comerciais</span>',
                'eyebrow' => 'Comércio',
                'subtitle' => 'Balcões, expositores, recepções e bancadas para clínicas, barbearias e lojas. Estrutura reforçada em MDF, acabamento em MDF ou laca.',
                'descricao' => 'Móveis comerciais exigem resistência ao uso intenso e alinhamento com a identidade visual da marca. Projetamos balcões de atendimento, expositores, recepções e bancadas com estrutura em MDF reforçado e acabamento em MDF com laca ou melamina de alto impacto. O projeto prevê fluxo de clientes, posicionamento de equipamentos e integração com a comunicação visual.',
                'imagem' => BASE_URL . '/assets/img/modelocorporativo/IMG-20250507-WA0074.jpg',
                'eyebrow' => 'Comércio',
                'wa_message' => 'm%C3%B3veis%20comerciais',
                'prazo' => '30 a 50 dias úteis',
                'materiais' => [
                    ['nome' => 'MDF 18/25 mm', 'detalhe' => 'estrutura de balcões, expositores e painéis de fundo'],
                    ['nome' => 'MDF 18 mm', 'detalhe' => 'frentes decorativas, painéis de comunicação visual e detalhes de marca'],
                    ['nome' => 'Melamina alto impacto', 'detalhe' => 'acabamento resistente a uso comercial intenso'],
                    ['nome' => 'Laca / UV', 'detalhe' => 'acabamento premium para frentes de atendimento e recepção'],
                ],
                'processo' => [
                    'Visita técnica: medição do espaço, mapeamento de fluxo de clientes e posicionamento de equipamentos',
                    'Projeto 3D com comunicação visual e layout funcional',
                    'Aprovação do cliente — alinhamento de marca, cores e funcionalidade',
                    'Corte CNC e usinagem com reforços estruturais onde necessário',
                    'Acabamento com inspeção de resistência ao uso',
                    'Entrega e instalação com cronograma compatível com a operação do negócio',
                ],
                'beneficios' => [
                    'Estrutura reforçada para uso comercial intenso',
                    'Projeto alinhado com a identidade visual da marca',
                    'Acabamento em melamina alto impacto ou laca, conforme a exigência do ambiente',
                    'Cronograma de entrega planejado para não interferir na operação',
                    'Equipe de instalação treinada para montagem rápida e limpa',
                ],
                'aplicacoes' => [
                    ['icone' => 'fa-store', 'titulo' => 'Lojas e varejo', 'descricao' => 'Balcões de atendimento, expositores e painéis de produto.'],
                    ['icone' => 'fa-user-md', 'titulo' => 'Clínicas e consultórios', 'descricao' => 'Recepções, painéis de espera e bancadas de atendimento.'],
                    ['icone' => 'fa-cut', 'titulo' => 'Barbearias e salões', 'descricao' => 'Bancadas de trabalho, expositores de produtos e painéis decorativos.'],
                ],
                'faq' => [
                    ['p' => 'Atendem clínicas e barbearias?', 'r' => 'Sim. Temos experiência em projetos comerciais diversificados: clínicas médicas, odontológicas, barbearias, salões de beleza, lojas de varejo e escritórios corporativos.'],
                    ['p' => 'Qual o prazo para comércio?', 'r' => 'Depende da complexidade. Trabalhamos com cronograma planejado para não interferir na operação do negócio. Projetos de urgência podem ser negociados com prazo reduzido.'],
                    ['p' => 'Os materiais resistem ao uso intenso?', 'r' => 'Sim. Usamos MDF 25 mm na estrutura e acabamento em melamina de alto impacto ou laca UV, que resistem a riscos, manchas e uso contínuo. Especificamos o material conforme o fluxo de pessoas previsto.'],
                ],
            ],
        ];
    }

    public function index() {
        $servicos = array_values($this->services());
        return $this->render('servicos', [
            'title' => 'Serviços de Móveis Planejados em Campo Grande | LC Soluções em Móveis',
            'description' => 'Cozinhas planejadas, closets, painéis, home office e móveis comerciais sob medida em Campo Grande, MS. Processo técnico: medição, projeto 3D, fabricação e instalação.',
            'og_image' => $servicos[0]['imagem'],
            'servicos' => $servicos
        ]);
    }

    public function show($slug) {
        $all = $this->services();
        if (!isset($all[$slug])) {
            http_response_code(404);
            exit('Página não encontrada');
        }
        $s = $all[$slug];
        return $this->render('servico', [
            'title' => $s['titulo'] . ' em Campo Grande | LC Soluções em Móveis',
            'description' => $s['subtitle'],
            'canonical_url' => BASE_URL . '/servicos/' . $s['slug'],
            'og_image' => $s['imagem'],
            'servico' => $s,
            'servicos' => array_values($all)
        ]);
    }
}
