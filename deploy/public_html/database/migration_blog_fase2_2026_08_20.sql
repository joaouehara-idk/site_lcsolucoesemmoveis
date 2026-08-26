-- =============================================
-- MIGRAÇÃO: Blog Fase 2 - Artigos de Ambientes
-- Data: 2026-08-20
-- Execute APÓS a migration_blog_2026_08_20.sql
-- =============================================

-- POST 13: Quanto Custa uma Cozinha Planejada em Campo Grande?
SET @cat_precos = (SELECT `id` FROM `categorias` WHERE `slug` = 'precos-e-orcamentos' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'Quanto Custa uma Cozinha Planejada em Campo Grande?',
'quanto-custa-cozinha-planejada-campo-grande',
'<h2>A cozinha planejada é o ambiente mais desejado — e também o que mais gera dúvidas sobre o investimento. Veja as faixas de preço e o que realmente influencia o custo.</h2>

<p>Planejar uma cozinha em Campo Grande envolve pensar no layout, nos materiais, nas ferragens, no acabamento e na instalação. Cada escolha impacta no valor final do projeto.</p>

<p>Mas quanto custa, de fato, uma cozinha planejada na capital sul-mato-grossense? Veja as faixas de valores e o que você precisa considerar.</p>

<h2>Faixas de preço por tamanho</h2>

<h3>Cozinha compacta (até 6m²)</h3>
<p>Ideal para apartamentos pequenos e flats. Geralmente inclui armários superiores e inferiores, gaveteiro, espaço para fogão e geladeira.</p>
<p><strong>Faixa de investimento: R$ 6.000 a R$ 12.000</strong></p>

<h3>Cozinha média (6m² a 12m²)</h3>
<p>A mais comum em apartamentos e casas de Campo Grande. Permite incluir ilha ou balcão, mais gavetas e organizadores internos.</p>
<p><strong>Faixa de investimento: R$ 10.000 a R$ 20.000</strong></p>

<h3>Cozinha grande (acima de 12m²)</h3>
<p>Permite layout completo com ilha, armários até o teto, puxadores especiais, iluminação embutida e integração com sala de estar.</p>
<p><strong>Faixa de investimento: R$ 18.000 a R$ 35.000+</strong></p>

<h2>O que influi no preço?</h2>

<h3>Material</h3>
<p>MDF 18mm é o padrão de qualidade. MDF 15mm é mais econômico mas menos resistente. MDP é a opção mais barata, porém com menor durabilidade — especialmente em cozinhas, onde a umidade é um fator constante.</p>

<h3>Acabamento</h3>
<p>Laminação (fapes) é o mais acessível. Pintura PU, AC3 e folheados especiais encarecem o projeto, mas oferecem mais liberdade de design.</p>

<h3>Ferragens</h3>
<p>Corrediças com soft-close, dobradiças Hettich ou Blum, e organizadores internos (badejes, cestos, divisórias) aumentam o custo, mas melhoram muito a funcionalidade.</p>

<h3>Balcão</h3>
<p>Balancos em granito, quartzo ou acrílico têm preços diferentes. Granito é mais acessível; quartzo e acrílico são mais sofisticados e caros.</p>

<h3>Iluminação</h3>
<p>Luzes embutidas sob os armários superiores, iluminação LED de cor e spots de destaque são opcionais que fazem grande diferença no visual final.</p>

<h2>O que está incluso no projeto?</h2>

<p>Uma cozinha planejada completa geralmente inclui:</p>

<ul>
<li>Medição do ambiente</li>
<li>Projeto 3D com aprovação</li>
<li>Fabricação dos módulos</li>
<li>Ferragens e acessórios</li>
<li>Instalação</li>
<li>Garantia</li>
</ul>

<p>Pergunte sempre o que está incluso. Algumas marcenarias cobram projeto, instalação e ferragens separadamente.</p>

<h2>Cozinha planejada vs. cozinha pronta</h2>

<p>Uma cozinha pronta de grandes lojas pode parecer mais barata à primeira vista. Mas as medidas padrão raramente se encaixam perfeitamente no seu espaço, sobrando gaps e desperdiçando centímetros. Com o tempo, as ferragens de qualidade inferior começam a dar problema.</p>

<p>A cozinha planejada, apesar do investimento inicial maior, oferece aproveitamento total do espaço, durabilidade muito superior e um visual exclusivo.</p>

<h2>Perguntas frequentes</h2>

<h3>Quanto tempo leva para montar uma cozinha planejada?</h3>
<p>Em média, de 30 a 45 dias úteis após a aprovação do projeto. Projetos maiores ou com materiais importados podem levar mais.</p>

<h3>Posso parcelar?</h3>
<p>Sim, a maioria das marcenarias oferece parcelamento. Na LC Soluções, trabalhamos com condições que facilitam o investimento.</p>

<h3>A instalação está inclusa?</h3>
<p>Sim, na LC Soluções a instalação faz parte do projeto completo. Não há custo adicional para montagem.</p>

<div class="blog-cta-box">
<h3>Planejando sua cozinha em Campo Grande?</h3>
<p>A LC Soluções em Móveis pode projetar e montar sua cozinha planejada com materiais de qualidade e acabamento profissional. Solicite um orçamento sem compromisso.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20or%C3%A7amento%20de%20cozinha%20planejada">Solicitar Orçamento de Cozinha</a></p>
</div>',
'Quanto custa uma cozinha planejada em Campo Grande? Veja faixas de preço por tamanho, o que influi no orçamento e como escolher a melhor opção.',
'/assets/img/cozinha/IMG-20250507-WA0041.jpg',
'Preços e Orçamentos',
@cat_precos,
'Quanto Custa uma Cozinha Planejada em Campo Grande? | Blog LC Soluções',
'Descubra quanto custa uma cozinha planejada em Campo Grande. Faixas de preço por tamanho, materiais, ferragens e o que está incluso no projeto.',
'publicado',
NOW()
);

