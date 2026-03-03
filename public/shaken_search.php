<?php
// ===== 直アクセス防止（他ページ同様）
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}

// アクセスログ記録処理
$log_directory = '../logs';
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

file_put_contents($log_file, $log, FILE_APPEND);
// アクセスログ記録処理ここまで

?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>点検対象 パラパラ検索</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ★CSS（あなたの現行パスに合わせる） -->
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

    <!-- ※ 元コードに「コメントアウトしたbuttonの閉じタグだけ残る」崩れがあったので除去 -->
    <!-- <button type="button" class="btn btn--ghost" onclick="scrollToSearch()">↑ 検索条件へ戻る</button> -->
  </div>

  <!-- ▼ 検索条件 -->
  <p class="notice">
  ※ 車検:当月〜翌々月  12V:当月・翌月  安点/無6:当月分を検索・表示
  </p>
  <section class="panel">
    <div id="statusText" class="statusText">◎ 条件を選択してください</div>
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
        <select id="work_type"></select>
      </label>
    </div>

    <div class="actions search-actions">
      <label class="toggle">
        <input id="showDone" type="checkbox"> 対応済みも表示
      <button id="btnSearch" type="button" class="btn btn--primary">検索</button>
      </label>
    </div>

    <div id="msg" class="msg"></div>
  </section>

  <!-- ▼ パラパラ表示 -->
  <section class="viewer">
    <div class="status">
      <div id="counter" class="counter"></div>
    </div>

    <div class="nav card-nav is-hidden">
      <button id="btnPrev" type="button" class="btn">◀◀ 前へ</button>
      <button id="btnArrived" type="button" class="btn btn--ok">対応済み(入庫/予約/代替)</button>
      <button id="btnCancel" type="button" class="btn btn--ghost" hidden>対応済みキャンセル</button>
      <button id="btnNext" type="button" class="btn">次へ ▶▶</button>
    </div>
    
    <div id="card" class="card is-hidden">
      <div id="maru" class="maru is-hidden"></div>

      <div class="cardHeader">
        <div class="label" id="cardSubLabel">顧客名</div>
        <div id="customer_name" class="big"></div>
      </div>

      <div class="kv">
        <div class="k">携帯TEL</div>
        <div class="v"><a id="mobile_tel" class="tel" href="#">—</a></div>
      </div>

      <div id="contactActions" class="contact-actions is-hidden">
        <a id="btnCall" class="btn btn--call" href="#">📞 電話</a>
        <a id="btnSms"  class="btn btn--sms"  href="#">💬 SMS</a>
      </div>

      <div class="kv">
        <div class="k">自宅住所</div>
        <div id="address" class="v">—</div>
      </div>

      <div class="kv-grid">
        <div class="kv"><div class="k">対象月</div><div id="target_month" class="v">—</div></div>
        <div class="kv"><div class="k">作業種別</div><div id="work_type_view" class="v">—</div></div>
        <div class="kv"><div class="k">車名</div><div id="car_name" class="v">—</div></div>

        <div class="kv"><div class="k">初度登録年月</div><div id="first_reg" class="v">—</div></div>
        <div class="kv"><div class="k">次回車検日</div><div id="next_shaken" class="v">—</div></div>
        <div class="kv"><div class="k">車検回数</div><div id="shaken_count" class="v">—</div></div>

        <div class="kv"><div class="k">現チャオ</div><div id="chao_plan" class="v">—</div></div>
        <div class="kv"><div class="k">チャオ対象</div><div id="chao_target" class="v">—</div></div>
        <div class="kv"><div class="k">残クレ</div><div id="zankure" class="v">—</div></div>

        <div class="kv"><div class="k">サービス予約日</div><div id="service_date" class="v">—</div></div>

        <div class="kv market" id="market_box">
          <div class="k">市場措置</div>
          <div id="market_action" class="v">—</div>
        </div>
      </div>
      <div class="hint">iPad：左右スワイプでパラパラめくり</div>
    </div>
  </section>

</main>

