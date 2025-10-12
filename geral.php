<?php

// --- geral.php ---

// Recebe ?page=suplica | chamada | litania | painosso | credo

$page = $_GET['page'] ?? 'suplica';

/**

 * Converte o texto da liturgia (com sintaxe Markdown simplificada) para HTML estruturado.

 * * Marcadores:

 * - **texto em negrito** => <strong>texto em negrito</strong>

 * - Ministro: / Usongi: => Inicia um bloco <div class="minister">

 * - ** Congregação: ** / ** Ekongelo: ** => Inicia um bloco <div class="congregation">

 * * @param string $text O texto original do banco de dados.

 * @param string $lang O idioma ('pt' ou 'um') para definir os nomes dos papéis.

 * @return string O HTML formatado.

 */

function convert_liturgy_markdown($text, $lang)
{

    // 1. Remove espaços e novas linhas iniciais/finais

    $text = trim($text);

    // 2. Processa **Bold** text (substitui **...** por <strong>...</strong>)

    // O modificador 's' (dotall) garante que o ponto '.' case novas linhas, se houver.

    $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);



    // 3. Define os nomes dos papéis com base no idioma

    if ($lang == 'pt') {

        $minister_role = 'Ministro:';

        $congregation_role = 'Congregação:';
    } else {

        $minister_role = 'Usongi:';

        $congregation_role = 'Ekongelo:';
    }



    // 4. Marca as transições de blocos (Abre a nova div e fecha a anterior)

    // Substitui a sequência de nova linha e marcador de Congregação (com ###)

    // Opcionalmente, pode-se incluir ' ** ' ou ' ### ' para suportar a sintaxe dos novos dados.

    $text = str_replace(

        ["\n\n ### " . $congregation_role, "\n\n ** " . $congregation_role . " **"],

        ['</div><div class="congregation"><strong>' . $congregation_role . '</strong> '],

        $text

    );



    // Substitui a sequência de nova linha e marcador de Ministro (para blocos subsequentes)

    $text = str_replace(

        ["\n\n " . $minister_role],

        ['</div><div class="minister"><strong>' . $minister_role . '</strong> '],

        $text

    );

    // 5. Inicia o primeiro bloco

    // Verifica se o texto começa com o nome do papel do Ministro. Se não, assume-se que é um texto contínuo do Ministro.

    if (strpos($text, $minister_role) === 0) {

        $text = '<div class="minister"><strong>' . $minister_role . '</strong> ' . substr($text, strlen($minister_role));
    } else if (strpos($text, $congregation_role) === 0) {

        // Se começar com a congregação (teoricamente só ocorreria se o dado JSON estivesse mal formatado), ajusta para congregação.

        $text = '<div class="congregation"><strong>' . $congregation_role . '</strong> ' . substr($text, strlen($congregation_role));
    } else {

        // Para textos como "Pai Nosso" ou "Credo" que não começam com um papel, usamos uma div de ministro padrão

        $text = '<div class="minister">' . $text;
    }



    // 6. Finaliza

    // Adiciona quebras de linha (<br />) para as novas linhas restantes dentro dos blocos.

    $text = nl2br($text);



    // Fecha a última div aberta

    $text .= '</div>';



    // O passo 5 introduz uma div de abertura (e um strong) no início. 

    // Se a primeira linha tiver uma nova linha seguida do nome do papel (como em alguns JSONs), 

    // a substituição do passo 4 pode ter deixado uma div de fechamento indesejada no início.

    // Vamos garantir que a string comece com a abertura do bloco.

    if (strpos($text, '</div>') === 0) {

        $text = substr($text, 6);
    }



    return $text;
}

// Estrutura de dados litúrgicos (JSON embutido)