-- POST 14: Cozinha Planejada: O Que Você Precisa Definir Antes do Projeto
SET @cat_ambientes = (SELECT `id` FROM `categorias` WHERE `slug` = 'ambientes' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'Cozinha Planejada: O Que Você Precisa Definir Antes do Projeto',
'cozinha-planejada-o-que-definir-antes-do-projeto',
'<h2>Antes de pedir uma proposta para sua cozinha planejada, existem decisões importantes que você precisa tomar. Saber o que quer facilita o projeto e evita surpresas.</h2>

<p>Uma cozinha planejada não começa com o corte do MDF. Ela começa com decisões que você precisa tomar antes de entrar em contato com qualquer marcenaria. Essas escolhas determinam o resultado final, o prazo e o investimento.</p>

<p>Veja o que definir antes de iniciar o projeto da sua cozinha.</p>

<h2>1. Layout da cozinha</h2>

<p>O layout é a disposição dos armários e equipamentos no ambiente. Os layouts mais comuns são:</p>

<ul>
<li><strong>Linha reta</strong> — todos os armários em uma parede. Ideal para cozinhas pequenas.</li>
<li><strong>L</strong> — armários em duas paredes adjacentes. Oferece mais espaço e gavetas.</li>
<li><strong>U</strong> — armários em três paredes. Máximo de armazenamento.</li>
<li><strong>Com ilha</strong> — inclui uma ilha central. Precisa de espaço mínimo de 90cm de passagem ao redor.</li>
</ul>

<p>Pense no fluxo de trabalho: geladeira → pia → fogão. Quanto mais lógico esse fluxo, mais prática será a cozinha.</p>

<h2>2. Equipamentos que serão embutidos</h2>

<p>Defina antecipadamente quais eletrodomésticos serão embutidos: fogão, cooktop, forno, micro-ondas, máquina de louça, extractor. Cada um requer um módulo específico com dimensões exatas.</p>

<p>Se você ainda não comprou os eletrodomésticos, defina as marcas e modelos antes do projeto. As dimensões dos aparelhos determinam o tamanho dos módulos.</p>

<h2>3. Tipo de bancada</h2>

<p>A bancada é a superfície de trabalho. As opções mais comuns são:</p>

<ul>
<li><strong>Granito</strong> — resistente, durável, preço acessível</li>
<li><strong>Quartzo</strong> — mais sofisticado, resistente a manchas, mais caro</li>
<li><strong>Acrílico</strong> — visual contípuro, sem emendas visíveis</li>
<li><strong>Silestone</strong> —/engineered stone, alta resistência</li>
</ul>

<p>Considere a resistência a manchas, à umidade e ao calor. Em cozinhas, a bancada recebe bastante uso.</p>

<h2>4. Acabamento dos armários</h2>

<p>O acabamento define o visual da cozinha. As opções incluem:</p>

<ul>
<li><strong>Laminação (fapes)</strong> — económica, variedade de cores e texturas</li>
<li><strong>Pintura PU</strong> — acabamento premium, cores personalizadas</li>
<li><strong>Folheado</strong> — simula madeira natural</li>
<li><strong>AC3</strong> — alta resistência a arranhões e manchas</li>
</ul>

<h2>5. Organização interna</h2>

<p>Pense em como você organiza sua cozinha hoje. O que funciona? O que falta? Defina:</p>

<ul>
<li>Tipos de gavetas (fundo, talheres, temperos)</li>
<li>Organizadores para panelas e tampas</li>
<li>Estantes para alimentos secos</li>
<li>Cestos para lixo reciclável</li>
<li>Espaço para produtos de limpeza</li>
</ul>

<h2>6. Iluminação</h2>

<p>Além da iluminação geral, considere:</p>

<ul>
<li>Luzes sob os armários superiores (trabalho na bancada)</li>
<li>Luzes dentro dos armários (facilita encontrar itens)</li>
<li>Luzes de destaque em nichos</li>
</ul>

<h2>7. Orçamento disponível</h2>

<p>Defina uma faixa de investimento antes de iniciar o projeto. Isso ajuda a marcenaria a oferecer opções adequadas ao seu bolso, sem surpresas.</p>

<p>Na LC Soluções em Móveis, trabalhamos com faixas de investimento variadas e sempre explicamos o que cada escolha impacta no valor final.</p>

<h2>Próximo passo</h2>

<p>Depois de definir esses pontos, entre em contato com uma marcenaria de confiança. Ela vai fazer a medição técnica, criar o projeto 3D e apresentar o orçamento detalhado.</p>

<div class="blog-cta-box">
<h3>Quer ajuda para planejar sua cozinha?</h3>
<p>A LC Soluções em Móveis oferece projeto personalizado com medição técnica, visualização 3D e orçamento transparente. Fale conosco.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20ajuda%20para%20planejar%20minha%20cozinha">Pedir Ajuda para Planejar</a></p>
</div>',
'Antes de pedir uma proposta de cozinha planejada, entenda o que precisa definir: layout, materiais, bancada, organização e orçamento.',
'/assets/img/cozinha/IMG-20250507-WA0041.jpg',
'Ambientes',
@cat_ambientes,
'Cozinha Planejada: O Que Definir Antes do Projeto | Blog LC Soluções',
'Guia completo sobre o que definir antes de projetar uma cozinha planejada: layout, materiais, bancada, organização, iluminação e orçamento.',
'publicado',
NOW()
);

