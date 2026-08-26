-- =============================================
-- MIGRAÇÃO: Blog - Categorias + Novos Artigos
-- Data: 2026-08-20
-- Execute no phpMyAdmin ou MySQL CLI
-- =============================================

-- 1. CORRIGIR categorias existentes e criar categorias novas
-- =============================================

-- Corrigir charset da categoria "Dormitórios"
UPDATE `categorias` SET `nome` = 'Dormitórios', `slug` = 'dormitorios' WHERE `id` = 3;

-- Inserir categorias novas (IGNORE evita duplicatas)
INSERT IGNORE INTO `categorias` (`nome`, `slug`) VALUES
('Móveis Planejados', 'moveis-planejados'),
('Materiais', 'materiais'),
('Preços e Orçamentos', 'precos-e-orcamentos'),
('Guia do Cliente', 'guia-do-cliente'),
('Campo Grande MS', 'campo-grande-ms'),
('Ambientes', 'ambientes'),
('Problemas e Soluções', 'problemas-e-solucoes'),
('Tendências', 'tendencias'),
('Projetos', 'projetos'),
('Investimentos', 'investimentos');

-- 2. ATUALIZAR posts existentes para usar categorias do banco
-- =============================================

UPDATE `posts` p
  JOIN `categorias` c ON c.slug = 'materiais'
  SET p.`categoria_id` = c.`id`
  WHERE p.`id` = 3;

UPDATE `posts` p
  JOIN `categorias` c ON c.slug = 'tendencias'
  SET p.`categoria_id` = c.`id`
  WHERE p.`id` = 4;

UPDATE `posts` p
  JOIN `categorias` c ON c.slug = 'projetos'
  SET p.`categoria_id` = c.`id`
  WHERE p.`id` = 5;

UPDATE `posts` p
  JOIN `categorias` c ON c.slug = 'projetos'
  SET p.`categoria_id` = c.`id`
  WHERE p.`id` = 6;

UPDATE `posts` p
  JOIN `categorias` c ON c.slug = 'moveis-planejados'
  SET p.`categoria_id` = c.`id`
  WHERE p.`id` = 7;

-- Encurtar slugs dos posts existentes
UPDATE `posts` SET `slug` = 'mdf-limpeza-e-manutencao-guia-definitivo' WHERE `id` = 3;
UPDATE `posts` SET `slug` = '10-tendencias-moveis-planejados-2026' WHERE `id` = 4;
UPDATE `posts` SET `slug` = '10-projetos-apartamento-sob-medida' WHERE `id` = 5;
UPDATE `posts` SET `slug` = '10-tipos-projetos-moveis-planejados' WHERE `id` = 6;
UPDATE `posts` SET `slug` = 'mdf-o-melhor-investimento-moveis' WHERE `id` = 7;

-- 3. INSERIR NOVOS ARTIGOS — FASE 1
-- =============================================
-- Os IDs das categorias são resolvidos via JOIN nas queries abaixo.
-- Cada INSERT usa uma variável @cat_id para referenciar a categoria correta.

