<?php
// ===== 直アクセス防止（他ページ同様）
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}

// セッションが未開始なら開始（他ページで既に start していれば不要）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// アクセスログ記録処理
$log_directory = '/../logs';
$today = date('Y-m-d');
$log_file = $log_directory . "/access_log_{$today}.txt";

if (!file_exists($log_directory)) {
    mkdir($log_directory, 0777, true);
}

$username = $_SESSION['username'] ?? '不明ユーザー';
$access_page = basename($_SERVER['PHP_SELF']);
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$log = date("Y-m-d H:i:s")
    . " | ID: {$username}"
    . " | アクセス: {$access_page}"
    . " | IP: {$ip_address}"
    . " | ブラウザ: {$user_agent}\n";

// file_put_contents($log_file, $log, FILE_APPEND);

file_put_contents($log_file, $log, FILE_APPEND | LOCK_EX);

?>

<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>点検対象 パラパラ検索</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ★CSSパスを assets/css に統一 -->
  <link rel="stylesheet" href="../assets/css/shaken_cards.css">
</head>
<body>

<header class="topbar">
  <div class="topbar__title">点検対象 パラパラ検索</div>
</header>

<main class="container">

  <!-- ▼ 戻るナビ -->
  <div class="nav-top">
    <a href="../index.php" class="btn btn--ghost">← TOPページへ戻る</a>
    <!-- <button type="button" class="btn btn--ghost" onclick="scrollToSearch()">
      ↑ 検索条件へ戻る -->
    </button>
  </div>

  <!-- ▼ 検索条件 -->
  <section class="panel">
    <div class="grid">
      <label class="field">
        <span>拠点</span>
        <select id="kyoten"></select>
      </label>

      <label class="field">
        <span>担当者</span>
        <select id="tanto" disabled></select>
      </label>

      <label class="field">
        <span>対象月</span>
        <select id="month"></select>
      </label>
      
      <label class="field">
        <span>点検項目（作業種別）</span>
        <select id="work"></select>
      </label>
    </div>

    <div class="actions">
      <button id="btnSearch" class="btn btn--primary">検索</button>
      <label class="toggle">
        <input id="showDone" type="checkbox"> 対応済みも表示
      </label>
    </div>

    <div id="msg" class="msg"></div>
  </section>

  <!-- ▼ パラパラ表示 -->
  <section class="viewer">
    <div class="status">
      <div id="statusText" class="statusText">条件を選択してください</div>
      <div id="counter" class="counter"></div>
    </div>

    <div id="card" class="card is-hidden">
      <div id="maru" class="maru is-hidden"></div>

      <div class="cardHeader">
        <div class="label">顧客名</div>
        <div id="customer_name" class="big"></div>
      </div>

      <div class="kv"><div class="k">携帯TEL</div><div class="v"><a id="mobile_tel" class="tel">—</a></div></div>
      <div class="kv"><div class="k">自宅住所</div><div id="address" class="v">—</div></div>

    <div class="kv-grid">
      <!-- ★追加② 対象月 -->
      <div class="kv"><div class="k">対象月</div><div id="target_month" class="v">—</div></div>
      <div class="kv"><div class="k">作業種別</div><div id="work_type" class="v">—</div></div>
      <div class="kv"><div class="k">車名</div><div id="car_name" class="v">—</div></div>

      <!-- ★追加③ 初度登録年月（文字列：2026/07） -->
      <div class="kv"><div class="k">初度登録年月</div><div id="first_reg" class="v">—</div></div>
      <div class="kv"><div class="k">次回車検日</div><div id="next_shaken" class="v">—</div></div>
      <div class="kv"><div class="k">車検回数</div><div id="shaken_count" class="v">—</div></div>

      <div class="kv"><div class="k">現チャオ</div><div id="chao_plan" class="v">—</div></div>
      <div class="kv"><div class="k">チャオ対象</div><div id="chao_target" class="v">—</div></div>
      <div class="kv"><div class="k">残クレ</div><div id="zankure" class="v">—</div></div>

      <!-- 日付は整形表示 -->
      <div class="kv"><div class="k">サービス予約日</div><div id="service_date" class="v">—</div></div>
      <div class="kv market" id="market_box"><div class="k">市場措置</div><div id="market_action" class="v">—</div></div>
    </div>

      <div class="nav">
        <button id="btnPrev" class="btn">◀◀  前へ  ◀◀</button>
        <button id="btnArrived" class="btn btn--ok"> ⭕ 入庫/予約/代替 ⭕</button>
        <button id="btnCancel" class="btn btn--ghost" hidden>予約キャンセル</button>
        <button id="btnNext" class="btn">▶▶  次へ  ▶▶</button>
      </div>

      <div class="hint">iPad：左右スワイプでパラパラめくり</div>
    </div>
  </section>