$geral = [

    // --- SÚPLICA GERAL (suplica.json) ---

    "suplica" => [

        "titulo_pt" => "Súplica Geral",

        "titulo_um" => "Mbuete ngacosse",

        "texto_pt" => "Ministro: Buscai ao Senhor enquanto se pode achar. Invocai-O enquanto está perto. Deixe o ímpio o seu caminho, e o homem maligno os seus pensamentos, e se converta ao Senhor, que se compadecerá dele. Torne para o nosso Deus, porque grandioso é em perdoar.\n\n ** Congregação: ** Porque o Senhor é bom e eterna a sua misericórdia. A Sua verdade estende-se de geração a geração.\n\nMinistro: Ainda que os vossos pecados sejam como a escarlata, eles se tornarão brancos como a neve; ainda que sejam vermelhos como o carmezim, se tornarão como a branca lã.\n\n ** Congregação: ** Se dissermos que não temos pecado, enganamo-nos a nós mesmos e não há verdade em nós. Se confessarmos os nossos pecados, Ele é fiel e justo para nos perdoar os pecados e para nos purificar de toda a injustiça.\n\nMinistro: O que encobre as suas transgressões não prosperará, mas o que as confessa e as deixa alcançará misericórdia. Confessemos, pois, os nossos pecados, porque bem-aventurado é aquele cuja transgressão é perdoada, e cujo pecado é coberto.\n\n ** Congregação: ** Confessei-te o meu pecado, e a minha maldade não encobri. Dizia eu, confessarei ao Senhor as minhas transgressões, e Tu perdoaste a maldade do meu pecado.\n\nMinistro: Eu sei os pensamentos que penso de vós, diz o Senhor: pensamentos de paz e não de mal, para vos dar o fim que esperais. Então me invocareis e buscar-me-eis, e me achareis quando me buscardes de todo o vosso coração. Esquadrinhemos, pois, os nossos caminhos, experimentêmo-los, e volvamos para o Senhor. Inclina os nossos corações aos céus.\n\n ** Congregação: ** Tem misericórdia de mim, ó Deus, segundo a tua benignidade: apaga as minhas transgressões, segundo a multidão das tuas misericórdias. Cria em mim, ó Deus, um coração puro, e renova em mim um espírito recto. Guia-me pelo caminho eterno.\n\nMinistro: Àquele que não conheceu pecado, o fez pecado por nós, para que nele fôssemos feitos justiça de Deus. Assim também vós considerai-vos como mortos para o pecado; mas vivos para Deus em Cristo Jesus, nosso Senhor.\n\n ** Congregação: ** Nele temos a redenção pelo seu sangue, a remissão das ofensas, segundo as riquezas da sua graça.\n\nMinistro: GRAÇAS A DEUS PELO SEU DOM INEFÁVEL!",

        "texto_um" => "Usongi: Sandi Yehova osimbu a sangiwa; u vili kiyi osimbu a kasi ocipepi. Ukuakandu a sie onjila yaye, lu wa sia esunga a litepe lovisimilo viaye, ha tiuka ku Yehova, kuenje eye u linga ohenda, a tiuke muele ku Suku yetu, kuenje u muisa ongecelo ya lua.\n\n ###  Ekongelo: Momo Yehova ukuahenda, kuenje ohenda yaye ka yi pui. Epandi liaye li enda ño hu toke kovitumbulukila viosi.\n\nUsongi: Ndaño akandu ene a kusuka nge, a yela ndocikokoto; ndaño a soka ndonanga yohusu, a yela ndutele. Nda tu endaila vutanya ndeci eye a kasi vutanya, osonde ya Yesu omõlaye yi tu yelisa kakandu osi.\n\n ###  Ekongelo: Nda tu linga tuti, Akandu ka tu kuete, tu lilimbika, kuenda ocili vitima vietu ka cimo. Puãi nda tu litavela ovakandu etu, eye ukuacili, haiye ukuesunga, o tu ecela akandu etu; o tu upa aviho osi.\n\nUsongi: U wa limbika akandu aye ka tela oku sumuluha. U puãi, o litavela, ha tindukako, eye o sanga ohenda. Oco tu litaveli ovakandu etu, momo u oku lueya kuaye kua ecelua wa sumu luha.\n\n ###  Ekongelo: Nda litavela kokuove ekandu liange; ongole yange sia limbikile. Nda linga, siti, Ndi litavela ku Yehova oku lueya kuange kuenje wa njupa eviho liekandu liange.\n\nUsongi: Ca popia Yehova citi, Nda kuliha ovisi milo ndo simili, okuti viombembua, vieyambulo havioko, kuenje eci wu vilikiyi kokuange ko kambi oku sanga nda wu sandiliyi lutima wene wosi. Oco tu konomuisi olonjila vietu, loku vi seteka, ha tu tiuka ku Yehova. Itima vietu vi imhe ovaso ku Yehova ukuailu.\n\n ###  Ekongelo: Ndinge ohenda, a Suku, omo o kasi locisola. Omo liohenda yove yalua, imula oku lueya kuange. SovuiIe mima u yela, a Suku. Tumbulula vokati kange espiritu liesunga, songuile vonjila ka yi pui.\n\nUsongi: Una ka kulihile ekandu, Suku wo lingi sa ndekandu mekonda lietu, ha tu lingi vakue sunga lia Suku mekonda liaye. Oco ene sokololi okuti kakandu wa fi, puãi wa muili omuenyo ku Suku mekonda lia Yesu Kristu.\n\n ###  Ekongelo: Kokuahe tu kuete eyovo, olio ongecelo yakandu, ndukuasi wa lua wocali caye.\n\nUsongi: Pandu, pandu ku Suku me konda liocali caye ca lua.",

    ],

    // --- CHAMADA PARA ADORAÇÃO (chamada.json) ---

    "chamada" => [

        "titulo_pt" => "CHAMADA PARA ADORAÇÃO",

        "titulo_um" => "ONOHA YATETE",

        "texto_pt" => "Ministro: O Senhor está no Seu santo templo: cale-se diante dele toda a terra.\n\n** Congregação: ** Escutarei o que Deus, o Senhor disser; porque falará de paz ao Seu povo.\n\n Ministro: Deus é o nosso, refúgio e fortaleza, socorro bem presente na angústia.\n\n** Congregação: ** Pelo que não temeremos, ainda que a terra se mude, e ainda que os montes se transportem para o meio dos mares.\n\n Ministro: Esperai no Senhor, porque no Senhor há misericórdia, e nele há abundante redenção.\n\n** Congregação: ** Aguardo ao Senhor: a minha alma o aguarda, e espero na Sua palavra.\n\n Ministro: Engrandecei ao Senhor comigo, e juntos exaltemos o Seu nome.\n\n** Congregação: ** Porque grande é o Senhor e digno de louvor.\n\n Ministro: Misericordioso e piedoso é o Senhor; longânimo e grande em benignidade.\n\n** Congregação: ** Não nos tratou segundo os nossos pecados; nem nos retribuiu segundo as nossas iniquidades.\n\n Ministro: Quanto o céu. está elevado acima da terra, assim é grande a Sua misericórdia para com os que o temem.\n\n** Congregação: ** Quanto está longe o oriente do ocidente, assim afasta de nós as nossas transgressões.\n\n Ministro: Confia no Senhor de todo o teu coração, e não te estribes no teu próprio entendimento.\n\n** Congregação: ** Sonda-me ó Deus, e conhece o meu coração; prova-me, e conhece os meus pensamentos, e vê se há em mim algum caminho mau, e guia-me pelo caminho eterno.",

        "texto_um" => "Usongi: Suku o kasi vonembele yaye yi kola.Omanu vosi vohile kovaso aye.\n\n** Ekongelo: ** Njeva eci ci popia Suku Yehova, momo o popia ombembua lomanu vaye.\n\n Usongi: Suku, eye Ocikolo cetu,haye ukuaku kolisa. Eye Oñuatisi; kohali o moleha lombili.\n\n** Ekongelo: ** Oco ka tu yokoka, ndaño oluali lu pongoloka, ndaño olomunda vi tutumuhila pokati kokalunga.\n\n Usongi: Imbi ovaso ku Yehova; momo ku Yehova ku kuete ohenda; kokuaye ku li eyovo lia lua.\n\n** Ekongelo: ** Njimba ovaso ku Yehova; utima wange u wimbila ovaso; ndavoka kondaka yaye.\n\n Usongi: : Kemainyi Yehova kumuamue lame; tu pandiyili pamosi onduko yaye.\n\n** Ekongelo: ** Momo Yehova unene, kuenje wa posokela esivayo lia piãla.\n\n Usongi: Yehova ukuahenda, o kuete ocali. O livala konyeño. Ocisola caye calua cimue.\n\n** Ekongelo: ** Ka lingile letu eci ca soka lovakandu etu. Ca sesamela olohole vietu, hacoko a tu muisa.\n\n Usongi: Ndeci kilu kua lepa loposi, haico ohenda yaye ya lua kuava vo sumbila.\n\n** Ekongelo: ** Ndeci kutundilo kupala lutakelo, haico oku lueya kuetu a ku kapa ocipala letu.\n\n Usongi: Kolela Suku lutima wove wosi. Ku ka lipandiye lolondunge viove muele.\n\n** Ekongelo: ** Ñulihise, a Suku; kuliha utima wange. Seteke; kuliha ovisimilo viange, ku vanji nda ñasi lovituwa vieviho. Songuile vonjila ka yi pui.\n\n Usongi: Tali ocisola ca piãla ndomo Tate a tu ciha, okuti tua tukuiwa omãla va Suku.\n\n** Ekongelo: ** Pandu, pandu ku Suku mekonda lio cali cahe ca lua.",

    ],

    // --- LITANIA DE ACÇÃO DE GRAÇAS (litania.json) ---

    "litania" => [

        "titulo_pt" => "LITANIA DE ACÇÃO DE GRAÇAS",

        "titulo_um" => "ONOHA YATATU",

        "texto_pt" => "

Agradeçamos a Deus por se revelar a nós, através de muitas coisas que Ele falou e fez; sobretudo por ter falado para nós através do Seu Filho amado Jesus Cristo, nosso Salvador e Senhor; O qual morreu por nós na cruz e ressuscitou, tornando-se assim em nosso caminho, nossa justiça e nossa Vida.

** Congregação: ** GRAÇAS A DEUS PELO SEU DOM INEFÁVEL!

Agradeçamos a Deus porque temos uma grande e fiel Testemunha em nossos corações que é o Espirito Santo, o qual está completando em nós o acto da salvação já recebida; e nos concede sabedoria, força e alegria, para testemunharmos de Jesus.

** Congregação: ** GRAÇAS A DEUS PELO SEU DOM INEFÁVEL!

Agradeçamos a Deus porque temos as suas Sagradas Escrituras que são a vela para os nossos pés e orientação segura para as questões da salvação e de vida eterna. Agradeçamos também por ter nos enviado homens fiéis a Ele, que nos transmitiram o Evangelho, a Palavra da vida eterna, capaz de nos conduzir à completa e eterna salvação.

** Congregação: ** GRAÇAS A DEUS PELO SEU DOM INEFÁVEL!

Agradeçamos a Deus porque temos a esperança da segunda vinda do Senhor Jesus, tal como Ele mesmo prometeu quando disse: voltarei para buscar-vos! Agradeçamos a Deus porque o Seu eterno e inabalável reino está chegando.

** Congregação: ** GRAÇAS A DEUS PELO SEU DOM INEFÁVEL!

Agradeçamos a Deus porque sempre houve aqueles consagraram verdadeira e completamente à sua causa do Evangelho. Cuidaram e defenderam tenazmente a sua Fé, esperando o regresso do Senhor Jesus; pelo que a chama que eles acenderam, ilumina-nos até hoje. Agradeçamos a Deus porque também nós fomos chamados por Ele, para sermos a luz deste mundo, através do nosso viver; mostrando assim a palavra da Vida, até que o Senhor Jesus volte.

** Congregação: ** GRAÇAS A DEUS PELO SEU DOM INEFÁVEL!

Agradeçamos o poder do Espirito Santo que estava com os cristãos da Igreja primitiva. Pelo tal poder, eles revelaram muita coragem e perseverança, na proclamação do Evangelho do reino de Deus. Eles aceitaram ser verdadeiras e leais testemunhas de Jesus Cristo, a ponto de enfrentarem todo tipo de sofrimento. Agradeçamos o poder do Espirito Santo que está também conosco no trabalho da pregação do Evangelho da salvação, a toda criatura.

** Congregação: ** GRAÇAS A DEUS PELO SEU DOM INEFÁVEL!

",

        "texto_um" => "

Tu panduli Suku momo a lisitulula kokutu pokati kovina vialua a popia loku linga, puãi cinene omo a popia letu lomolaye Yesu Kristu, eye Onjovoli yetu, haiye Ñala yetu wa tu fila kekulusu, wa tu pindukila kuenje wa tu lingila onjila lesunga lomuenyo.

** Ekongelo: ** Pandu, pandu ku Suku mekonda liocali caye ka ci popaiwa.

Tu panduli Suku omo tu kasi lombangi yinene vovitima vietu, okuti Espiritu Sandu, lomo eye a kasi oku malusula vovitima vietu upange weyovo, lama a tu muisa olondun- ge kuenda ongusu lesanju lioku imbila Ñala Yesu uvangi.

** Ekongelo: ** Pandu, pandu ku Suku mekonda liocali caye ka ci popaiwa,

Tu panduli Suku omo tu kasi lovisonehua viaye vi kola via linga ovela yolomai vietu, haivio vi tela oku tu imba onumbi yovina vieyovo levi vionuenyo ko pui. Tu panduilivo ndomo a tu katuila ava va tu situluila ondaka yayeyi kola loku tu ilikila eyovo liaye liocili.

** Ekongelo: ** Pandu, pandu ku Suku mekonda liocali caye ka ci popaiwa,

Tu panduli Suku, omo tu kuete elavoko lietukuluko lia Ñala Yesu, ndohuminyoyaye, eci a popia hati, Njiya vali ndu wupilili. Tu panduli omo usoma wa Kristu wiya una ka u sengiwa, wenda nõ hu.

** Ekongelo: ** Pandu, pandu ku Suku mekonda liocali caye ka ci popaiwa,

Tu panduli Suku omo olotembo viosi kua kala ava va tiamela ocili kondaka yaye, va tata olondiyelo viavo, va lavoka Cime cavo kuenje ocinyi va miha ci tu tuila tuila toke etali. Tu pandulivo okuti letuvo Suku wa tu kovongela oku linga imihi violuali ha tu molehisa ondaka yomuenyo toke Ñala eya.

** Ekongelo: ** Pandu, pandu ku Suku mekonda liocali caye ka ci popaiwa,

Tu panduila unene Espiritu Sandu wa kala lolondonge viatete. Kunene waco va kala lepandi lutoi woku yevalisa ondaka yusoma wa Suku. Va tava lesanju oku linga olonbangi viaye ndaño lolohali. Tu panduili unene Espiritu Sandu u kasivo letu kocikele cetu coku yevalisa kuosi ondaka ya Yesu, hayo yeyovo

** Ekongelo: ** Pandu, pandu ku Suku mekonda liocali caye ka ci popaiwa,

"

    ],



    // --- PAI NOSSO (painosso.json) ---

    "painosso" => [

        "titulo_pt" => "PAI NOSSO",

        "titulo_um" => "TATE WETU",

        "texto_pt" => "Pai nosso que estais nos céus,\nSantificado seja o vosso nome;\nVenha a nós o vosso reino;\nSeja feita a vossa vontade,\nAssim na terra como no céu.\n\nO pão nosso de cada dia nos dai hoje;\nPerdoai-nos as nossas ofensas,\nAssim como nós perdoamos a quem nos tem ofendido;\nE não nos deixeis cair em tentação,\nMas livrai-nos do mal.\n\nAmém.",

        "texto_um" => "Tate wetu owa li kunene,\nUli mucima okuvelela lina lyove;\nUli mucima okuza ombuso yove;\nOkusokoloka okuvelela ohandi yove,\nPalunda pamwe na mu ulimo.\n\nOmbuto yetu ya litwe liñi, utupelela lelo;\nUtusokolole vihululu vyetu,\nPamwe tu sokolola avala vatuhulule;\nKatuheleni okukwata muvi kovihanda,\nNokutulombolola kovihululu vyosi.\n\nAmen."

    ],



    // --- CREDO DOS APÓSTOLOS (credo.json) ---

    "credo" => [

        "titulo_pt" => "CREDO DOS APOSTOLOS",

        "titulo_um" => "ONOHA YA VAMATETI",

        "texto_pt" => "Creio em Deus Pai todo-poderoso,\nCriador do céu e da terra;\nE em Jesus Cristo, seu único Filho, nosso Senhor;\nO qual foi concebido pelo poder do Espírito Santo;\nNasceu da Virgem Maria;\nPadeceu sob o poder de Pôncio Pilatos;\nFoi crucificado, morto e sepultado;\nNo terceiro dia ressurgiu dos mortos;\nSubiu aos céus;\nEstá sentado à mão direita de Deus Pai todo-poderoso,\nDonde há de vir a julgar os vivos e os mortos.\n\nCreio no Espírito Santo;\nNa Santa Igreja Católica;\nNa comunhão dos santos;\nNa remissão dos pecados;\nNa ressurreição do corpo;\nNa vida eterna.\n\nAmém.",

        "texto_um" => "Ndafekela Omunu waKalunga, Tatekulu okuti olomboloka vyosi vyose;\nOlomboloka vyosi vyose;\nNdafekela Yesu Kristu, Omunu waKalunga umwe, Mwene wetu;\nOkulisoka kovili lyosikwelo ly’Amesu Muku;\nOkusumbwa kovavya Maria;\nOkwatama kovavya Pôncio Pilato;\nOkxivama, okufa nokusimbilwa;\nOkutambula kuji lyatatu kovafwa;\nOkukwata kunene;\nOkwikala koku Kalunga Tatekulu, olomboloka vyosi vyose;\nOkuvua okuti okuza okukandja avala vahidiye nevala vahili.\n\nNdafekela Omesu Muku;\nOmunu wa Kanisa Katolika;\nOkupandula kovasande;\nOkusokoloka kovihululu;\nOkutambula kovimati;\nOnolundo lyosi.\n\nAmen."

    ]

];