-- POST 8: Quanto Custa um Móvel Planejado em Campo Grande em 2026?
SET @cat_precos = (SELECT `id` FROM `categorias` WHERE `slug` = 'precos-e-orcamentos' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'Quanto Custa um Móvel Planejado em Campo Grande em 2026?',
'quanto-custa-moveis-planejados-campo-grande',
'<h2>Quanto custa um móvel planejado em Campo Grande? É a pergunta que todo mundo faz antes de reformar.</h2>

<p>Se você está planejando reformar sua casa em Campo Grande e está considerando móveis planejados, a primeira dúvida é quase sempre sobre o investimento. É normal — ninguém quer ser pego de surpresa.</p>

<p>A verdade é que não existe um valor fixo. O preço de um móvel planejado depende de vários fatores que mudam de projeto para projeto. Mas isso não significa que você fique no escuro. É possível ter uma noção bem clara do que esperar.</p>

<h2>O que influi no preço de um móvel planejado?</h2>

<p>Cada ambiente tem suas particularidades, e o orçamento reflete isso diretamente. Veja os principais fatores que influenciam o valor:</p>

<h3>1. Dimensões do ambiente</h3>
<p>Quanto maior o espaço, mais material e mais módulos serão necessários. Uma cozinha de 12m² terá um investimento diferente de uma cozinha de 6m² — mesmo que o estilo seja parecido.</p>

<h3>2. Material escolhido</h3>
<p>O MDF é o material mais utilizado em móveis planejados no Brasil. Ele oferece ótimo custo-benefício, resistência e variedade de acabamentos. O MDP é uma alternativa mais econômica, porém com menor durabilidade. Já a madeira maciça e compensados especiais encarecem o projeto.</p>

<h3>3. Espessura do MDF</h3>
<p>MDF de 15mm, 18mm e 25mm têm preços diferentes. A espessura ideal depende do tipo de móvel e da carga que ele vai suportar. Gavetas e prateleiras, por exemplo, muitas vezes usam 18mm, enquanto fundos de armários podem usar 15mm.</p>

<h3>4. Acabamentos</h3>
<p>Acabamentos em MDF vão desde a laminação básica (fapes) até pintura PU, AC3 e folheados especiais. Cada tipo de acabamento tem um custo diferente. Pintura Personalizada, por exemplo, é mais cara que laminação, mas oferece mais liberdade de cores.</p>

<h3>5. Ferragens e acessórios</h3>
<p>Dobradiças, corrediças, puxadores e organizadores internos variam muito de preço. Corrediças com amortecimento (soft-close) custam mais que as convencionais, mas oferecem muito mais conforto no dia a dia.</p>

<h3>6. Complexidade do design</h3>
<p>Projetos com curvas, encaixes especiais, iluminação embutida ou integração com outros elementos (como eletrodomésticos embutidos) tendem a ser mais trabalhosos e, consequentemente, mais caros.</p>

<h3>7. Quantidade de módulos</h3>
<p>Um projeto com 15 módulos de armários terá um custo maior que um com 8 módulos. Cada módulo envolve corte, cola, furação, montagem e instalação.</p>

<h2>Faixas de valores por ambiente em Campo Grande</h2>

<p>Baseado na experiência de marcenarias em Campo Grande, essas são as faixas de investimento mais comuns em 2026:</p>

<h3>Cozinha planejada</h3>
<p>Uma cozinha completa com armários, gaveteiro, balcão e acessórios internos varia de <strong>R$ 8.000 a R$ 25.000</strong>, dependendo do tamanho, material e acabamento escolhido.</p>

<h3>Quarto com closet</h3>
<p>Um quarto com closet planejado, incluindo compartimentos para roupas, calçados e acessórios, custa entre <strong>R$ 6.000 a R$ 18.000</strong>.</p>

<h3>Sala com painel de TV</h3>
<p>Painéis de TV com nichos, prateleiras e armários embutidos ficam na faixa de <strong>R$ 4.000 a R$ 12.000</strong>.</p>

<h3>Home office</h3>
<p>Uma estante + mesa sob medida com organização para computador e documentos: <strong>R$ 3.000 a R$ 10.000</strong>.</p>

<h3>Guarda-roupa</h3>
<p>Guarda-roupa planejado com divisórias internas, gavetas e organize: <strong>R$ 5.000 a R$ 15.000</strong>.</p>

<blockquote>Importante: esses valores são faixas de referência. O orçamento final depende das dimensões exatas do seu ambiente, do material escolhido e dos acessórios que farão parte do projeto. Para um orçamento personalizado, é necessário uma medição técnica.</blockquote>

<h2>Como é calculado o orçamento?</h2>

<p>A maioria das marcenarias calcula o orçamento com base em:</p>

<ul>
<li><strong>Metro linear</strong> — preço por metro de móvel (muito usado em cozinhas)</li>
<li><strong>Por módulo</strong> — preço por módulo individual (mais comum em projetos modulares)</li>
<li><strong>Por metro quadrado</strong> — usado em painéis e projetos de parede</li>
<li><strong>Projeto completo</strong> — valor fechado para o projeto inteiro, incluindo medição, projeto, fabricação e instalação</li>
</ul>

<p>Na LC Soluções em Móveis, o orçamento é feito de forma transparente, com itens detalhados para que você saiba exatamente quanto está investindo em cada componente.</p>

<h2>Quanto custa a instalação?</h2>

<p>A instalação geralmente está inclusa no orçamento do projeto, mas algumas marcenarias cobram à parte. É importante perguntar isso antes de fechar. Na LC, a instalação faz parte do serviço completo.</p>

<h2>Vale a pena investir em móveis planejados?</h2>

<p>Sim — e o principal motivo é o aproveitamento do espaço. Em Campo Grande, onde apartamentos compactos e casas com dimensões variadas são comuns, ter móveis que se adaptam perfeitamente ao ambiente faz uma diferença enorme no dia a dia.</p>

<p>Além disso, móveis planejados em MDF de qualidade duram muito mais que móveis prontos de MDP. No longo prazo, o investimento se paga.</p>

<h2>Perguntas frequentes</h2>

<h3>Qual a diferença de preço entre MDF e MDP?</h3>
<p>O MDF custa em média 20% a 30% mais que o MDP, mas oferece significativamente mais durabilidade, resistência à umidade e fixação de ferragens. No custo-benefício ao longo dos anos, o MDF costuma ser mais vantajoso.</p>

<h3>É possível parcelar o projeto?</h3>
<p>Sim, a maioria das marcenarias em Campo Grande oferece opções de parcelamento. Na LC Soluções, trabalhamos com condições de pagamento que facilitam o investimento.</p>

<h3>Quanto tempo leva para fabricar e instalar?</h3>
<p>Em média, de 30 a 45 dias úteis após a aprovação do projeto. Prazos maiores podem ocorrer para projetos mais complexos ou com materiais importados.</p>

<h3>Posso pedir orçamento sem compromisso?</h3>
<p>Sim. Na LC Soluções em Móveis, oferecemos orçamento gratuito e sem compromisso. Basta entrar em contato pelo WhatsApp ou pelo formulário do site.</p>

<div class="blog-cta-box">
<h3>Planejando seus móveis em Campo Grande?</h3>
<p>A LC Soluções em Móveis pode avaliar seu espaço e desenvolver um projeto sob medida, com materiais de qualidade e instalação profissional. Faça um orçamento sem compromisso.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20or%C3%A7amento%20de%20m%C3%B3veis%20planejados">Solicitar Orçamento pelo WhatsApp</a></p>
</div>',
'Quanto custa um móvel planejado em Campo Grande em 2026? Veja as faixas de preço por ambiente, o que influi no orçamento e como calcular o investimento.',
'/assets/img/blog/1778770059_IMG-20250507-WA0029.jpg',
'Preços e Orçamentos',
@cat_precos,
'Quanto Custa um Móvel Planejado em Campo Grande em 2026? | Blog LC Soluções',
'Descubra quanto custa um móvel planejado em Campo Grande. Veja faixas de preço por ambiente, o que influi no orçamento e como planejar seu investimento.',
'publicado',
NOW()
);