</main>

<script>
(() => {
  const $ = id => document.getElementById(id);

  const kyoten = $('kyoten');
  const tanto  = $('tanto');
  const month  = $('month');
  const work   = $('work');
  const showDone = $('showDone');

  const btnSearch = $('btnSearch');
  const btnPrev = $('btnPrev');
  const btnNext = $('btnNext');
  const btnArrived = $('btnArrived');
  const btnCancel  = $('btnCancel');

  const msg = $('msg');
  const statusText = $('statusText');
  const counter = $('counter');

  const card = $('card');
  const maru = $('maru');

  let list = [];
  let idx = 0;

  // 日付整形（MySQL DATE: YYYY-MM-DD / DATETIME）
  function formatDate(v) {
    if (!v) return '—';
    const d = new Date(v);
    if (isNaN(d.getTime())) return v; // 変換不能ならそのまま
    const y = d.getFullYear();
    const m = ('0' + (d.getMonth() + 1)).slice(-2);
    const day = ('0' + d.getDate()).slice(-2);
    return `${y}/${m}/${day}`;
  }

  /* ===== 初期ロード ===== */

  fetch('api/get_month.php').then(r=>r.json()).then(j=>{
  month.innerHTML = '<option value="">選択</option>' +
    (j.data || []).map(v=>`<option>${v}</option>`).join('');
  });

  fetch('api/get_kyoten.php').then(r=>r.json()).then(j=>{
    kyoten.innerHTML = '<option value="">選択</option>' +
      (j.data || []).map(v=>`<option>${v}</option>`).join('');
  });

  fetch('api/get_work_type.php').then(r=>r.json()).then(j=>{
    work.innerHTML = '<option value="">選択</option>' +
      (j.data || []).map(v=>`<option>${v}</option>`).join('');
  });

  kyoten.addEventListener('change', async ()=>{
    tanto.disabled = true;
    tanto.innerHTML = '<option>読込中...</option>';
    if (!kyoten.value) return;
    const r = await fetch('api/get_tanto.php?kyoten=' + encodeURIComponent(kyoten.value));
    const j = await r.json();
    tanto.disabled = false;
    tanto.innerHTML = '<option value="">選択</option>' +
      (j.data || []).map(v=>`<option>${v}</option>`).join('');
  });



  btnSearch.onclick = async () => {

    msg.textContent = '';
    if (!kyoten.value || !tanto.value || !month.value || !work.value) {
      msg.textContent = 'すべて選択してください';
    return;
    }

    statusText.textContent = '検索中...';
    card.classList.add('is-hidden');

    try {
      const qs = new URLSearchParams({
        kyoten: kyoten.value,
        tanto: tanto.value,
        month: month.value,
        work_type: work.value,
        show_done: showDone.checked ? 1 : 0
      });

      const r = await fetch('api/shaken_fetch.php?' + qs.toString());
      const j = await r.json();

      statusText.textContent = '';   // ← 必ず解除

      if (j.status !== 'ok') {
        statusText.textContent = '該当なし';
        return;
      }

      list = j.data || [];
      idx = 0;

      if (!list.length) {
        statusText.textContent = '該当なし';
        return;
      }

      statusText.textContent = '検索結果';
      render();

    } catch (e) {
      statusText.textContent = 'エラー';
      msg.textContent = '検索に失敗しました';
      console.error(e);
    }
  };

  function render() {
    const d = list[idx];
    card.classList.remove('is-hidden');
    counter.textContent = `${idx+1} / ${list.length}`;

    $('customer_name').textContent = d.customer_name || '—';

    $('mobile_tel').textContent = d.mobile_tel || '—';
    $('mobile_tel').href = d.mobile_tel ? `sms:${d.mobile_tel}` : '#';

    $('address').textContent = d.address || '—';
    $('target_month').textContent = d.target_month || '—';
    $('first_reg').textContent = d.first_reg || '—'; // 文字列
    $('next_shaken').textContent = d.next_shaken ? formatDate(d.next_shaken) : '—';

    $('car_name').textContent = d.car_name || '—';
    $('work_type').textContent = d.work_type || '—';
    $('shaken_count').textContent = (d.shaken_count ?? '—');
    $('chao_plan').textContent = d.chao_plan || '—';
    $('chao_target').textContent = d.chao_target || '—';
    $('zankure').textContent = d.zankure || '—';

    $('service_date').textContent =d.service_date && d.service_date !== '0000-00-00'
    ? formatDate(d.service_date)
    : '—';

    // ▼ 市場措置（赤表示）
    const marketEl = $('market_action');
    const marketVal = d.market_action || '';

    marketEl.textContent = marketVal || '—';

    // 「有」または「あり」のときだけ赤
    const hasMarketAction =
    marketVal === '有' || marketVal === 'あり';

    marketEl.classList.toggle('market-alert', hasMarketAction);

    maru.classList.toggle('is-hidden', Number(d.arrived) !== 1);
    btnArrived.disabled = Number(d.arrived) === 1;
    btnCancel.hidden = Number(d.arrived) !== 1;
  }

  // パラパラ演出（めくり）
  function next() {
    if (idx >= list.length - 1) return;
    card.classList.add('slide-next');
    setTimeout(() => {
      idx++;
      card.classList.remove('slide-next');
      render();
    }, 220);
  }
  function prev() {
    if (idx <= 0) return;
    card.classList.add('slide-prev');
    setTimeout(() => {
      idx--;
      card.classList.remove('slide-prev');
      render();
    }, 220);
  }

  btnNext.onclick = next;
  btnPrev.onclick = prev;

  btnArrived.onclick = async ()=>{
    const d = list[idx];
    if (Number(d.arrived) === 1) return;

    await fetch('api/shaken_arrived.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:'no=' + encodeURIComponent(d.no)
    });

    d.arrived = 1;
    render();
  };


  btnCancel.onclick = async () => {
    const d = list[idx];
    if (Number(d.arrived) !== 1) return;
    if (!confirm('予約をキャンセルしますか？')) return;

  const r = await fetch('./api/reserve_cancel.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'no=' + encodeURIComponent(d.no)
  });

  const j = await r.json();

  if (j.status !== 'ok' || Number(j.affected) !== 1) {
    alert('キャンセルに失敗しました（DB未更新）');
    return;
  }

  // ★ DB更新成功時のみ UI を更新
  d.arrived = 0;
  render();
  };




  // スワイプ
  let sx=null;
  card.addEventListener('touchstart',e=>sx=e.touches[0].clientX,{passive:true});
  card.addEventListener('touchend',e=>{
    if(sx===null)return;
    const dx=e.changedTouches[0].clientX-sx;
    sx=null;
    if(Math.abs(dx)>50) dx<0?next():prev();
  },{passive:true});

})();
function scrollToSearch(){
  document.querySelector('.panel').scrollIntoView({behavior:'smooth'});
}
</script>

</body>
</html>