// Dados escolhidos

$data = $geral[$page] ?? ["titulo_pt" => "Não encontrado", "titulo_um" => "Katuvali", "texto_pt" => "", "texto_um" => ""];

// --- HTML + Interface ---

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['titulo_pt']) ?> - Tab Layout</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            line-height: 1.6;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: center;
            background-color: #ff6600;
            color: white;
            padding: 10px 15px;
            font-size: 1.2em;
        }

        .header a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            font-size: 22px;
        }

        .tab-menu {
            display: flex;
            border-bottom: 2px solid #ccc;
            background-color: white;
        }

        .tab-button {
            flex-grow: 1;
            text-align: center;
            padding: 10px 0;
            cursor: pointer;
            font-weight: bold;
            color: #777;
            transition: color 0.3s;
            position: relative;
        }

        .tab-button.active {
            color: #ff6600;
        }

        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #ff6600;
        }

        .tab-content {
            padding: 20px;
        }

        .content-pane {
            display: none;
        }

        .content-pane.active {
            display: block;
        }

        .minister,
        .congregation {
            margin-bottom: 1.5em;
        }

        .minister strong,
        .congregation strong {
            font-weight: bold;
            display: inline;
        }

        /* Botão flutuante do chat */
        #chat-float-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            background: #ff5722;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            cursor: pointer;
            transition: background 0.2s;
        }

        #chat-float-btn:hover {
            background: #e64a19;
        }
    </style>