-- POST 15: Cozinha Planejada Pequena: Como Aproveitar Cada Centímetro
SET @cat_ambientes2 = (SELECT `id` FROM `categorias` WHERE `slug` = 'ambientes' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'Cozinha Planejada Pequena: Como Aproveitar Cada Centímetro',
'cozinha-planejada-pequena-aproveitar-espaco',
'<h2>Cozinha pequena não significa cozinha ruim. Com planejamento correto, é possível ter um ambiente funcional, bonito e com muito espaço de armazenamento.</h2>

<p>Em Campo Grande, onde apartamentos compactos são cada vez mais comuns, a cozinha pequena é uma realidade para muitas famílias. Mas com o planejamento certo, ela pode ser tão funcional quanto uma cozinha grande.</p>

<p>Veja estratégias comprovadas para aproveitar cada centímetro da sua cozinha.</p>

<h2>1. Armários até o teto</h2>

<p>Um dos erros mais comuns é deixar um espaço vazio entre os armários superiores e o teto. Esse espaço acumula poeira e desperdiça área de armazenamento.</p>

<p>Armários que vão até o teto:</p>
<ul>
<li>Eliminam acúmulo de poeira</li>
<li>Dobram a capacidade de armazenamento</li>
<li> Criam um visual mais limpo e integrado</li>
</ul>

<h2>2. Portas de correr</h2>

<p>Em cozinhas onde não há espaço para portas que abrem para fora, as portas de correr são a solução. Elas economizam espaço de circulação e funcionam bem em ambientes compactos.</p>

<h2>3. Gavetas com organizadores</h2>

<p>Gavetas com divisórias internas para talheres, temperos e utensílios mantêm tudo organizado e fácil de encontrar. Evita a bagunça que acontece em armários com prateleiras fixas.</p>

<h2>4. Balcão multifuncional</h2>

<p>Se houver espaço, um balcão pequeno pode servir como area de trabalho, local para refeições rápidas e até como divisória visual entre a cozinha e a sala.</p>

<h2>5. Cores claras</h2>

<p>Cores claras (branco, bege, cinza claro) fazem o ambiente parecer maior. Evite cores escuras em cozinhas pequenas — elas tendem a "fechar" o espaço.</p>

<h2>6. Iluminação embutida</h2>

<p>Luzes sob os armários superiores iluminam a bancada sem ocupar espaço. É uma solução discreta que faz muita diferença no dia a dia.</p>

<h2>7. Prateleiras abertas</h2>

<p>Em vez de armários fechados em alguns pontos, prateleiras abertas criam sensação de amplitude e facilitam o acesso a itens usados diariamente.</p>

<h2>8. Aproveitamento de cantos</h2>

<p>Cantos são frequentemente desperdiçados. Soluções giratórias (carrosséis) ou prateleiras em L aproveitam esse espaço.</p>

<h2>9. Espelhos</h2>

<p>Um espelho na parede oposta à janela reflete a luz natural e faz o ambiente parecer maior. Pode ser uma parede toda espelhada ou apenas uma faixa.</p>

<h2>10. Eletrodomésticos compactos</h2>

<p>Fogão de 4 bocas em vez de 6. Geladeira side by side pode não caber — uma geladeira duplex de 300L resolve. Máquina de louça compacta para cozinhas minúsculas.</p>

<h2>Exemplo prático</h2>

<p>Uma cozinha de 4m² pode ter: armários superiores até o teto, 6 módulos inferiores com gavetas organizadoras, espaço para fogão de 4 bocas, geladeira duplex, micro-ondas embutido e prateleiras abertas para itens decorativos.</p>

<p>Tudo isso com 90cm de passagem livre entre os armários.</p>

<div class="blog-cta-box">
<h3>Sua cozinha é pequena e precisa de solução?</h3>
<p>A LC Soluções em Móveis tem experiência em cozinhas compactas em Campo Grande. Podemos projetar uma cozinha que aproveita cada centímetro.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Minha%20cozinha%20é%20pequena%20e%20quero%20um%20projeto">Solicitar Projeto para Cozinha Pequena</a></p>
</div>',
'Cozinha planejada pequena: 10 ideias para aproveitar cada centímetro. Armários até o teto, portas de correr, gavetas organizadoras e mais.',
'/assets/img/cozinha/IMG-20250507-WA0041.jpg',
'Ambientes',
@cat_ambientes2,
'Cozinha Planejada Pequena: 10 Ideias para Aproveitar o Espaço | Blog LC',
'Cozinha planejada pequena: como aproveitar cada centímetro com armários até o teto, portas de correr, gavetas organizadoras e mais.',
'publicado',
NOW()
);

