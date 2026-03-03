<?php
session_start();

// ログインチェック
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}

/* ★ セッション開始時刻（初回のみ） */
if (!isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = time();
}

/* ★ 最大ログイン時間（4時間） */
define('MAX_LOGIN_TIME', 4 * 60 * 60);


/* ★ 時間超過で強制ログアウト */
if (time() - $_SESSION['login_time'] > MAX_LOGIN_TIME) {
    header("Location: logout.php");
    exit();
}

// PDFアクセス許可をセッションに設定
$_SESSION['allow_pdf_access'] = true;

?>
<script>
  // 30分ごとに強制リロード（ミリ秒）
  setTimeout(() => {
    location.reload();
  }, 30 * 60 * 1000);
</script>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ブラウザの初期cssのリセット -->
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css">
    <!-- cssを充てる -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- <link rel= "stylesheet" type= "text/css" href= "{{ url_for('static',filename='style.css') }}"> -->
    <!-- 検索した際の説明分 -->
    <meta name="description" content="Get3000">
    <link href="https://fonts.googleapis.com/css?family=Sawarabi+Mincho" rel="stylesheet">
    <!-- タブの名前 -->
    <title>Growth engine & Growth driver</title>
</head>
<body>
<div class="header-topbar">
  <div class="welcome">
    Welcome to the new page,
    <?php echo htmlspecialchars($_SESSION['username']); ?>
  </div>

  <div class="user-menu">
    <a href="password_change_2.php">パスワード変更</a>
    <span>|</span>
    <a href="./logout.php">ログアウト</a>
  </div>