</head>

<body>
    <div class="header">
        <a href="home" title="Voltar para a página inicial"><i class="fas fa-arrow-left"></i>
        </a>
        <span><?= htmlspecialchars($data['titulo_pt']) ?></span>
    </div>
    <div class="tab-menu">
        <div class="tab-button active" data-tab="portugues">Português</div>
        <div class="tab-button" data-tab="umbundu">Umbundu</div>
    </div>
    <div class="tab-content">
        <div id="portugues" class="content-pane active">
            <?= convert_liturgy_markdown($data['texto_pt'], 'pt') ?>
        </div>
        <div id="umbundu" class="content-pane">
            <?= convert_liturgy_markdown($data['texto_um'], 'um') ?>
        </div>
    </div>
    <a href="/hinario/chat" id="chat-float-btn" title="Falar com a IA">
        <i class="fas fa-comments"></i>
    </a>
    <script>
        // Alterna as abas
        const tabs = document.querySelectorAll(".tab-button");
        const panes = document.querySelectorAll(".content-pane");
        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                const target = tab.getAttribute("data-tab");
                tabs.forEach(t => t.classList.remove("active"));
                tab.classList.add("active");
                panes.forEach(p => p.classList.remove("active"));
                document.getElementById(target).classList.add("active");
            });
        });
    </script>
</body>

</html>