<script>
(() => {
'use strict';

/* ================= DOM ================= */
const $ = id => document.getElementById(id);

const kyoten    = $('kyoten');
const tanto     = $('tanto');
const month     = $('month');
const work      = $('work_type');
const showDone  = $('showDone');

const btnSearch  = $('btnSearch');
const btnPrev    = $('btnPrev');
const btnNext    = $('btnNext');
const btnArrived = $('btnArrived');
const btnCancel  = $('btnCancel');

const statusEl  = $('statusText');
const msgEl     = $('msg');
const counterEl = $('counter');
const card      = $('card');
const maru      = $('maru');
const titleEl   = $('card-title');

const fields = {
  customer_name: $('customer_name'),
  mobile_tel:    $('mobile_tel'),
  address:       $('address'),
  target_month:  $('target_month'),
  work_type:     $('work_type_view'),
  car_name:      $('car_name'),
  first_reg:     $('first_reg'),
  next_shaken:   $('next_shaken'),
  shaken_count:  $('shaken_count'),
  chao_plan:     $('chao_plan'),
  chao_target:   $('chao_target'),
  zankure:       $('zankure'),
  service_date:  $('service_date'),
  market_action: $('market_action'),
};

/* ================= State ================= */
let list = [];
let idx  = 0;
let loading = false;

/* ================= UI helpers ================= */
const setStatus = s => statusEl && (statusEl.textContent = s || '');
const setMsg    = s => msgEl && (msgEl.textContent = s || '');

function setCounter() {
  if (!counterEl) return;
  counterEl.textContent = list.length ? `${idx + 1} / ${list.length}` : '';
}

// function showCard(show) {
//   if (!card) return;
//   card.classList.toggle('is-hidden', !show);
// }

// let cardNav = null;
const cardNav = document.querySelector('.card-nav');

// ===== 初期状態を明示（iPhone Safari 対策）=====
if (cardNav) cardNav.classList.add('is-hidden');
if (card) card.classList.add('is-hidden');


function showCard(show) {
  if (card) {
    card.classList.toggle('is-hidden', !show);
  }
  if (cardNav) {
    cardNav.classList.toggle('is-hidden', !show);
  }
}

function setText(el, v) {
  if (!el) return;
  el.textContent = (v !== null && v !== undefined && String(v) !== '') ? String(v) : '—';
}

/* ================= Robust value getter =================
   - 日本語/英語/大文字/キャメル/スネーク…の揺れを吸収
   - 候補キーが見つからないときは「ゆるく一致」も試す
*/
function normKey(k) {
  return String(k ?? '')
    .trim()
    .toLowerCase()
    .replace(/[\s_\-\.\/\\()（）【】\[\]{}:;,'"「」]/g, '');
}

function makeNormMap(d) {
  const m = new Map();
  if (!d || typeof d !== 'object') return m;
  for (const [k, v] of Object.entries(d)) {
    m.set(normKey(k), v);
  }
  return m;
}

function pick(d, keys) {
  if (!d || typeof d !== 'object') return '';
  // 1) 完全一致（まずは最速）
  for (const k of keys) {
    if (Object.prototype.hasOwnProperty.call(d, k)) return d[k];
  }
  // 2) ゆる一致（キー揺れ対策）
  const nm = makeNormMap(d);
  for (const k of keys) {
    const nk = normKey(k);
    if (nm.has(nk)) return nm.get(nk);
  }
  return '';
}

function isArrived(d) {
  return Number(pick(d, [
    'arrived','ARRIVED','入庫済','対応済み',
    'arrive','ARRIVE','done','DONE'
  ])) === 1;
}

function getNo(d) {
  const v = pick(d, ['no','NO','No.','管理番号','顧客番号','id','ID']);
  return (v !== null && v !== undefined) ? String(v) : '';
}

/* ================= fetch helpers ================= */
async function fetchJsonSafe(url, opts) {
  const r = await fetch(url, opts);
  const t = await r.text();

  if (!r.ok) {
    // 500等でも落とさず、内容を見える化
    throw new Error(`HTTP ${r.status} ${t.slice(0, 200)}`);
  }

  const trimmed = (t || '').trim();
  if (!trimmed) return {};

  try {
    return JSON.parse(trimmed);
  } catch (e) {
    // HTML混入でもここで止めない（ただし例外は投げる）
    throw new Error(`JSON parse failed: ${trimmed.slice(0, 120)}`);
  }
}

function normalizeList(j) {
  if (Array.isArray(j)) return j;
  if (j && Array.isArray(j.data)) return j.data;
  if (j && Array.isArray(j.rows)) return j.rows;
  if (j && Array.isArray(j.result)) return j.result;
  return [];
}

/* ================= Paging helpers ================= */
function goNextUnarrived(fromIdx) {
  for (let i = fromIdx + 1; i < list.length; i++) {
    if (!isArrived(list[i])) {
      idx = i;
      render();
      return true;
    }
  }
  return false;
}

function goPrevUnarrived(fromIdx) {
  for (let i = fromIdx - 1; i >= 0; i--) {
    if (!isArrived(list[i])) {
      idx = i;
      render();
      return true;
    }
  }
  return false;
}

/* ================= Render ================= */
function render() {
  if (!Array.isArray(list) || list.length === 0) {
    showCard(false);
    setCounter();
    return;
  }

  if (idx < 0) idx = 0;
  if (idx >= list.length) idx = list.length - 1;

  const d = list[idx];

  // 対応済みOFFなら arrived=1 は表示しない（次の未対応へ）
  if (isArrived(d) && !showDone.checked) {
    const moved = goNextUnarrived(idx);
    if (!moved) {
      showCard(false);
      setStatus('未処理はありません');
      setCounter();
    }
    return;
  }

  showCard(true);
  setCounter();

  // dataset
  if (btnArrived) btnArrived.dataset.no = getNo(d);
  if (btnCancel)  btnCancel.dataset.no  = getNo(d);

  // title（候補を多めに）
  const ky = pick(d, ['拠点名','kyoten','KYOTEN_NAME','kyoten_name']);
  const ta = pick(d, ['担当者名','tanto','TANTO_NAME','tanto_name']);
  const mo = pick(d, ['対象月','month','TARGET_MONTH','target_month']);
  const wt = pick(d, ['作業種別','work_type','WORK_TYPE']);
  if (titleEl) titleEl.textContent = `${ky} / ${ta} / ${mo} / ${wt}`;

  // body（揺れ吸収）
  setText(fields.customer_name, pick(d, ['顧客名','customer_name','customerName','name']));
  // setText(fields.mobile_tel,    pick(d, ['携帯TEL','mobile_tel','mobileTel','tel','phone']));
  setMobileTel(pick(d, ['携帯TEL','mobile_tel','mobileTel','tel','phone']));
  setText(fields.address,       pick(d, ['自宅住所','address','addr']));
  setText(fields.target_month,  pick(d, ['対象月','target_month','month']));
  setText(fields.work_type,     pick(d, ['作業種別','work_type']));
  setText(fields.car_name,      pick(d, ['車名','car_name']));
  setText(fields.first_reg,     pick(d, ['初度登録年月','first_reg','firstReg']));
  setText(fields.next_shaken,   pick(d, ['次回車検日','next_shaken','nextShaken']));
  setText(fields.shaken_count,  pick(d, ['車検回数','shaken_count','shakenCount']));
  setText(fields.chao_plan,     pick(d, ['現チャオコース/プラン','chao_plan','chaoPlan']));
  setText(fields.chao_target,   pick(d, ['チャオ対象','chao_target','chaoTarget']));
  setText(fields.zankure,       pick(d, ['残クレ','zankure']));
  // setText(fields.service_date,  pick(d, ['サービス予約日','service_date','serviceDate']));
  setDateText(fields.service_date,pick(d, ['サービス予約日','service_date','serviceDate']));
  setText(fields.market_action, pick(d, ['市場措置有','市場措置','market_action','MARKET_ACTION']));

  function setDateText(el, v) {
    if (!el) return;

    if (
      !v ||
      v === '0000-00-00' ||
      v === '0000-00-00 00:00:00'
    ) {
      el.textContent = '—';
      return;
    }
    el.textContent = v;
  }
  // ★ 市場措置「有」を赤太字にする
  const marketVal = pick(d, ['市場措置有','市場措置','market_action','MARKET_ACTION']);
  const marketBox = document.getElementById('market_box');

  if (marketBox) {
    marketBox.classList.toggle('market-danger', marketVal === '有');
  }

  document.querySelectorAll('.kv .v').forEach(v => {
  const val = v.textContent.trim();

  // ダッシュ系をまとめて判定
  const isEmptyDash = /^[\-\–\—\―\－]+$/.test(val);

  v.classList.toggle('is-empty', isEmptyDash);
  });

  // arrived UI
  // if (isArrived(d)) {
  //   if (maru) maru.classList.remove('is-hidden');
  //   if (btnArrived) btnArrived.disabled = true;
  //   if (btnCancel) btnCancel.hidden = false;
  // } else {
  //   if (maru) maru.classList.add('is-hidden');
  //   if (btnArrived) btnArrived.disabled = false;
  //   if (btnCancel) btnCancel.hidden = true;
  // }

  if (isArrived(d)) {
    if (maru) maru.classList.remove('is-hidden');

    if (btnArrived) btnArrived.hidden = true;
    if (btnCancel)  btnCancel.hidden  = false;

  } else {
    if (maru) maru.classList.add('is-hidden');

    if (btnArrived) btnArrived.hidden = false;
    if (btnCancel)  btnCancel.hidden  = true;
  }

  if (btnPrev) btnPrev.disabled = (idx <= 0);
  if (btnNext) btnNext.disabled = (idx >= list.length - 1);
}

  function slide(dir, moveFn) {
    if (!card) return;

    const cls = dir === 'next' ? 'slide-next' : 'slide-prev';
    card.classList.add(cls);

    setTimeout(() => {
      card.classList.remove(cls);
      moveFn();
    }, 120); // ← CSS transition より少し短く
  }

/* ================= Paging events ================= */
if (btnNext) {
  btnNext.onclick = () => {
    if (!list.length) return;

    slide('next', () => {

      if (showDone.checked) {
        idx++;
        render();
        showCard(true);   // ★ render後に表示を保証
        return;
      }

      const moved = goNextUnarrived(idx);

      if (!moved) {
        // ★ ここでは showCard(false) を呼ばない
        setStatus('未処理はありません');
        setCounter();
      } else {
        showCard(true);   // ★ 移動できたら必ず表示
      }

    });
  };
}


if (btnPrev) {
  btnPrev.onclick = () => {
    if (!list.length) return;

    slide('prev', () => {

      if (showDone.checked) {
        idx--;
        render();
        return;
      }

      goPrevUnarrived(idx);

    });
  };
}

/* ================= arrived（最終仕様） =================
   confirm YES: DB更新→成功したら次の未対応へ（今カードは消える）
   cancel: そのまま
*/
if (btnArrived) {
  btnArrived.onclick = async () => {
    const d = list[idx];
    if (!d) return;

    if (!confirm('対応済み(予約/入庫/代替)にしますか？')) return;

    // まずローカル状態更新
    d.arrived = 1;
    d.arrived_at = new Date().toISOString().slice(0,19).replace('T',' ');

    // DB更新（失敗したら戻す）
    try {
      await fetchJsonSafe('api/shaken_arrived.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'no=' + encodeURIComponent(getNo(d))
      });
    } catch(e) {
      console.error(e);
      alert('DB更新に失敗しました（このカードは残します）');
      d.arrived = 0;
      d.arrived_at = null;
      render();
      return;
    }

    // 成功 → 次の未対応へ（なければ終了表示）
    const moved = goNextUnarrived(idx);
    if (!moved) {
      showCard(false);
      setStatus('未処理はありません');
      setCounter();
    }
  };
}

/* ================= cancel（arrived=0） ================= */
if (btnCancel) {
  btnCancel.onclick = async () => {
    const d = list[idx];
    if (!d || !isArrived(d)) return;
    if (!confirm('対応済み(予約/入庫/代替)を取り消ししますか？')) return;

    // ローカル更新
    d.arrived = 0;
    d.arrived_at = null;

    try {
      await fetchJsonSafe('api/shaken_cancel.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'no=' + encodeURIComponent(getNo(d))
      });
    } catch(e) {
      console.error(e);
      alert('取り消しに失敗しました');
      // 失敗時は元に戻す
      d.arrived = 1;
      render();
      return;
    }

    render();
  };
}

/* ================= Search ================= */
if (btnSearch) {
  btnSearch.onclick = async () => {
    if (loading) return;
    loading = true;

    setMsg('');
    setStatus('検索中...');
    showCard(false);

    try {
      if (!kyoten?.value || !tanto?.value || !month?.value || !work?.value) {
        setMsg('すべて選択してください');
        setStatus('');
        return;
      }

      const qs = new URLSearchParams({
        kyoten: kyoten.value,
        tanto: tanto.value,
        month: month.value,
        work_type: work.value,
        show_done: (showDone && showDone.checked) ? '1' : '0'
      });

      const j = await fetchJsonSafe('api/shaken_fetch.php?' + qs.toString());
      list = normalizeList(j);
      idx = 0;

      if (!list.length) {
        setMsg('該当なし(または対象外の検索です）');
        setStatus('');          // 上のステータスは消す
        showCard(false);
        setCounter();
        return;
      }

      setStatus('検索結果');
      render();

    } catch(e) {
      console.error(e);
      setStatus('エラー');
      setMsg('検索に失敗しました（API/JSONを確認）');
    } finally {
      loading = false;
    }
  };
}

/* ================= Swipe (iPad / mobile) ================= */
  if (card) {
    let startX = null;
    let startY = null;

    card.addEventListener('touchstart', e => {
      if (e.touches.length !== 1) return;
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
    }, { passive: true });

    card.addEventListener('touchend', e => {
      if (startX === null) return;

      const dx = e.changedTouches[0].clientX - startX;
      const dy = e.changedTouches[0].clientY - startY;

      startX = startY = null;

      // 縦スクロールは無視
      if (Math.abs(dx) < 50 || Math.abs(dx) < Math.abs(dy)) return;

        // if (dx < 0) {
        //   btnNext && btnNext.click(); // ← ★ここ
        // } else {
        //   btnPrev && btnPrev.click(); // ← ★ここ
        // }

        if (dx < 0) {
          btnPrev && btnPrev.click(); // ← 左スワイプ → 前
        } else {
          btnNext && btnNext.click(); // → 右スワイプ → 次
        }
        
      }, { passive: true });
  }

/* ================= 初期選択肢ロード ================= */
async function loadOptions() {
  try {
    setMsg('');
    setStatus('');

    const jm = await fetchJsonSafe('api/get_month.php');
    const months = normalizeList(jm);
    if (month) {
      month.innerHTML = '<option value="">選択</option>' +
        months.map(v => `<option value="${v}">${v}</option>`).join('');
    }

    const jk = await fetchJsonSafe('api/get_kyoten.php');
    const kyotens = normalizeList(jk);
    if (kyoten) {
      kyoten.innerHTML = '<option value="">選択</option>' +
        kyotens.map(v => `<option value="${v}">${v}</option>`).join('');
    }

    const jw = await fetchJsonSafe('api/get_work_type.php');
    const works = normalizeList(jw);
    if (work) {
      work.innerHTML = '<option value="">選択</option>' +
        works.map(v => `<option value="${v}">${v}</option>`).join('');
    }

    if (tanto) {
      tanto.innerHTML = '<option value="">拠点を選択してください</option>';
      tanto.disabled = true;
    }

  } catch(e) {
    console.error(e);
    alert('初期データの取得に失敗しました（get_kyoten/get_month/get_work_type を確認）');
  }
}

if (kyoten && tanto) {
  kyoten.addEventListener('change', async () => {
    tanto.disabled = true;
    tanto.innerHTML = '<option>読込中...</option>';

    if (!kyoten.value) {
      tanto.innerHTML = '<option value="">拠点を選択してください</option>';
      return;
    }

    try {
      const jt = await fetchJsonSafe('api/get_tanto.php?kyoten=' + encodeURIComponent(kyoten.value));
      const tantos = normalizeList(jt);

      tanto.innerHTML = '<option value="">選択</option>' +
        tantos.map(v => `<option value="${v}">${v}</option>`).join('');
      tanto.disabled = false;

    } catch(e) {
      console.error(e);
      tanto.innerHTML = '<option value="">取得失敗</option>';
    }
  });
}

  // ▼ 携帯TELをSMSリンクに変換（本文なし）
  // function setMobileTel(tel) {
  //   const a = document.getElementById('mobile_tel');

  //   if (!tel) {
  //     a.textContent = '—';
  //     a.removeAttribute('href');
  //     return;
  //   }

  // ▼ 携帯TEL表示 + 電話 / SMS ボタン制御
  function setMobileTel(tel) {
    const telLink = document.getElementById('mobile_tel');
    const actions = document.getElementById('contactActions');
    const btnCall = document.getElementById('btnCall');
    const btnSms  = document.getElementById('btnSms');

  // TELなし
    if (!tel) {
      telLink.textContent = '—';
      telLink.removeAttribute('href');
      actions.classList.add('is-hidden');
      return;
    }

  // 表示用
    telLink.textContent = tel;

  // 数字だけ抽出
    const num = tel.replace(/[^0-9]/g, '');

  // 電話 / SMS
    btnCall.href = 'tel:' + num;
    btnSms.href  = 'sms:' + num;

  // スマホ幅のみ表示
    if (window.innerWidth <= 768) {
        actions.classList.remove('is-hidden');
      } else {
        actions.classList.add('is-hidden');
      }

  // 誤送信防止ワンクッション
      btnSms.onclick = e => {
        const ok = confirm('この番号にSMSを送信します。\nよろしいですか？');
        if (!ok) {
          e.preventDefault();   // SMS起動を止める
        }
      }; 
  }

  //   // 表示用（ハイフンあり）
  //   a.textContent = tel;

  //   // SMS用（数字のみ）
  //   const smsNumber = tel.replace(/[^0-9]/g, '');

  //   // SMS起動（iPhone / Android 共通）
  //   a.href = 'sms:' + smsNumber;
  // }

/* ================= Start ================= */
loadOptions();
showCard(false);

})();
</script>
