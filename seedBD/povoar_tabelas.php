<?php
require_once '../includes/db.php'; // Ajusta o caminho se necessário

function seedUsers($pdo) {
    $nomesCompletos = [
        'Andre Sousa',
        'Joana Dias',
        'Miguel Ferreira',
        'Sara Costa',
        'Tiago Martins',
        'Ines Rocha',
        'Pedro Silva',
        'Carla Ribeiro',
        'Luis Almeida',
        'Ana Monteiro'
    ];

    foreach ($nomesCompletos as $nomeCompleto) {
        $partes = explode(' ', $nomeCompleto);
        $primeiroNome = strtolower($partes[0]); // usado para o email e password
        $email = "$primeiroNome@$primeiroNome.com";
        $password = password_hash($primeiroNome, PASSWORD_DEFAULT);
        $birth_date = '1990-01-01';
        $nationality = 'Portuguesa';
        $country = 'Portugal';
        $phone = '912345678';

        $stmt = $pdo->prepare("INSERT INTO users 
            (name, birth_date, nationality, country, email, phone, password, is_admin, email_verified, status, is_verified)
            VALUES (?, ?, ?, ?, ?, ?, ?, 0, 1, 'ativo', 1)");

        try {
            $stmt->execute([$nomeCompleto, $birth_date, $nationality, $country, $email, $phone, $password]);
            echo "✔️ Utilizador '$nomeCompleto' inserido com sucesso.\n";
        } catch (PDOException $e) {
            echo "❌ Erro ao inserir '$nomeCompleto': " . $e->getMessage() . "\n";
        }
    }

// Cria o administrador se não existir
try {
    
    // Verificar se o admin já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => 'admin@admin.com']);
    $adminExiste = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$adminExiste) {
        // Criar o admin com password hash
        $passwordAdmin = password_hash('admin', PASSWORD_DEFAULT);
        $stmtInsert = $pdo->prepare("INSERT INTO users (name, birth_date, nationality, country, email, phone, password, is_admin, email_verified, is_verified, status) VALUES (:name, :birth_date, :nationality, :country, :email, :phone, :password, :is_admin, :email_verified, :is_verified, :status)");
        $stmtInsert->execute([
            ':name' => 'Admin',
            ':birth_date' => '2000-01-01',
            ':nationality' => 'PT',
            ':country' => 'Portugal',
            ':email' => 'admin@admin.com',
            ':phone' => '',
            ':password' => $passwordAdmin,
            ':is_admin' => 1,
            ':email_verified' => 1,
            ':is_verified' => 1,
            ':status' => 'ativo',
        ]);
        echo "--- \n✅ Utilizadores criados com sucesso.\n✅ Admin criado com sucesso (admin@admin.com / admin).\n --- \n";
    } else {
        // Se admin já existe, garante que está configurado corretamente (atualiza)
        $stmtUpdate = $pdo->prepare("UPDATE users SET is_admin = 1, email_verified = 1, is_verified = 1 WHERE email = :email");
        $stmtUpdate->execute([':email' => 'admin@admin.com']);
        echo "--- \n✅ Utilizadores atualizados com sucesso.\n❌ Admin já existia, dados atualizados.\n --- \n";
    }
    
} catch (PDOException $e) {
    echo "--- \nErro ao povoar as tabelas: " . $e->getMessage();
}


}