-- POST 9: MDF ou MDP: Qual é Melhor para Móveis Planejados?
SET @cat_materiais = (SELECT `id` FROM `categorias` WHERE `slug` = 'materiais' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'MDF ou MDP: Qual é Melhor para Móveis Planejados?',
'mdf-ou-mdp-qual-e-melhor-para-moveis',
'<h2>A escolha entre MDF e MDP é uma das decisões mais importantes ao planejar seus móveis. E ela impacta diretamente na durabilidade, no acabamento e no investimento.</h2>

<p>Quando você decide por móveis planejados, uma das primeiras dúvidas que aparece é: qual material usar? MDF ou MDP? Parece a mesma coisa, mas as diferenças são grandes — e escolher errado pode significar problemas no futuro.</p>

<p>Vamos explicar de forma clara as diferenças, vantagens e desvantagens de cada um, para que você tome a melhor decisão para o seu projeto.</p>

<h2>O que é MDF?</h2>

<p>MDF significa Medium Density Fiberboard — ou Painel de Fibras de Média Densidade. É feito a partir de fibras de madeira prensadas com resina, resultando em um material homogêneo, liso e uniforme.</p>

<p>As principais características do MDF são:</p>

<ul>
<li><strong>Densidade uniforme</strong> — sem nós, falhas ou variações na espessura</li>
<li><strong>Boa fixação de parafusos</strong> — as fibras compactas seguram bem as ferragens</li>
<li><strong>Acabamento superior</strong> — aceita laminação, pintura, folheado e qualquer tipo de revestimento</li>
<li><strong>Versatilidade</strong> — permite curvas, encaixes precisos e designs personalizados</li>
</ul>

<h2>O que é MDP?</h2>

<p>MDP significa Medium Density Particleboard — Painel de Partículas de Média Densidade. É feito com partículas de madeira prensadas, resultando em um material mais granulado e menos denso que o MDF.</p>

<p>As principais características do MDP são:</p>

<ul>
<li><strong>Custo mais baixo</strong> — é significativamente mais barato que o MDF</li>
<li><strong>Disponibilidade</strong> — encontrado em grandes lojas de materiais de construção</li>
<li><strong>Peso menor</strong> — mais leve que o MDF</li>
<li><strong>Densidade irregular</strong> — partículas de tamanhos diferentes, o que afeta a uniformidade</li>
</ul>

<h2>MDF vs MDP: comparação direta</h2>

<h3>Resistência à umidade</h3>
<p><strong>MDF:</strong> Mais resistente, especialmente em versões tratadas. A fibra compacta dificulta a penetração de água.</p>
<p><strong>MDP:</strong> Menos resistente. As partículas absorvem umidade mais facilmente, o que pode causar inchaço e deformação.</p>

<h3>Fixação de ferragens</h3>
<p><strong>MDF:</strong> Parafusos e dobradiças ficam firmes. As fibras compactas oferecem excelente ancoragem.</p>
<p><strong>MDP:</strong> Parafusos podem afrouxar com o tempo. A fixação é menos confiável, especialmente em peças que recebem carga.</p>

<h3>Acabamento</h3>
<p><strong>MDF:</strong> Superfície lisa e uniforme. Aceita qualquer tipo de revestimento, incluindo pintura com acabamento fosco, acetinado ou brilhante.</p>
<p><strong>MDP:</strong> Superfície mais áspera. O acabamento é limitado a laminados e fapes. Pintura direta não é recomendada.</p>

<h3>Durabilidade</h3>
<p><strong>MDF:</strong> Com manutenção adequada, um móvel de MDF pode durar 15 a 20 anos ou mais.</p>
<p><strong>MDP:</strong> A durabilidade média é de 5 a 8 anos, dependendo do uso e das condições ambientais.</p>

<h3>Custo-benefício</h3>
<p><strong>MDF:</strong> Custo inicial maior, mas economia no longo prazo por durar mais e exigir menos manutenção.</p>
<p><strong>MDP:</strong> Custo inicial menor, mas pode exigir substituição mais precoce.</p>

<h2>Quando usar MDP?</h2>

<p>O MDP pode ser uma opção viável em algumas situações:</p>

<ul>
<li>Projetos com orçamento muito apertado</li>
<li>Móveis provisórios (aluguel, mudança frequente)</li>
<li>Ambientes secos e com uso leve</li>
<li>Peças que não recebem carga pesada</li>
</ul>

<p>Mas para projetos que precisam de durabilidade, acabamento refinado e resistência ao tempo, o MDF é a escolha mais segura.</p>

<h2>Por que a maioria das marcenarias usa MDF?</h2>

<p>Em Campo Grande, onde a umidade relativa do ar pode atingir 80% ou mais nos meses de chuva, a resistência à umidade é um fator decisivo. O MDF trata desse problema de forma mais eficiente que o MDP.</p>

<p>Além disso, móveis planejados são, por definição, projetados para durar. Usar um material com vida útil limitada iria contra a própria proposta do serviço.</p>

<blockquote>Na LC Soluções em Móveis, trabalhamos exclusivamente com MDF de alta qualidade. Cada peça é selecionada para garantir que o resultado final tenha o acabamento e a durabilidade que nosso cliente espera.</blockquote>

<h2>Perguntas frequentes</h2>

<h3>MDF é mais caro que MDP?</h3>
<p>Sim, o MDF custa em média 20% a 30% mais. Porém, a durabilidade superior e o menor custo de manutenção fazem com que o investimento se pague ao longo do tempo.</p>

<h3>MDF pode ser usado em cozinhas e banheiros?</h3>
<p>Sim, desde que receba tratamento adequado. O MDF tratado (MR e MRO) oferece resistência à umidade superior. É importante garantir que o acabamento seja resistente à água e que haja ventilação adequada no ambiente.</p>

<h3>Como identificar se um móvel é de MDF ou MDP?</h3>
<p>O MDF tem superfície lisa e uniforme. O MDP mostra partículas visíveis na borda cortada. Outro teste: ao furar, o MDF apresenta fibras finas, enquanto o MDP mostra pedaços de madeira de tamanhos diferentes.</p>

<div class="blog-cta-box">
<h3>Precisa de orientação sobre materiais?</h3>
<p>A LC Soluções em Móveis pode ajudar você a escolher o melhor material para cada ambiente do seu projeto. Fale conosco.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20saber%20mais%20sobre%20MDF%20para%20meus%20m%C3%B3veis">Fale com um Especialista</a></p>
</div>',
'MDF ou MDP para móveis planejados? Compare durabilidade, resistência à umidade, acabamento e custo-benefício. Entenda qual material é melhor para seu projeto.',
'/assets/img/blog/1778770603_logo.jpg',
'Materiais',
@cat_materiais,
'MDF ou MDP: Qual é Melhor para Móveis Planejados? | Blog LC Soluções',
'Compare MDF e MDP: diferenças, vantagens e desvantagens. Guia completo para escolher o melhor material para seus móveis planejados em Campo Grande.',
'publicado',
NOW()
);

