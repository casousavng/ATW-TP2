-- Seed de artigos
INSERT INTO articles (user_id, title, image, content)
VALUES
(13, 'O Impacto da Nutrição no Rendimento Desportivo', '/uploads/artigos/img_1.jpg', 
'A alimentação é um dos pilares fundamentais do desempenho desportivo. 
Muitos atletas ignoram a importância de uma dieta equilibrada, mas a verdade é que sem uma nutrição adequada, é impossível atingir o pico de performance.

Além da ingestão correta de macronutrientes, como proteínas, hidratos de carbono e gorduras saudáveis, é essencial garantir uma boa hidratação e o consumo de micronutrientes. 
O acompanhamento com nutricionistas desportivos tem-se tornado cada vez mais comum, potenciando os resultados de treinos intensos.'),

(14, 'Treinar com Peso Corporal: Eficiente e Acessível', '/uploads/artigos/img_2.jpg', 
'O treino com peso corporal tem vindo a ganhar popularidade, especialmente entre atletas que procuram desenvolver força funcional sem necessidade de equipamentos.

Flexões, agachamentos, pranchas e burpees são apenas alguns dos exercícios que demonstram ser extremamente eficazes na melhoria da condição física geral. 
Este tipo de treino promove a coordenação, resistência e pode ser feito em qualquer lugar, tornando-o ideal para estilos de vida ocupados.'),

(14, 'A Preparação Psicológica nos Desportos de Alta Competição', '/uploads/artigos/img_3.jpg', 
'Não basta ter apenas capacidade física: a força mental é crucial em desportos de alto rendimento. 
Psicólogos desportivos têm um papel cada vez mais relevante na preparação dos atletas.

A gestão da ansiedade, concentração e foco são aspetos trabalhados para que o atleta mantenha a consistência ao mais alto nível. 
Ferramentas como a visualização, respiração controlada e rotinas pré-competição são agora parte integrante do treino.'),

(15, 'Benefícios do Ciclismo para a Saúde Cardiovascular', '/uploads/artigos/img_4.jpg', 
'O ciclismo é um dos exercícios aeróbicos mais completos, trazendo imensos benefícios para a saúde do coração. 
Este desporto melhora a circulação sanguínea, reduz a pressão arterial e fortalece o sistema cardiovascular.

Além disso, andar de bicicleta contribui para o bem-estar mental, sendo uma excelente forma de aliviar o stress diário. 
É também uma alternativa ecológica de transporte nas cidades.'),

(16, 'O Regresso dos Espectadores aos Estádios: O Novo Normal', '/uploads/artigos/img_5.jpg', 
'Com o fim das restrições pandémicas, os adeptos voltam a encher os estádios com entusiasmo redobrado. 
Este regresso tem um impacto significativo na motivação dos jogadores e na receita dos clubes.

No entanto, continuam a existir medidas preventivas, como a desinfeção de mãos, bilhetes digitais e controlo de lotação. 
A ligação emocional entre equipa e adeptos mostrou-se mais forte do que nunca.'),

(17, 'A Evolução do Futebol Feminino em Portugal', '/uploads/artigos/img_6.jpg', 
'O futebol feminino tem registado um crescimento notável em Portugal, com mais atletas, clubes e apoios federativos. 
As transmissões televisivas e patrocínios também aumentaram a visibilidade e profissionalismo da modalidade.

Jogadoras portuguesas começam a destacar-se em campeonatos internacionais, elevando o nome do país além-fronteiras. 
Ainda há desafios, mas o futuro promete.'),

(17, 'O Papel do Sono na Recuperação Desportiva', '/uploads/artigos/img_7.jpg', 
'O sono é fundamental para a regeneração muscular e recuperação após esforço físico. 
Atletas que dormem menos de 7 horas por noite têm maior risco de lesões.

Estudos apontam que a qualidade do sono afeta diretamente a performance. 
Técnicas como a higiene do sono, meditação e suplementação natural têm sido utilizadas por profissionais para melhorar o descanso noturno.'),

(17, 'Corrida Urbana: Como Tirar Partido da Cidade', '/uploads/artigos/img_8.jpg', 
'A corrida urbana é uma tendência crescente, combinando desporto com a descoberta da cidade. 
Ao evitar monotonia, os corredores exploram novos trajetos, parques e locais históricos.

Além disso, a prática ajuda a reduzir o stress e melhora a condição física de forma geral. 
Correr em grupo também promove a socialização e consistência nos treinos.'),

(18, 'Tecnologia no Desporto: Wearables e Aplicações', '/uploads/artigos/img_9.jpg', 
'Dispositivos como smartwatches e aplicações de treino revolucionaram a forma como os atletas monitorizam o seu progresso. 
A medição em tempo real de batimentos cardíacos, calorias e GPS permite ajustes imediatos nos treinos.

Além disso, plataformas de análise de dados ajudam treinadores e atletas a melhorar estratégias e prevenir lesões. 
A tecnologia está cada vez mais integrada no desporto moderno.'),

(20, 'Desporto Escolar: A Base para Hábitos Saudáveis', '/uploads/artigos/img_10.jpg', 
'O desporto nas escolas tem um papel vital na promoção de hábitos saudáveis desde cedo. 
Além dos benefícios físicos, promove valores como disciplina, cooperação e respeito.

Programas escolares bem estruturados são essenciais para identificar talentos e garantir uma base sólida para futuras gerações de atletas. 
A articulação entre professores, pais e clubes é essencial.') ;

-- Seed de notícias
INSERT INTO noticias (titulo, imagem, texto)
VALUES
('Portugal vence a Liga das Nações', 'img_1.jpg', 
'A seleção nacional conquistou novamente a Liga das Nações após uma final intensa frente à França. 
Com golos de Bruno Fernandes e Bernardo Silva, a equipa das Quinas dominou o encontro com grande maturidade tática.

Este título reforça o bom momento do futebol português a nível internacional, destacando o trabalho da equipa técnica e a qualidade dos jogadores convocados. 
A festa estendeu-se pelas ruas de Lisboa e do Porto, com milhares de adeptos a celebrar.'),

('Maratona de Lisboa com recorde de participantes', 'img_2.jpg',
'A edição deste ano da Maratona de Lisboa contou com mais de 30.000 corredores de todo o mundo. 
O evento decorreu com excelentes condições meteorológicas, e o vencedor da prova masculina terminou com um tempo recorde.

O percurso junto ao Tejo continua a ser um dos preferidos a nível internacional. 
A organização destacou o impacto turístico e económico da prova na cidade.'),

('Surfista português brilha no Havai', 'img_3.jpg',
'João Guedes conquistou o segundo lugar numa das mais prestigiadas provas de surf no Havai. 
As ondas gigantes foram um verdadeiro desafio, mas o atleta mostrou uma técnica apurada e coragem fora do comum.

Este resultado marca um ponto alto na carreira do jovem surfista. 
A imprensa internacional elogiou a sua performance e destacou Portugal como uma potência em crescimento no surf.'),

('Atleta paralímpico bate novo recorde europeu', 'img_4.jpg',
'Carlos Lopes, atleta paralímpico português, bateu o recorde europeu nos 100 metros livres. 
O feito aconteceu no campeonato europeu de atletismo adaptado, em Berlim.

Com este resultado, Carlos reforça o seu estatuto como um dos melhores velocistas do continente. 
A Federação Portuguesa de Desporto para Pessoas com Deficiência manifestou orgulho e prometeu mais apoios.'),

('Novos equipamentos no Estádio Nacional', 'img_5.jpg',
'O Estádio Nacional foi alvo de uma renovação profunda, com destaque para os novos relvados híbridos e sistemas de iluminação LED. 
A medida visa modernizar as condições para eventos desportivos internacionais.

Além disso, foram criadas zonas de acesso inclusivo e uma nova pista de atletismo. 
As obras fazem parte de um plano de requalificação a cinco anos.'),

('Equipa feminina de andebol vence campeonato nacional', 'img_6.jpg',
'O SL Benfica sagrou-se campeão nacional de andebol feminino após uma época irrepreensível. 
A equipa demonstrou uma consistência impressionante, com destaque para a guarda-redes Ana Ribeiro.

A final foi marcada por grande emoção e fair-play. 
Os adeptos fizeram-se ouvir nas bancadas e encheram o pavilhão de entusiasmo.'),

('Jogador português assina por clube inglês', 'img_7.jpg',
'O médio português Diogo Teixeira assinou contrato de 4 anos com o Everton. 
O jogador mostrou-se entusiasmado com o desafio de competir na Premier League.

Com passagens pelo FC Porto e Sporting, Diogo é visto como uma das promessas do futebol luso. 
O clube inglês destaca a sua técnica e visão de jogo.'),

('Portugal organiza Campeonato Mundial de Judo', 'img_8.jpg',
'Lisboa será palco do Campeonato Mundial de Judo em 2026. 
A decisão foi anunciada pela Federação Internacional e conta com o apoio do governo português.

Este evento trará centenas de atletas e milhares de visitantes ao país. 
Espera-se um forte impacto mediático e turístico.'),

('Desporto universitário ganha novo impulso', 'img_9.jpg',
'O Instituto Politécnico de Leiria lançou um novo programa de bolsas para atletas universitários. 
O objetivo é incentivar a prática desportiva durante os estudos.

As bolsas cobrem propinas, alimentação e alojamento. 
Este modelo poderá ser replicado por outras instituições.'),

('Jovem promessa do ténis conquista torneio sub-18', 'img_10.jpg',
'Beatriz Nunes, de apenas 17 anos, venceu o torneio internacional sub-18 em Barcelona. 
A tenista portuguesa superou adversárias de renome e mostrou um jogo maduro e confiante.

Os técnicos nacionais já acompanham a sua evolução há anos. 
Com este título, Beatriz reforça a sua posição no ranking mundial júnior.');