-- POST 16: Home Office Planejado: Como Criar um Espaço para Trabalhar
SET @cat_ambientes3 = (SELECT `id` FROM `categorias` WHERE `slug` = 'ambientes' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'Home Office Planejado: Como Criar um Espaço Confortável para Trabalhar',
'home-office-planejado-espaco-confortavel',
'<h2>Trabalhar em casa exige um ambiente planejado. Home office com móveis sob medida é a solução para quem precisa de organização, conforto e produtividade.</h2>

<p>Desde 2020, o home office deixou de ser tendência para virar necessidade. Mas trabalhar na mesa da cozinha ou no sofá da sala não é sustentável a longo prazo. Um espaço dedicado, com mobiliário planejado, faz diferença na produtividade e na saúde.</p>

<p>Veja como planejar um home office funcional e bonito.</p>

<h2>1. Mesa sob medida</h2>

<p>A mesa é o centro do home office. Com planejamento, ela pode ter:</p>

<ul>
<li>Espaço exato para monitor, teclado e mouse</li>
<li>Gavetas para documentos e material de escritório</li>
<li>Canaletas para organização de fios</li>
<li>Espaço para impressora</li>
</ul>

<p>Uma mesa planejada se adapta ao tamanho do ambiente e às suas necessidades específicas.</p>

<h2>2. Estantes e prateleiras</h2>

<p>Estantes que vão até o teto aproveitam o espaço vertical e mantêm livros, pastas e organizadores acessíveis. Prateleiras flutuantes criam visual limpo.</p>

<h2>3. Armários com portas</h2>

<p>Para esconder o que não precisa estar exposto: documentos, material de escritório, eletrônicos extras. Armários com portas mantêm o visual limpo e organizado.</p>

<h2>4. Organização de fios</h2>

<p>Fios soltos são inimigos da produtividade e da estética. Soluções incluem:</p>

<ul>
<li>Canaletas embutidas na parede ou na mesa</li>
<li>Espaço específico para fontes e cabos</li>
<li>Organizadores de fios duplos</li>
</ul>

<h2>5. Iluminação</h2>

<p>A iluminação do home office precisa ser adequada para trabalho prolongado. Considere:</p>

<ul>
<li>Luz natural (posicione a mesa perto da janela)</li>
<li>Luz de trabalho (lâmpada LED de luz branca)</li>
<li>Luz ambiente (iluminação indireta para reduzir contraste)</li>
</ul>

<h2>6. Ergonomia</h2>

<p>Altura da mesa, distância do monitor, posição da cadeira — tudo influencia na postura. Com móveis planejados, você define as medidas ideais para o seu corpo.</p>

<h2>7. Integração com outros ambientes</h2>

<p>Se o home office é um canto da sala ou do quarto, o mobiliário pode ser projetado para se integrar visualmente com o ambiente, mantendo harmonia no design.</p>

<h2>8. Espaço para reuniões</h2>

<p>Se você recebe clientes ou colegas, considere incluir uma pequena área com cadeiras extras e uma estante para materiais de apresentação.</p>

<h2>Quanto custa um home office planejado?</h2>

<p>Em Campo Grande, um home office completo (mesa + estantes + armários) pode variar de <strong>R$ 3.000 a R$ 10.000</strong>, dependendo do tamanho, material e complexidade.</p>

<div class="blog-cta-box">
<h3>Precisa de um home office planejado?</h3>
<p>A LC Soluções em Móveis pode projetar e montar seu home office com mobiliário sob medida, organização inteligente e acabamento profissional.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20home%20office%20planejado">Solicitar Projeto de Home Office</a></p>
</div>',
'Home office planejado: como criar um espaço confortável e funcional para trabalhar. Mesa sob medida, estantes, organização de fios e ergonomia.',
'/assets/img/escritorio/IMG-20250507-WA0063.jpg',
'Ambientes',
@cat_ambientes3,
'Home Office Planejado: Como Criar um Espaço Confortável | Blog LC Soluções',
'Guia completo para planejar um home office com móveis sob medida. Mesa, estantes, organização, iluminação e ergonomia.',
'publicado',
NOW()
);