-- POST 10: Checklist: 15 Coisas para Conferir Antes de Contratar uma Marcenaria
SET @cat_guia = (SELECT `id` FROM `categorias` WHERE `slug` = 'guia-do-cliente' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'Checklist: 15 Coisas para Conferir Antes de Contratar uma Marcenaria',
'checklist-antes-contratar-marcenaria',
'<h2>Contratar uma marcenaria é uma decisão que impacta diretamente no resultado final do seu projeto. Este checklist reúne os pontos mais importantes que você precisa verificar antes de fechar.</h2>

<p>Se você está pensando em reformar sua casa com móveis planejados, sabe que escolher a marcenaria certa é fundamental. Muitas vezes, a diferença entre um projeto bem-feito e um problema está nos detalhes que parecem pequenos, mas fazem toda a diferença.</p>

<p>Use esta lista como guia antes de assinar qualquer contrato.</p>

<h2>Antes de contratar</h2>

<h3>1. Verifique o portfólio de projetos anteriores</h3>
<p>Uma marcenaria séria tem fotos reais dos seus projetos — não apenas imagens de catálogo. Peça para ver trabalhos similares ao que você quer fazer. Se possível, peça referências de clientes anteriores.</p>

<h3>2. Confirme que a empresa tem endereço fixo</h3>
<p>Uma marcenaria com endereço físico oferece mais segurança. Se a empresa não tem CNPJ ou funciona apenas por WhatsApp, o risco é maior.</p>

<h3>3. Pergunte sobre o material que será utilizado</h3>
<p>Saiba exatamente qual MDF será usado, de qual marca, qual a espessura e qual o acabamento. Isso evita surpresas com materiais de qualidade inferior.</p>

<h3>4. Verifique se o orçamento é detalhado</h3>
<p>Um orçamento vago é sinal de alerta. O ideal é que cada item esteja separado: material, ferragens, acabamento, instalação, projeto. Transparência é sinal de profissionalismo.</p>

<h3>5. Pergunte sobre garantia</h3>
<p>Pergunte qual é a garantia do produto e da instalação. Uma marcenaria confiante no seu trabalho oferece garantia por escrito.</p>

<h3>6. Confirme o prazo de entrega e instalação</h3>
<p>Saiba quantos dias levarão para fabricar e instalar. Prazos vagos ("em breve", "quando der") são um sinal de alerta. O ideal é um prazo realista e por escrito.</p>

<h3>7. Verifique como é feita a medição</h3>
<p>A medição deve ser feita por um profissional, com fita métrica, nível a laser ou equivalente. Medição errada = móvel que não encaixa.</p>

<h3>8. Pergunte sobre o processo de projeto</h3>
<p>Existe projeto 3D? Você aprova o projeto antes da fabricação? Como são feitas as alterações? Ter clareza sobre o processo evita mal-entendidos.</p>

<h2>Durante o projeto</h2>

<h3>9. Confirme os materiais antes da fabricação</h3>
<p>Antes de começarem a cortar o material, confirme: cor, espessura, acabamento, tipo de ferragem. Peça amostras se possível.</p>

<h3>10. Verifique as ferragens</h3>
<p>Pergunte qual marca de dobradiças, corrediças e puxadores serão usados. Marcas como Hettich, Blum e FGV são referência. Ferragens de qualidade fazem o móvel durar muito mais.</p>

<h3>11. Pergunte sobre o processo de instalação</h3>
<p>A instalação é feita por quem? A equipe tem experiência? Eles protegem o piso e as paredes durante a instalação?</p>

<h2>Após a entrega</h2>

<h3>12. Inspecione antes de assinar a entrega</h3>
<p>Verifique cada porta, cada gaveta, cada encaixe. Conferir tudo antes de assinar o Termo de Entrega é o momento de reportar qualquer problema.</p>

<h3>13. Confirme o prazo de garantia</h3>
<p>Saiba quanto tempo dura a garantia e o que ela cobre. Tenha o contrato por escrito.</p>

<h3>14. Pergunte sobre manutenção</h3>
<p>Saiba como limpar e cuidar do material. Cada acabamento tem suas particularidades.</p>

<h3>15. Guarde o contrato e o projeto</h3>
<p>Mantenha uma cópia do projeto, do orçamento e do contrato. Pode ser útil em futuras reformas ou ampliações.</p>

<h2>Resumo do checklist</h2>

<ul>
<li>Portfólio com projetos reais</li>
<li>Endereço fixo e CNPJ</li>
<li>Material detalhado no orçamento</li>
<li>Orçamento itemizado</li>
<li>Garantia por escrito</li>
<li>Prazo definido</li>
<li>Medição profissional</li>
<li>Projeto 3D com aprovação</li>
<li>Materiais confirmados antes da fabricação</li>
<li>Ferragens de qualidade</li>
<li>Equipe de instalação qualificada</li>
<li>Inspeção antes de assinar</li>
<li>Garantia documentada</li>
<li>Instruções de manutenção</li>
<li>Projeto e contrato guardados</li>
</ul>

<div class="blog-cta-box">
<h3>Quer um orçamento sem surpresas?</h3>
<p>A LC Soluções em Móveis trabalha com transparência total: orçamento detalhado, projeto 3D, materiais de qualidade e garantia. Fale conosco.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20or%C3%A7amento%20transparente%20de%20m%C3%B3veis%20planejados">Solicitar Orçamento</a></p>
</div>',
'Checklist completo com 15 pontos para verificar antes de contratar uma marcenaria. Guia prático para evitar problemas e garantir um projeto bem-feito.',
'/assets/img/blog/1778677762_IMG-20250507-WA0001.jpg',
'Guia do Cliente',
@cat_guia,
'Checklist: 15 Coisas para Conferir antes de Contratar uma Marcenaria | Blog LC',
'Checklist completo com 15 pontos para conferir antes de contratar uma marcenaria em Campo Grande. Evite problemas e garanta um projeto de qualidade.',
'publicado',
NOW()
);

