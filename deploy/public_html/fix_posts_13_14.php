<?php
/**
 * Fix script: insert posts 13 and 14 that failed during migration
 * due to semicolons inside HTML content breaking the SQL parser.
 * DELETE this file after use!
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$db_host = 'localhost';
$db_name = 'luizc159_lcsolucoes_site';
$db_user = 'luizc159_joao';
$db_pass = 'Jm@10653407388336141$';

echo "<h1>Fix: Insert Posts 13 & 14</h1>";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>Connected</p>";
} catch (PDOException $e) {
    die("<p style='color:red'>Connection error: " . $e->getMessage() . "</p>");
}

// Check if already inserted
$stmt = $pdo->query("SELECT id, titulo FROM posts WHERE slug IN ('quanto-custa-cozinha-planejada-campo-grande', 'cozinha-planejada-o-que-definir-antes-do-projeto') ORDER BY id");
$existing = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($existing) >= 2) {
    echo "<p style='color:blue'>Posts 13 and 14 already exist. Skipping.</p>";
    foreach ($existing as $e) echo "<p>  ID {$e['id']}: {$e['titulo']}</p>";
    exit;
}

// Get category IDs
$stmt = $pdo->query("SELECT id, slug FROM categorias");
$cats = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $cats[$row['slug']] = $row['id'];

$cat_precos = $cats['precos-e-orcamentos'] ?? 1;
$cat_ambientes = $cats['ambientes'] ?? 1;

// POST 13
$titulo13 = 'Quanto Custa uma Cozinha Planejada em Campo Grande?';
$slug13 = 'quanto-custa-cozinha-planejada-campo-grande';
$conteudo13 = '<h2>A cozinha planejada é o ambiente mais desejado — e também o que mais gera dúvidas sobre o investimento. Veja as faixas de preço e o que realmente influencia o custo.</h2><p>Planejar uma cozinha em Campo Grande envolve pensar no layout, nos materiais, nas ferragens, no acabamento e na instalação. Cada escolha impacta no valor final do projeto.</p><p>Mas quanto custa, de fato, uma cozinha planejada na capital sul-mato-grossense? Veja as faixas de valores e o que você precisa considerar.</p><h2>Faixas de preço por tamanho</h2><h3>Cozinha compacta (até 6m²)</h3><p>Ideal para apartamentos pequenos e flats. Geralmente inclui armários superiores e inferiores, gaveteiro, espaço para fogão e geladeira.</p><p><strong>Faixa de investimento: R$ 6.000 a R$ 12.000</strong></p><h3>Cozinha média (6m² a 12m²)</h3><p>A mais comum em apartamentos e casas de Campo Grande. Permite incluir ilha ou balcão, mais gavetas e organizadores internos.</p><p><strong>Faixa de investimento: R$ 10.000 a R$ 20.000</strong></p><h3>Cozinha grande (acima de 12m²)</h3><p>Permite layout completo com ilha, armários até o teto, puxadores especiais, iluminação embutida e integração com sala de estar.</p><p><strong>Faixa de investimento: R$ 18.000 a R$ 35.000+</strong></p><h2>O que influi no preço?</h2><h3>Material</h3><p>MDF 18mm é o padrão de qualidade. MDF 15mm é mais econômico mas menos resistente. MDP é a opção mais barata, porém com menor durabilidade — especialmente em cozinhas, onde a umidade é um fator constante.</p><h3>Acabamento</h3><p>Laminação (fapes) é o mais acessível. Pintura PU, AC3 e folheados especiais encarecem o projeto, mas oferecem mais liberdade de design.</p><h3>Ferragens</h3><p>Corrediças com soft-close, dobradiças Hettich ou Blum, e organizadores internos (badejes, cestos, divisórias) aumentam o custo, mas melhoram muito a funcionalidade.</p><h3>Balcão</h3><p>Balancos em granito, quartzo ou acrílico têm preços diferentes. Granito é mais acessível — quartzo e acrílico são mais sofisticados e caros.</p><h3>Iluminação</h3><p>Luzes embutidas sob os armários superiores, iluminação LED de cor e spots de destaque são opcionais que fazem grande diferença no visual final.</p><h2>O que está incluso no projeto?</h2><p>Uma cozinha planejada completa geralmente inclui:</p><ul><li>Medição do ambiente</li><li>Projeto 3D com aprovação</li><li>Fabricação dos módulos</li><li>Ferragens e acessórios</li><li>Instalação</li><li>Garantia</li></ul><p>Pergunte sempre o que está incluso. Algumas marcenarias cobram projeto, instalação e ferragens separadamente.</p><h2>Cozinha planejada vs. cozinha pronta</h2><p>Uma cozinha pronta de grandes lojas pode parecer mais barata à primeira vista. Mas as medidas padrão raramente se encaixam perfeitamente no seu espaço, sobrando gaps e desperdiçando centímetros. Com o tempo, as ferragens de qualidade inferior começam a dar problema.</p><p>A cozinha planejada, apesar do investimento inicial maior, oferece aproveitamento total do espaço, durabilidade muito superior e um visual exclusivo.</p><h2>Perguntas frequentes</h2><h3>Quanto tempo leva para montar uma cozinha planejada?</h3><p>Em média, de 30 a 45 dias úteis após a aprovação do projeto. Projetos maiores ou com materiais importados podem levar mais.</p><h3>Posso parcelar?</h3><p>Sim, a maioria das marcenarias oferece parcelamento. Na LC Soluções, trabalhamos com condições que facilitam o investimento.</p><h3>A instalação está inclusa?</h3><p>Sim, na LC Soluções a instalação faz parte do projeto completo. Não há custo adicional para montagem.</p><div class="blog-cta-box"><h3>Planejando sua cozinha em Campo Grande?</h3><p>A LC Soluções em Móveis pode projetar e montar sua cozinha planejada com materiais de qualidade e acabamento profissional. Solicite um orçamento sem compromisso.</p><p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20um%20or%C3%A7amento%20de%20cozinha%20planejada">Solicitar Orçamento de Cozinha</a></p></div>';

$resumo13 = 'Quanto custa uma cozinha planejada em Campo Grande? Veja faixas de preço por tamanho, o que influi no orçamento e como escolher a melhor opção.';
$imagem13 = '/assets/img/cozinha/IMG-20250507-WA0041.jpg';
$meta13 = 'Quanto Custa uma Cozinha Planejada em Campo Grande? | Blog LC Soluções';
$meta_desc13 = 'Descubra quanto custa uma cozinha planejada em Campo Grande. Faixas de preço por tamanho, materiais, ferragens e o que está incluso no projeto.';

$stmt13 = $pdo->prepare("INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'publicado', NOW())");
$stmt13->execute([$titulo13, $slug13, $conteudo13, $resumo13, $imagem13, 'Preços e Orçamentos', $cat_precos, $meta13, $meta_desc13]);
echo "<p style='color:green'>✅ Post 13 inserted (ID: " . $pdo->lastInsertId() . "): $titulo13</p>";

// POST 14
$titulo14 = 'Cozinha Planejada: O Que Você Precisa Definir Antes do Projeto';
$slug14 = 'cozinha-planejada-o-que-definir-antes-do-projeto';
$conteudo14 = '<h2>Antes de pedir uma proposta para sua cozinha planejada, existem decisões importantes que você precisa tomar. Saber o que quer facilita o projeto e evita surpresas.</h2><p>Uma cozinha planejada não começa com o corte do MDF. Ela começa com decisões que você precisa tomar antes de entrar em contato com qualquer marcenaria. Essas escolhas determinam o resultado final, o prazo e o investimento.</p><p>Veja o que definir antes de iniciar o projeto da sua cozinha.</p><h2>1. Layout da cozinha</h2><p>O layout é a disposição dos armários e equipamentos no ambiente. Os layouts mais comuns são:</p><ul><li><strong>Linha reta</strong> — todos os armários em uma parede. Ideal para cozinhas pequenas.</li><li><strong>L</strong> — armários em duas paredes adjacentes. Oferece mais espaço e gavetas.</li><li><strong>U</strong> — armários em três paredes. Máximo de armazenamento.</li><li><strong>Com ilha</strong> — inclui uma ilha central. Precisa de espaço mínimo de 90cm de passagem ao redor.</li></ul><p>Pense no fluxo de trabalho: geladeira → pia → fogão. Quanto mais lógico esse fluxo, mais prática será a cozinha.</p><h2>2. Equipamentos que serão embutidos</h2><p>Defina antecipadamente quais eletrodomésticos serão embutidos: fogão, cooktop, forno, micro-ondas, máquina de louça, extractor. Cada um requer um módulo específico com dimensões exatas.</p><p>Se você ainda não comprou os eletrodomésticos, defina as marcas e modelos antes do projeto. As dimensões dos aparelhos determinam o tamanho dos módulos.</p><h2>3. Tipo de bancada</h2><p>A bancada é a superfície de trabalho. As opções mais comuns são:</p><ul><li><strong>Granito</strong> — resistente, durável, preço acessível</li><li><strong>Quartzo</strong> — mais sofisticado, resistente a manchas, mais caro</li><li><strong>Acrílico</strong> — visual contínuo, sem emendas visíveis</li><li><strong>Silestone</strong> — pedra engineered, alta resistência</li></ul><p>Considere a resistência a manchas, à umidade e ao calor. Em cozinhas, a bancada recebe bastante uso.</p><h2>4. Acabamento dos armários</h2><p>O acabamento define o visual da cozinha. As opções incluem:</p><ul><li><strong>Laminação (fapes)</strong> — econômica, variedade de cores e texturas</li><li><strong>Pintura PU</strong> — acabamento premium, cores personalizadas</li><li><strong>Folheado</strong> — simula madeira natural</li><li><strong>AC3</strong> — alta resistência a arranhões e manchas</li></ul><h2>5. Organização interna</h2><p>Pense em como você organiza sua cozinha hoje. O que funciona? O que falta? Defina:</p><ul><li>Tipos de gavetas (fundo, talheres, temperos)</li><li>Organizadores para panelas e tampas</li><li>Estantes para alimentos secos</li><li>Cestos para lixo reciclável</li><li>Espaço para produtos de limpeza</li></ul><h2>6. Iluminação</h2><p>Além da iluminação geral, considere:</p><ul><li>Luzes sob os armários superiores (trabalho na bancada)</li><li>Luzes dentro dos armários (facilita encontrar itens)</li><li>Luzes de destaque em nichos</li></ul><h2>7. Orçamento disponível</h2><p>Defina uma faixa de investimento antes de iniciar o projeto. Isso ajuda a marcenaria a oferecer opções adequadas ao seu bolso, sem surpresas.</p><p>Na LC Soluções em Móveis, trabalhamos com faixas de investimento variadas e sempre explicamos o que cada escolha impacta no valor final.</p><h2>Próximo passo</h2><p>Depois de definir esses pontos, entre em contato com uma marcenaria de confiança. Ela vai fazer a medição técnica, criar o projeto 3D e apresentar o orçamento detalhado.</p><div class="blog-cta-box"><h3>Quer ajuda para planejar sua cozinha?</h3><p>A LC Soluções em Móveis oferece projeto personalizado com medição técnica, visualização 3D e orçamento transparente. Fale conosco.</p><p><a href="https://wa.me/556732537898?text=Oi!%20Quero%20ajuda%20para%20planejar%20minha%20cozinha">Pedir Ajuda para Planejar</a></p></div>';

$resumo14 = 'Antes de pedir uma proposta de cozinha planejada, entenda o que precisa definir: layout, materiais, bancada, organização e orçamento.';
$imagem14 = '/assets/img/cozinha/IMG-20250507-WA0041.jpg';
$meta14 = 'Cozinha Planejada: O Que Definir Antes do Projeto | Blog LC Soluções';
$meta_desc14 = 'Guia completo sobre o que definir antes de projetar uma cozinha planejada: layout, materiais, bancada, organização, iluminação e orçamento.';

$stmt14 = $pdo->prepare("INSERT INTO `posts` (`titulo`, `slug`, `conteudo`, `resumo`, `imagem`, `categoria`, `categoria_id`, `meta_title`, `meta_description`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'publicado', NOW())");
$stmt14->execute([$titulo14, $slug14, $conteudo14, $resumo14, $imagem14, 'Ambientes', $cat_ambientes, $meta14, $meta_desc14]);
echo "<p style='color:green'>✅ Post 14 inserted (ID: " . $pdo->lastInsertId() . "): $titulo14</p>";

// Final count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM posts");
$final = $stmt->fetch()['total'];
echo "<hr><p><strong>Total posts now: $final</strong></p>";
echo "<p style='color:red;font-weight:bold'>DELETE this file after use!</p>";
?>