function seedArtigosENoticias($pdo) {
    // Buscar todos os IDs dos utilizadores existentes
    $stmt = $pdo->query("SELECT id FROM users");
    $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($userIds)) {
        echo "⚠️ Nenhum utilizador encontrado para associar artigos/notícias.\n";
        return;
    }

    // Artigos (com texto completo)
    $artigos = [
        [
            'title' => 'O Impacto da Nutrição no Rendimento Desportivo',
            'image' => '/uploads/artigos/img_1.jpg',
            'content' => "A alimentação é um dos pilares fundamentais do desempenho desportivo. 
Muitos atletas ignoram a importância de uma dieta equilibrada, mas a verdade é que sem uma nutrição adequada, é impossível atingir o pico de performance.

Além da ingestão correta de macronutrientes, como proteínas, hidratos de carbono e gorduras saudáveis, é essencial garantir uma boa hidratação e o consumo de micronutrientes. 
O acompanhamento com nutricionistas desportivos tem-se tornado cada vez mais comum, potenciando os resultados de treinos intensos."
        ],
        [
            'title' => 'Treinar com Peso Corporal: Eficiente e Acessível',
            'image' => '/uploads/artigos/img_2.jpg',
            'content' => "O treino com peso corporal tem vindo a ganhar popularidade, especialmente entre atletas que procuram desenvolver força funcional sem necessidade de equipamentos.

Flexões, agachamentos, pranchas e burpees são apenas alguns dos exercícios que demonstram ser extremamente eficazes na melhoria da condição física geral. 
Este tipo de treino promove a coordenação, resistência e pode ser feito em qualquer lugar, tornando-o ideal para estilos de vida ocupados."
        ],
        [
            'title' => 'A Preparação Psicológica nos Desportos de Alta Competição',
            'image' => '/uploads/artigos/img_3.jpg',
            'content' => "Não basta ter apenas capacidade física: a força mental é crucial em desportos de alto rendimento. 
Psicólogos desportivos têm um papel cada vez mais relevante na preparação dos atletas.

A gestão da ansiedade, concentração e foco são aspetos trabalhados para que o atleta mantenha a consistência ao mais alto nível. 
Ferramentas como a visualização, respiração controlada e rotinas pré-competição são agora parte integrante do treino."
        ],
        [
            'title' => 'Benefícios do Ciclismo para a Saúde Cardiovascular',
            'image' => '/uploads/artigos/img_4.jpg',
            'content' => "O ciclismo é um dos exercícios aeróbicos mais completos, trazendo imensos benefícios para a saúde do coração. 
Este desporto melhora a circulação sanguínea, reduz a pressão arterial e fortalece o sistema cardiovascular.

Além disso, andar de bicicleta contribui para o bem-estar mental, sendo uma excelente forma de aliviar o stress diário. 
É também uma alternativa ecológica de transporte nas cidades."
        ],
        [
            'title' => 'O Regresso dos Espectadores aos Estádios: O Novo Normal',
            'image' => '/uploads/artigos/img_5.jpg',
            'content' => "Com o fim das restrições pandémicas, os adeptos voltam a encher os estádios com entusiasmo redobrado. 
Este regresso tem um impacto significativo na motivação dos jogadores e na receita dos clubes.

No entanto, continuam a existir medidas preventivas, como a desinfeção de mãos, bilhetes digitais e controlo de lotação. 
A ligação emocional entre equipa e adeptos mostrou-se mais forte do que nunca."
        ],
        [
            'title' => 'A Evolução do Futebol Feminino em Portugal',
            'image' => '/uploads/artigos/img_6.jpg',
            'content' => "O futebol feminino tem registado um crescimento notável em Portugal, com mais atletas, clubes e apoios federativos. 
As transmissões televisivas e patrocínios também aumentaram a visibilidade e profissionalismo da modalidade.

Jogadoras portuguesas começam a destacar-se em campeonatos internacionais, elevando o nome do país além-fronteiras. 
Ainda há desafios, mas o futuro promete."
        ],
        [
            'title' => 'O Papel do Sono na Recuperação Desportiva',
            'image' => '/uploads/artigos/img_7.jpg',
            'content' => "O sono é fundamental para a regeneração muscular e recuperação após esforço físico. 
Atletas que dormem menos de 7 horas por noite têm maior risco de lesões.

Estudos apontam que a qualidade do sono afeta diretamente a performance. 
Técnicas como a higiene do sono, meditação e suplementação natural têm sido utilizadas por profissionais para melhorar o descanso noturno."
        ],
        [
            'title' => 'Corrida Urbana: Como Tirar Partido da Cidade',
            'image' => '/uploads/artigos/img_8.jpg',
            'content' => "A corrida urbana é uma tendência crescente, combinando desporto com a descoberta da cidade. 
Ao evitar monotonia, os corredores exploram novos trajetos, parques e locais históricos.

Além disso, a prática ajuda a reduzir o stress e melhora a condição física de forma geral. 
Correr em grupo também promove a socialização e consistência nos treinos."
        ],
        [
            'title' => 'Tecnologia no Desporto: Wearables e Aplicações',
            'image' => '/uploads/artigos/img_9.jpg',
            'content' => "Dispositivos como smartwatches e aplicações de treino revolucionaram a forma como os atletas monitorizam o seu progresso. 
A medição em tempo real de batimentos cardíacos, calorias e GPS permite ajustes imediatos nos treinos.

Além disso, plataformas de análise de dados ajudam treinadores e atletas a melhorar estratégias e prevenir lesões. 
A tecnologia está cada vez mais integrada no desporto moderno."
        ],
        [
            'title' => 'Desporto Escolar: A Base para Hábitos Saudáveis',
            'image' => '/uploads/artigos/img_10.jpg',
            'content' => "O desporto nas escolas tem um papel vital na promoção de hábitos saudáveis desde cedo. 
Além dos benefícios físicos, promove valores como disciplina, cooperação e respeito.

Programas escolares bem estruturados são essenciais para identificar talentos e garantir uma base sólida para futuras gerações de atletas. 
A articulação entre professores, pais e clubes é essencial."
        ],
    ];

    // Notícias (com texto completo)
    $noticias = [
        ['Portugal vence a Liga das Nações', 'img_1.jpg', "A seleção nacional conquistou novamente a Liga das Nações após uma final intensa frente à França. 
Com golos de Bruno Fernandes e Bernardo Silva, a equipa das Quinas dominou o encontro com grande maturidade tática.

Este título reforça o bom momento do futebol português a nível internacional, destacando o trabalho da equipa técnica e a qualidade dos jogadores convocados. 
A festa estendeu-se pelas ruas de Lisboa e do Porto, com milhares de adeptos a celebrar."],
        ['Maratona de Lisboa com recorde de participantes', 'img_2.jpg', "A edição deste ano da Maratona de Lisboa contou com mais de 30.000 corredores de todo o mundo. 
O evento decorreu com excelentes condições meteorológicas, e o vencedor da prova masculina terminou com um tempo recorde.

O percurso junto ao Tejo continua a ser um dos preferidos a nível internacional. 
A organização destacou o impacto turístico e económico da prova na cidade."],
        ['Surfista português brilha no Havai', 'img_3.jpg', "João Guedes conquistou o segundo lugar numa das mais prestigiadas provas de surf no Havai. 
Apesar das ondas desafiantes, mostrou técnica e coragem, recebendo aplausos dos espectadores.

Este resultado coloca-o entre os melhores surfistas da atualidade e inspira a nova geração portuguesa a apostar nesta modalidade. 
O atleta agradeceu o apoio da comunidade e prometeu continuar a dar o seu melhor."],
        ['Atleta paralímpico bate novo recorde europeu', 'img_4.jpg', "Carlos Lopes, atleta paralímpico português, bateu o recorde europeu nos 100 metros T44 durante os campeonatos de atletismo em Berlim. 
Com uma performance impressionante, subiu ao pódio com medalha de ouro.

Esta conquista realça o potencial e a dedicação dos atletas paralímpicos em Portugal, que têm vindo a ganhar maior reconhecimento e apoio. 
Lopes dedica a vitória à sua família e equipa técnica."],
        ['Novos equipamentos no Estádio Nacional', 'img_5.jpg', "O Estádio Nacional foi alvo de uma renovação profunda, recebendo novos equipamentos tecnológicos e de segurança. 
Entre as novidades estão sistemas de iluminação LED, câmaras de vigilância inteligentes e cadeiras mais confortáveis.

Estas melhorias visam proporcionar melhor experiência a adeptos e atletas, além de cumprir normas internacionais para competições. 
O presidente da Federação mostrou-se satisfeito com o investimento."],
        ['Equipa feminina de andebol vence campeonato nacional', 'img_6.jpg', "O SL Benfica sagrou-se campeão nacional de andebol feminino após uma temporada invicta. 
Com uma defesa sólida e ataques rápidos, dominou a competição desde o início.

O treinador destacou o esforço coletivo e o espírito de equipa como chaves para o sucesso. 
A equipa prepara-se agora para representar Portugal em torneios europeus, com ambições elevadas."]
    ];

$adminId = 1; // ou outro valor que seja o ID do admin

// Remove o admin do array de usuários possíveis
$filteredUserIds = array_filter($userIds, fn($id) => $id !== $adminId);

foreach ($artigos as $artigo) {
    $userId = $filteredUserIds[array_rand($filteredUserIds)];
    $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, image, content) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$userId, $artigo['title'], $artigo['image'], $artigo['content']]);
        echo "📝 Artigo '{$artigo['title']}' inserido com sucesso.\n";
    } catch (PDOException $e) {
        echo "❌ Erro ao inserir artigo '{$artigo['title']}': " . $e->getMessage() . "\n";
    }
}

foreach ($noticias as $noticia) {
    $stmt = $pdo->prepare("INSERT INTO noticias (titulo, imagem, texto) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$noticia[0], $noticia[1], $noticia[2]]);
        echo "📰 Notícia '{$noticia[0]}' inserida com sucesso.\n";
    } catch (PDOException $e) {
        echo "❌ Erro ao inserir notícia '{$noticia[0]}': " . $e->getMessage() . "\n";
    }
}
echo "--- \n✅ Artigos e notícias inseridos com sucesso.\n";
}

// Executar as funções
seedUsers($pdo);
seedArtigosENoticias($pdo);