-- POST 11: Como Escolher uma Boa Marcenaria em Campo Grande
SET @cat_cg = (SELECT `id` FROM `categorias` WHERE `slug` = 'campo-grande-ms' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'Como Escolher uma Boa Marcenaria em Campo Grande',
'como-escolher-marcenaria-campo-grande',
'<h2>Escolher uma boa marcenaria em Campo Grande não é só questão de preço. É sobre qualidade, confiança e resultado. Veja o que verificar antes de tomar sua decisão.</h2>

<p>Campo Grande tem opções de marcenarias — de pequenas oficinas a empresas mais estruturadas. Mas nem todas oferecem o mesmo nível de qualidade, profissionalismo e compromisso com o resultado.</p>

<p>Se você está buscando uma marcenaria na capital sul-mato-grossense, este guia vai ajudar você a identificar os pontos que realmente importam.</p>

<h2>1. Pesquise o trabalho da empresa</h2>

<p>Antes de ligar ou enviar mensagem, pesquise. Veja o portfólio da empresa no Instagram, no Google Meu Negócio e no site. Procure por projetos similares ao que você precisa.</p>

<p>Uma marcenaria que mostra seus trabalhos reais transmite mais confiança do que uma que apenas diz que é boa.</p>

<h2>2. Verifique a reputação online</h2>

<p>Veja as avaliações no Google, no Reclame Aqui e nas redes sociais. Não se preocupe com avaliações negativas pontuais — o que importa é o padrão geral e como a empresa responde aos problemas.</p>

<h2>3. Confirme se tem endereço fixo em Campo Grande</h2>

<p>Uma marcenaria com endereço físico oferece mais segurança. Se o projeto der algum problema, você sabe onde ir. Empresas que funcionam apenas por WhatsApp, sem endereço registrado, são mais arriscadas.</p>

<h2>4. Pergunte sobre o material</h2>

<p>Uma boa marcenaria vai te explicar, detalhadamente, qual material será usado. Ela vai te mostrar amostras, explicar as diferenças entre MDF e MDP, e te ajudar a escolher o melhor para cada ambiente.</p>

<p>Se a empresa não sabe te explicar qual MDF usa ou de qual fornecedor vem, é um sinal de alerta.</p>

<h2>5. Veja exemplos de ferragens</h2>

<p>As ferragens são o "motor" do móvel. Dobradiças ruins fazem as portas caírem em poucos meses. Corrediças baratas travam e quebram. Pergunte qual marca de ferragens a empresa usa.</p>

<p>Marcas como Hettich, Blum, FGV e Docol são referência. Se a empresa não sabe te dizer qual marca usa, desconfie.</p>

<h2>6. Peça um orçamento detalhado</h2>

<p>Um orçamento que diz apenas "cozinha planejada: R$ 12.000" sem detalhar nada, não é um bom orçamento. O ideal é que cada componente esteja listado: material, ferragens, acabamento, instalação.</p>

<h2>7. Avalie o atendimento</h2>

<p>O atendimento reflete o profissionalismo. Uma marcenaria que responde rapidamente, tira suas dúvidas e te orienta com segurança provavelmente vai entregar um bom resultado.</p>

<p>Empresas que pressionam para fechar rápido, não explicam detalhes ou evitam perguntas são sinal de alerta.</p>

<h2>8. Pergunte sobre prazo e garantia</h2>

<p>Prazo de entrega: quantos dias após a aprovação do projeto? Garantia: quanto tempo e o que cobre? Tudo deve ser documentado.</p>

<h2>9. Considere a experiência da empresa</h2>

<p>Uma marcenaria que trabalha há mais tempo em Campo Grande tende a ter mais experiência com os tipos de imóveis da região, o clima (que afeta o material) e as necessidades dos moradores locais.</p>

<h2>10. Não se guie apenas pelo preço</h2>

<p>O preço mais baixo nem sempre é a melhor opção. Muitas vezes, materiais inferiores e mão de obra amadora resultam em móveis que duram pouco. O investimento em qualidade se paga no longo prazo.</p>

<h2>Resumo</h2>

<p>Escolher uma boa marcenaria em Campo Grande é um processo que exige pesquisa, perguntas e atenção aos detalhes. Não se guie apenas pelo preço — o mais barato frequentemente sai mais caro no longo prazo.</p>

<div class="blog-cta-box">
<h3>Procurando uma marcenaria de confiança em Campo Grande?</h3>
<p>A LC Soluções em Móveis atende Campo Grande e região com projetos personalizados, materiais de qualidade e instalação profissional. Veja nossos trabalhos e solicite um orçamento.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20saber%20mais%20sobre%20a%20LC%20Solu%C3%A7%C3%B5es">Conhecer a LC Soluções</a></p>
</div>',
'Guia completo para escolher uma boa marcenaria em Campo Grande. 10 pontos para verificar: portfólio, materiais, ferragens, prazo, garantia e atendimento.',
'/assets/img/blog/1778677773_IMG-20250507-WA0007.jpg',
'Campo Grande MS',
@cat_cg,
'Como Escolher uma Boa Marcenaria em Campo Grande | Blog LC Soluções',
'Guia completo para escolher uma marcenaria em Campo Grande. 10 pontos essenciais para verificar: portfólio, materiais, ferragens, prazo e garantia.',
'publicado',
NOW()
);