</div>
    <header>
        <h1 class="site-title">
            <a>Growth engine & Growth driver</a>
        </h1>
        <nav>
            <ul>
                <li><a href="#tenken">contact</a></li>
                <li><a href="#works">works</a></li>
                <li><a href="#other">other</a></li>
                <!-- <li><a href="#contact">Contact</a></li> -->
                <!-- 追跡されないようにtagetとrelを入れておく -->
                <li><a
                    href="https://www.hondacars-kitaosaka.com/" 
                    target="_blank"
                    rel="noopener noreferrer"
                    ><img src="./assets/img/hko.png" alt="hko" class="icon1">
                    </a>
                </li>
                <li><a 
                    href="https://www.honda.co.jp/" 
                    target="_blank"
                    rel="noopener noreferrer"
                    ><img src="./assets/img/H.png" alt="honda" class="icon2">
                    </a>
                </li>
                <li><a
                    href="https://xp014804td.atledcloud.jp/xpoint/login.jsp" 
                    target="_blank"
                    rel="noopener noreferrer"
                    ><img src="./assets/img/xp.png" alt="X-point" class="icon3">
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <!-- Main -->
    <div id="main">
        <!-- 画像をレスポンシブに対応させる -->
        <picture>
            <img src="./assets/img/main3.jpg" alt=""/>
        </picture>
    </div>
    <!-- about -->
    <section id="about" class="wrapper">
        <h2 class="sec-title">About</h2>
        <ul>
            <li>＜ホンダ北大阪スタンダード＞</li>

            <li>お客様の視点に立って行動する！</li>
            <li>社会の変化にスピーディーに対応する！</li>
            <li>法令を遵守する！</li>
        </ul>
    <div>    
        <p class="sec-sub">
            <span class="label">＜新体制スローガン＞</span>
            <span class="title">成長エンジン＆成長ドライバー</span>
        </p>
        <!-- <p>新車販売3,000台を達成し、大阪を代表するディーラーになる!!</p> -->
    </div>
    </section>
    <section id="SPIKA" class="wrapper">
    <H2 class="sec-title">SPIKA</H2>
        <ul>
            <li>
                <a href="view_pdf.php?file=works/SPIKA202602.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/cup4.jpg" alt="works1">
                        <p>SPIKA/NPS分析(12-2月)</p>
                </a>
            </li>  
            <li>
                <a href="./comment3.php" target="_blank" class="image-container">
                    <img src="./assets/img/cup5.jpg" alt="works1">
                        <p>ユーザーコメント/2月分</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/SPIKA_second_half.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/cup1.jpg" alt="works1">
                        <p>SPIKA2024下期分析</p>
                </a>
            </li>   
        </ul>
    </section>
    <section id="videos" class="wrapper">
        <H2 class="sec-title">videos</H2>
            <div class="archive">
                <a href="videos.php">
                    <img src="./assets/img/tops2024.gif" alt="archive">
                </a>
                <a href="videos_rakumaru.php">
                    <img src="./assets/img/rakumaru.gif" alt="archive">
                </a>
                <!-- <a href="videos4.php">
                    <img src="./assets/img/inui.png" alt="archive">
                </a> -->
                <a href="videos4.php">
                    <video autoplay loop muted playsinline poster="./assets/img/inui.png">
                        <source src="./assets/video/inui_Top.mp4" type="video/mp4">
                    </video>
                </a>
                <a href="videos2.php">
                    <img src="./assets/img/chuko_202508.jpg" alt="archive">
                </a>
                <!-- <a href="videos2.php">
                    <img src="./assets/img/jikotenken2025.png" alt="archive">
                </a> -->
                <!-- <a href="https://t-tube.stream.co.jp/?key=12e358e9f27f5c9295df5ed7ba33fa84" target="_blank" rel="noopener noreferrer">
                    <img src="./assets/img/jikotenken2025.png" alt="動画を見る">
                </a> -->
                <!-- <a href="videos3.php">
                    <img src="./assets/img/jikotenken2025.png" alt="archive">
                </a> -->
            </div>
    </section>

         <!-- tenken -->
    <section id="tenken" class="wrapper">
        <H2 class="sec-title">contact</H2>
            <div class="archive">
                <!-- <a href="./public/6m_search.php" class="banner">
                    <img src="assets/img/3kan.png" alt="3管率 パラパラ検索">
                </a> -->
                <a href="./public/shaken_search.php" class="banner">
                    <img src="assets/img/tenken.png" alt="点検対象 パラパラ検索">
                </a>
            </div>
    </section>

    <!-- works -->
    <section id="works" class="wrapper">
        <!-- <H2 class="sec-title">works</H2> -->
        <H2 class="sec-title">works</H2>
        <ul>
            <li>
                <a href="view_pdf.php?file=works/sokuho_2025.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/works1.jpg" alt="works1">
                        <p>受注登録売上速報</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/basic_1.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/pc1.jpg" alt="works1">
                        <p>L-MAX/基本活動</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/hoken.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/works3.jpg" alt="works1">
                        <p>保険獲得&継続率</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/attack_6m.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/iPhone2.jpg" alt="works1">
                        <p>アタックリスト状況</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/shinki.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/cycle1.jpg" alt="works1">
                        <p>新規フォロー状況</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/ei_zangyo.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/suisho3.jpg" alt="works1">
                        <p>営業S残業時間</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/service_rieki.pdf" target="_blank" class="image-container">    
                    <img src="./assets/img/works4.jpg" alt="works1">
                        <p>サービス実績表</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/jishiritu2.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/works5.jpg" alt="works1">
                        <p>車点検実施率[予測]</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/jishiritu.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/pc9.jpg" alt="works1">
                        <p>車点検実施率[実績]</p>
                </a>
            </li>    
            <li>
                <a href="view_pdf.php?file=works/chao_mijishi.pdf" target="_blank" class="image-container">    
                    <img src="./assets/img/works6.jpg" alt="works1">
                        <p>まかせチャオ消化率</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/chao_yoyaku.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/iPhone1.jpg" alt="works1">
                        <p>サービス翌月予約確認</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/SE_zangyo.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/suisho2.jpg" alt="works1">
                        <p>SE残業時間</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/SE_result.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/works2.jpg" alt="works1">
                        <p>SE別実績表</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/service_add.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/pc5.jpg" alt="works1">
                        <p>サービス付加価値</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=works/revenue.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/camera1.jpg" alt="works1">
                        <p>収益速報</p>
                </a>
            </li>
            <!-- <li>
                <a href="view_pdf.php?file=suit.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/suit.jpg" alt="works1">
                        <p>営業/受注キャンペーン</p>
                </a>
            </li> -->
        </ul>
    </section>
    <section id="other" class="wrapper">
    <H2 class="sec-title">other</H2>
    <div class="archive">
        <a href="view_pdf.php?file=works/keikakusho2025.pdf">
            <img src="./assets/img/keikaku_bana.png" alt="archive">
        </a>
        <!-- <a href="view_pdf.php?file=ranking2025.pdf">
            <img src="./assets/img/ranking_kojin2025.png" alt="archive">
        </a> -->
        <a href="view_pdf.php?file=works/ranking2025.pdf">
            <video autoplay loop muted playsinline poster="./assets/img/ranking_kojin2025.png">
                <source src="./assets/video/ranking.mp4" type="video/mp4">
            </video>
        </a>
        <a href="view_pdf.php?file=works/graph2025.pdf">
            <img src="./assets/img/graph2025.png" alt="archive">
        </a>
        <a href="view_pdf.php?file=works/新車納車説明スタンダード.pdf">
            <img src="./assets/img/nosha.png" alt="archive">
        </a> 
        <!-- <a href="view_pdf.php?file=keiriTips_05.pdf">
            <img src="./assets/img/keiri_tushin.jpg" alt="archive">
        </a>  -->
        <a href="keiri_tips.php">
            <video autoplay loop muted playsinline poster="./assets/img/keiri_tushin.jpg">
                <source src="./assets/video/keiri_Tips.mp4" type="video/mp4">
            </video>
        </a>
    </div>
    </section>
    <!-- news -->
    <section id="news" class="wrapper">
        <h2 class="sec-title">news</h2>
        <dl>
            <dt>2024/10/04</dt>
            <dd>本サイトをリリースしました。</dd>
            <dt>2024/10/01</dt>
            <dd>PythonとChatGPTで作成しています。</dd>
            <dt>2024/10/01</dt>
            <dd>システムの自動化を進めています。</dd>
            <dt>2026/02/20</dt>
            <dd>点検パラパラをリリースしました。</dd>
            <dt>2026/02/20</dt>
            <dd>ログイン方法を変更しました。</dd>
        </dl>
    </section>
    <!-- <section id="SPIKA" class="wrapper">
    <H2 class="sec-title">SPIKA</H2>
        <ul>
            <li>
                <a href="view_pdf.php?file=SPIKA2024_1.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/cup1.jpg" alt="works1">
                        <p>SPIKA上期分析</p>
                </a>
            </li>
            <li>
                <a href="view_pdf.php?file=SPIKA_second_half.pdf" target="_blank" class="image-container">
                    <img src="./assets/img/cup4.jpg" alt="works1">
                        <p>SPIKA下期分析(10-11月)</p>
                </a>
            </li>  
            <li>
                <a href="./comment.php" target="_blank" class="image-container">
                    <img src="./assets/img/cup5.jpg" alt="works1">
                        <p>SPIKAコメント(11月)</p>
                </a>
            </li>   
        </ul>
    </section> -->
    <footer>
        <div class="container">
            <small>&copy;honda-kitaosaka</small>
        </div>
    </footer>
</body>
</html>