-- POST 17: Closet Planejado: Como Aproveitar Melhor o Espaço
SET @cat_ambientes4 = (SELECT `id` FROM `categorias` WHERE `slug` = 'ambientes' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'Closet Planejado: Como Aproveitar Melhor o Espaço do Seu Quarto',
'closet-planejado-aproveitar-espaco',
'<h2>Um closet planejado transforma o quarto. Com divisórias sob medida, organização inteligente e acabamento profissional, você encontra tudo com facilidade.</h2>

<p>O closet é um dos ambientes que mais se beneficiam do planejamento. Cada pessoa tem uma quantidade diferente de roupas, calçados e acessórios. Um closet pronto dificilmente atende todas as necessidades.</p>

<p>Com um closet planejado, cada compartimento é pensado para o que você realmente guarda.</p>

<h2>1. Divisórias internas sob medida</h2>

<p>Divisórias para camisas, calças, vestidos, saias, acessórios. Cada tipo de roupa tem um espaço ideal. Divisórias fixas ou ajustáveis mantêm tudo organizado.</p>

<h2>2. Gavetas para acessórios</h2>

<p>Gavetas com divisórias para cintos, relógios, joias, meias e calcinhas. Tudo visível e acessível.</p>

<h2>3. Espaço para calçados</h2>

<p>Prateleiras inclinadas para sapatos permitem ver todos os pares de uma vez. Capacidade para 10, 20 ou mais pares, dependendo do tamanho do closet.</p>

<h2>4. Cabideiros em alturas diferentes</h2>

<p>Cabideiro mais alto para vestidos e casacos. Cabideiro médio para camisas e blusas. Cabideiro baixo para calças. Tudo planejado para aproveitar a altura.</p>

<h2>5. Iluminação interna</h2>

<p>Luzes LED dentro do closet facilitam encontrar itens, especialmente em ambientes com pouca luz natural.</p>

<h2>6. Portas</h2>

<p>Portas de correr são ideais para closets em passagens ou quartos pequenos. Portas basculantes são uma alternativa moderna. Portas convencionais funcionam bem em espaços maiores.</p>

<h2>7. Espelho</h2>

<p>Um espelho na porta do closet ou em uma parede interna elimina a necessidade de espelho separado no quarto.</p>

<h2>Tipos de closet</h2>

<h3>Closet walk-in</h3>
<p>Espaço dedicado (geralmente a partir de 3m²). Permite acesso pelos dois lados. Ideal para casais.</p>

<h3>Closet embutido</h3>
<p>Ocupa uma parede do quarto. Usa a altura total do ambiente. Solução prática para quartos menores.</p>

<h3>Closet compacto</h3>
<p>Em espaços apertados (1,5m a 2m de parede). Compartimentos inteligentes que aproveitam cada centímetro.</p>

<h2>Quanto custa um closet planejado?</h2>

<p>Em Campo Grande, um closet planejado varia de <strong>R$ 4.000 a R$ 15.000</strong>, dependendo do tamanho, material e acabamento.</p>

<div class="blog-cta-box">
<h3>Quer um closet planejado para seu quarto?</h3>
<p>A LC Soluções em Móveis pode projetar um closet sob medida que se adapta perfeitamente ao seu espaço e às suas necessidades.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20closet%20planejado">Solicitar Projeto de Closet</a></p>
</div>',
'Closet planejado: como aproveitar melhor o espaço do seu quarto. Divisórias sob medida, gavetas para acessórios, espaço para calçados e iluminação.',
'/assets/img/quarto/quarto1.jpeg',
'Ambientes',
@cat_ambientes4,
'Closet Planejado: Como Aproveitar o Espaço | Blog LC Soluções',
'Guia completo para planejar um closet sob medida. Divisórias, gavetas, organização, iluminação e dicas para aproveitar cada centímetro.',
'publicado',
NOW()
);

-- =============================================
-- FIM DA MIGRAÇÃO FASE 2
-- =============================================