-- POST 12: Móveis Planejados ou Prontos: Qual Vale Mais a Pena?
SET @cat_moveis = (SELECT `id` FROM `categorias` WHERE `slug` = 'moveis-planejados' LIMIT 1);

INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (
'Móveis Planejados ou Prontos: Qual Vale Mais a Pena?',
'moveis-planejados-ou-prontos-qual-vale-mais',
'<h2>Essa é uma das dúvidas mais comuns de quem está reformando: é melhor investir em móveis planejados ou comprar móveis prontos? A resposta depende do que você prioriza.</h2>

<p>Lojas de móveis oferecem opções prontas com design moderno e preços aparentemente acessíveis. Mas será que a longo prazo, esses móveis entregam o que prometem?</p>

<p>Vamos comparar os dois de forma objetiva, para que você tome a melhor decisão para a sua situação.</p>

<h2>Móveis prontos: vantagens e limitações</h2>

<h3>Vantagens</h3>
<ul>
<li><strong>Entrega rápida</strong> — você compra e recebe em poucos dias, às vezes no mesmo dia</li>
<li><strong>Preço aparente baixo</strong> — o valor na prateleira parece mais acessível</li>
<li><strong>Design de catálogo</strong> — modelos testados e aprovados pelo mercado</li>
<li><strong>Facilidade de compra</strong> — basta escolher e levar</li>
</ul>

<h3>Limitações</h3>
<ul>
<li><strong>Medidas padrão</strong> — não se adaptam perfeitamente ao seu espaço. Sobram gaps ou faltam centímetros</li>
<li><strong>Material inferior</strong> — muitos móveis prontos usam MDP, compensado ou MDF de 12mm</li>
<li><strong>Durabilidade limitada</strong> — com uso intenso, podem durar de 3 a 7 anos</li>
<li><strong>Personalização zero</strong> — você se adapta ao móvel, não o contrário</li>
<li><strong>Ferragens simples</strong> — dobradiças e corrediças de qualidade inferior</li>
</ul>

<h2>Móveis planejados: vantagens e considerações</h2>

<h3>Vantagens</h3>
<ul>
<li><strong>Medidas sob medida</strong> — se encaixam perfeitamente no seu espaço, aproveitando cada centímetro</li>
<li><strong>Material de qualidade</strong> — MDF 18mm, ferragens de marcas reconhecidas, acabamento profissional</li>
<li><strong>Durabilidade superior</strong> — com manutenção adequada, duram 15 a 20 anos</li>
<li><strong>Personalização total</strong> — cores, divisórias, organização interna, tudo do seu jeito</li>
<li><strong>Aproveitamento do espaço</strong> — armários que vão até o teto, encaixes em cantos difíceis, soluções para espaços pequenos</li>
<li><strong>Valorização do imóvel</strong> — ambientes com móveis planejados valorizam o imóvel</li>
</ul>

<h3>Considerações</h3>
<ul>
<li><strong>Prazo de entrega</strong> — de 30 a 45 dias em média, dependendo da marcenaria</li>
<li><strong>Investimento inicial maior</strong> — o custo inicial é mais alto que móveis prontos</li>
<li><strong>Requer projeto profissional</strong> — não é algo que se resolve em uma ida à loja</li>
</ul>

<h2>Comparação lado a lado</h2>

<h3>Aproveitamento do espaço</h3>
<p><strong>Prontos:</strong> Medidas fixas. Se seu quarto tem 3,20m de parede e o armário tem 3,00m, sobram 20cm que viram espaço morto.</p>
<p><strong>Planejados:</strong> Medidas exatas. O móvel ocupa exatamente o espaço disponível, sem desperdício.</p>

<h3>Durabilidade</h3>
<p><strong>Prontos:</strong> MDP de 12-15mm, dobradiças simples. Vida útil de 3 a 7 anos.</p>
<p><strong>Planejados:</strong> MDF 18mm, ferragens Hettich/Blum. Vida útil de 15 a 20+ anos.</p>

<h3>Investimento vs. custo</h3>
<p><strong>Prontos:</strong> Custo inicial baixo, mas troca mais frequente. Custo total em 10 anos pode ser maior.</p>
<p><strong>Planejados:</strong> Custo inicial maior, mas durabilidade muito superior. Custo total ao longo dos anos é menor.</p>

<h3>Funcionalidade</h3>
<p><strong>Prontos:</strong> Organização genérica. Divisórias fixas. Gavetas padrão.</p>
<p><strong>Planejados:</strong> Organização personalizada. Divisórias sob medida. Organizadores internos para cada necessidade.</p>

<h2>Quando móveis prontos fazem sentido?</h2>

<ul>
<li>Móvel provisório para aluguel temporário</li>
<li>Budget muito apertado que não permite investimento</li>
<li>Ambiente que vai ser reformado em breve</li>
<li>Peças pequenas como criados-mudo, estantes simples</li>
</ul>

<h2>Quando móveis planejados são a melhor opção?</h2>

<ul>
<li>Ambientes com dimensões específicas (cozinhas, banheiros, closets)</li>
<li>Espaços pequenos onde cada centímetro conta</li>
<li>Quer durabilidade e qualidade</li>
<li>Projetos que envolvem vários ambientes</li>
<li>Valorização do imóvel é um objetivo</li>
</ul>

<h2>Conclusão</h2>

<p>Para ambientes como cozinhas, quartos com closet, salas com painel de TV e home offices, os móveis planejados são quase sempre a melhor opção. O aproveitamento do espaço, a durabilidade e a personalização superam o custo inicial.</p>

<p>Para peças avulsas ou ambientes temporários, os móveis prontos podem ser uma alternativa viável.</p>

<div class="blog-cta-box">
<h3>Quer saber qual é a melhor opção para o seu caso?</h3>
<p>A LC Soluções em Móveis pode avaliar seu espaço e mostrar, na prática, como os móveis planejados podem transformar seus ambientes. Sem compromisso.</p>
<p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20saber%20se%20móveis%20planejados%20valem%20a%20pena%20para%20meu%20caso">Solicitar Avaliação Gratuita</a></p>
</div>',
'Móveis planejados ou prontos: qual a melhor opção? Compare durabilidade, preço, aproveitamento do espaço e funcionalidade para tomar a decisão certa.',
'/assets/img/blog/1778676743_IMG-20250507-WA0007.jpg',
'Móveis Planejados',
@cat_moveis,
'Móveis Planejados ou Prontos: Qual Vale Mais a Pena? | Blog LC Soluções',
'Móveis planejados ou prontos: compare durabilidade, preço, aproveitamento do espaço e funcionalidade. Entenda qual é a melhor opção para cada situação.',
'publicado',
NOW()
);

-- =============================================
-- FIM DA MIGRAÇÃO
-- =============================================
