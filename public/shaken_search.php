<?php
session_start();

/* ===== ログインチェック ===== */
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.html");
    exit();
}

/* ===== ログ ===== */
require_once __DIR__ . '/../log_access.php';
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>点検対象 パラパラ検索</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ★CSS -->
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
  </div>

  <!-- ▼ 検索条件 -->
  <p class="notice">
    ※ 車検:当月〜翌々月 12V:当月･翌月 安点/無6:当月分を検索・表示
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
      </label>

      <button id="btnSearch" type="button" class="btn btn--primary">検索</button>
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

      <!-- ★ 追加：JS側で使っているタイトル表示先 -->
      <div id="card-title" class="card-title"></div>

      <div class="kv">
        <div class="k">携帯TEL</div>
        <div class="v"><a id="mobile_tel" class="tel" href="#">—</a></div>
      </div>

      <div id="contactActions" class="contact-actions is-hidden">
        <a id="btnCall" class="btn btn--call" href="#">📞 電話</a>
        <a id="btnSms1"  class="btn btn--sms"  href="#">💬 SMS/案内</a>
        <a id="btnSms2"  class="btn btn--sms"  href="#">💬 SMS/HTC</a>
        <a id="btnLine1" class="btn btn--line" href="#" target="_blank" rel="noopener noreferrer">💬 LINE/案内</a>
        <a id="btnLine2" class="btn btn--line" href="#" target="_blank" rel="noopener noreferrer">💬 LINE/HTC</a>
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

const cardNav = document.querySelector('.card-nav');

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

const contactActions = $('contactActions');
const btnCall = $('btnCall');
const btnSms1  = $('btnSms1');
const btnSms2  = $('btnSms2');
const btnLine1 = $('btnLine1');
const btnLine2 = $('btnLine2');

/* ================= State ================= */
let list = [];
let idx = 0;
let loading = false;

/* ================= UI helpers ================= */
const setStatus = s => {
  if (statusEl) statusEl.textContent = s || '';
};

const setMsg = s => {
  if (msgEl) msgEl.textContent = s || '';
};

function setCounter() {
  if (!counterEl) return;
  counterEl.textContent = list.length ? `${idx + 1} / ${list.length}` : '';
}

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

function isMobileWidth() {
  return window.matchMedia('(max-width: 768px)').matches;
}

function updateContactActionsVisibility() {
  if (!contactActions) return;

  const telText = fields.mobile_tel ? (fields.mobile_tel.textContent || '').trim() : '';
  const hasTel = telText && telText !== '—';

  if (isMobileWidth() && hasTel) {
    contactActions.classList.remove('is-hidden');
  } else {
    contactActions.classList.add('is-hidden');
  }
}

/* ================= Robust value getter ================= */
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

  for (const k of keys) {
    if (Object.prototype.hasOwnProperty.call(d, k)) return d[k];
  }

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
    throw new Error(`HTTP ${r.status} ${t.slice(0, 200)}`);
  }

  const trimmed = (t || '').trim();
  if (!trimmed) return {};

  try {
    return JSON.parse(trimmed);
  } catch (e) {
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

/* ================= Contact helpers ================= */
function buildMessage(d) {

  const customerName = pick(d, ['顧客名','customer_name','customerName','name']);

  // ★ 空白対策
  const rawWorkType = String(pick(d, ['作業種別','work_type'])).trim();

  // 作業種別の変換
  const workMap = {
    '車検': '車検',
    '12V': '法定12カ月点検',
    '安全': '安全点検',
    '無６': '初回6カ月点検'
  };

  const workType = workMap[rawWorkType] || rawWorkType;

  const targetMonth = pick(d, ['対象月','month','target_month']);

  return `Honda Cars 北大阪です。快適にお車をご利用いただいておりますでしょうか？
${customerName ? customerName + ' 様' : ''}の定期点検時期が近づいてまいりました。

対象月は ${targetMonth}、点検項目は「${workType}」となっております。

安全快適にお車をご利用いただくための定期点検となりますので、ご都合の良い日程をお知らせください。
ご希望の日程をこのメッセージに返信いただくか、お電話でも承っております。
よろしくお願いいたします。`;
}

function buildMessage2(d) {
  const customerName = pick(d, ['顧客名','customer_name','customerName','name']);
  // ★ 空白対策
  const rawWorkType = String(pick(d, ['作業種別','work_type'])).trim();
  // 作業種別の変換
  const workMap = {
    '車検': '車検',
    '12V': '法定12カ月点検',
    '安全': '安全点検',
    '無６': '初回6カ月点検'
  };
  const workType = workMap[rawWorkType] || rawWorkType;
  const targetMonth = pick(d, ['対象月','month','target_month']);

  return `Honda Cars 北大阪です。快適にお車をご利用いただいておりますでしょうか？
${customerName ? customerName + ' 様' : ''}の定期点検時期が近づいてまいりました。

対象月は ${targetMonth}、点検項目は「${workType}」となっております。

安全快適にお車をご利用いただくための定期点検となります。HondaTotalCareアプリの右下「メンテナンス予約」からご都合の良い日程でご予約をお願いいたします。
操作がご不明の場合は、このメッセージに返信いただくか、お電話いただけますと幸いです。
よろしくお願いいたします。`;
}


function setMobileTel(tel, d = null) {

  const telLink = fields.mobile_tel;
  if (!telLink) return;

  /* ================= TELなし ================= */

  if (!tel) {
    telLink.textContent = '—';
    telLink.removeAttribute('href');

    if (btnCall)  btnCall.removeAttribute('href');
    if (btnSms1)  btnSms1.removeAttribute('href');
    if (btnSms2)  btnSms2.removeAttribute('href');
    if (btnLine1) btnLine1.removeAttribute('href');
    if (btnLine2) btnLine2.removeAttribute('href');
    updateContactActionsVisibility();
    return;
  }

  telLink.textContent = tel;

  const num = String(tel).replace(/[^0-9]/g, '');

  if (!num) {
    telLink.removeAttribute('href');
    updateContactActionsVisibility();
    return;
  }

  telLink.href = 'tel:' + num;

/* ================= メッセージ生成 ================= */

  const enc1 = encodeURIComponent(buildMessage(d || {}));
  const enc2 = encodeURIComponent(buildMessage2(d || {}));

/* ================= 電話 ================= */

if (btnCall) {
  btnCall.href = 'tel:' + num;
}

/* ================= SMS ================= */

if (btnSms1) btnSms1.href = 'sms:' + num + '?body=' + enc1;
if (btnSms2) btnSms2.href = 'sms:' + num + '?body=' + enc2;

/* ================= LINE ================= */

if (btnLine1) {
  btnLine1.onclick = () => {
    const ta = document.createElement('textarea');
    ta.value = buildMessage(d || {});
    ta.style.position = 'fixed'; ta.style.top = '0'; ta.style.left = '0';
    ta.style.width = '1px'; ta.style.height = '1px';
    ta.style.opacity = '0'; ta.style.fontSize = '16px';
    document.body.appendChild(ta);
    ta.focus(); ta.setSelectionRange(0, ta.value.length);
    try { document.execCommand('copy'); } catch(e) {}
    document.body.removeChild(ta);
    window.open('https://line.me/R/nv/chat', '_blank');
  };
}

if (btnLine2) {
  btnLine2.onclick = () => {
    const ta = document.createElement('textarea');
    ta.value = buildMessage2(d || {});
    ta.style.position = 'fixed'; ta.style.top = '0'; ta.style.left = '0';
    ta.style.width = '1px'; ta.style.height = '1px';
    ta.style.opacity = '0'; ta.style.fontSize = '16px';
    document.body.appendChild(ta);
    ta.focus(); ta.setSelectionRange(0, ta.value.length);
    try { document.execCommand('copy'); } catch(e) {}
    document.body.removeChild(ta);
    window.open('https://line.me/R/nv/chat', '_blank');
  };
}

  /* ================= ボタン表示制御 ================= */

  updateContactActionsVisibility();

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

  if (btnArrived) btnArrived.dataset.no = getNo(d);
  if (btnCancel)  btnCancel.dataset.no  = getNo(d);

  const ky = pick(d, ['拠点名','kyoten','KYOTEN_NAME','kyoten_name']);
  const ta = pick(d, ['担当者名','tanto','TANTO_NAME','tanto_name']);
  const mo = pick(d, ['対象月','month','TARGET_MONTH','target_month']);
  const wt = pick(d, ['作業種別','work_type','WORK_TYPE']);

  if (titleEl) {
    titleEl.textContent = `${ky} / ${ta} / ${mo} / ${wt}`;
  }

  const cname = pick(d, ['顧客名','customer_name','customerName','name']);
  setText(fields.customer_name, cname ? cname + ' 様' : '');
  setMobileTel(pick(d, ['携帯TEL','mobile_tel','mobileTel','tel','phone']), d);
  setText(fields.address,      pick(d, ['自宅住所','address','addr']));
  setText(fields.target_month, pick(d, ['対象月','target_month','month']));
  setText(fields.work_type,    pick(d, ['作業種別','work_type']));
  setText(fields.car_name,     pick(d, ['車名','car_name']));
  setText(fields.first_reg,    pick(d, ['初度登録年月','first_reg','firstReg']));
  setText(fields.next_shaken,  pick(d, ['次回車検日','next_shaken','nextShaken']));
  setText(fields.shaken_count, pick(d, ['車検回数','shaken_count','shakenCount']));
  setText(fields.chao_plan,    pick(d, ['現チャオコース/プラン','chao_plan','chaoPlan']));
  setText(fields.chao_target,  pick(d, ['チャオ対象','chao_target','chaoTarget']));
  setText(fields.zankure,      pick(d, ['残クレ','zankure']));
  setDateText(fields.service_date, pick(d, ['サービス予約日','service_date','serviceDate']));
  setText(fields.market_action, pick(d, ['市場措置有','市場措置','market_action','MARKET_ACTION']));

  const marketVal = pick(d, ['市場措置有','市場措置','market_action','MARKET_ACTION']);
  const marketBox = $('market_box');
  if (marketBox) {
    marketBox.classList.toggle('market-danger', marketVal === '有');
  }

  document.querySelectorAll('.kv .v').forEach(v => {
    const val = (v.textContent || '').trim();
    const isEmptyDash = /^[\-\–\—\―\－]+$/.test(val);
    v.classList.toggle('is-empty', isEmptyDash);
  });

  if (isArrived(d)) {
    if (maru) maru.classList.remove('is-hidden');

    if (btnArrived) btnArrived.hidden = true;
    if (btnCancel)  btnCancel.hidden = false;
  } else {
    if (maru) maru.classList.add('is-hidden');

    if (btnArrived) btnArrived.hidden = false;
    if (btnCancel)  btnCancel.hidden = true;
  }

  if (btnPrev) btnPrev.disabled = (idx <= 0);
  if (btnNext) btnNext.disabled = (idx >= list.length - 1);
}

/* ================= Slide ================= */
function slide(dir, moveFn) {
  if (!card) return;

  const cls = dir === 'next' ? 'slide-next' : 'slide-prev';
  card.classList.add(cls);

  setTimeout(() => {
    card.classList.remove(cls);
    moveFn();
  }, 120);
}

/* ================= Paging events ================= */
if (btnNext) {
  btnNext.onclick = () => {
    if (!list.length) return;

    slide('next', () => {
      if (showDone.checked) {
        idx++;
        render();
        showCard(true);
        return;
      }

      const moved = goNextUnarrived(idx);

      if (!moved) {
        setStatus('未処理はありません');
        setCounter();
      } else {
        showCard(true);
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

/* ================= arrived ================= */
if (btnArrived) {
  btnArrived.onclick = async () => {
    const d = list[idx];
    if (!d) return;

    if (!confirm('対応済み(予約/入庫/代替)にしますか？')) return;

    d.arrived = 1;
    d.arrived_at = new Date().toISOString().slice(0, 19).replace('T', ' ');

    try {
      await fetchJsonSafe('api/shaken_arrived.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'no=' + encodeURIComponent(getNo(d))
      });
    } catch (e) {
      console.error(e);
      alert('DB更新に失敗しました（このカードは残します）');
      d.arrived = 0;
      d.arrived_at = null;
      render();
      return;
    }

    const moved = goNextUnarrived(idx);
    if (!moved) {
      showCard(false);
      setStatus('未処理はありません');
      setCounter();
    }
  };
}

/* ================= cancel ================= */
if (btnCancel) {
  btnCancel.onclick = async () => {
    const d = list[idx];
    if (!d || !isArrived(d)) return;

    if (!confirm('対応済み(予約/入庫/代替)を取り消ししますか？')) return;

    d.arrived = 0;
    d.arrived_at = null;

    try {
      await fetchJsonSafe('api/shaken_cancel.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'no=' + encodeURIComponent(getNo(d))
      });
    } catch (e) {
      console.error(e);
      alert('取り消しに失敗しました');
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
        setStatus('');
        showCard(false);
        setCounter();
        return;
      }

      setStatus('検索結果');
      render();

    } catch (e) {
      console.error(e);
      setStatus('エラー');
      setMsg('検索に失敗しました（API/JSONを確認）');
    } finally {
      loading = false;
    }
  };
}

/* ================= Swipe ================= */
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

    startX = null;
    startY = null;

    if (Math.abs(dx) < 50 || Math.abs(dx) < Math.abs(dy)) return;

    // 現仕様を維持
    if (dx < 0) {
      btnPrev && btnPrev.click(); // 左スワイプ → 前
    } else {
      btnNext && btnNext.click(); // 右スワイプ → 次
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
      month.innerHTML =
        '<option value="">選択</option>' +
        months.map(v => `<option value="${v}">${v}</option>`).join('');
    }

    const jk = await fetchJsonSafe('api/get_kyoten.php');
    const kyotens = normalizeList(jk);
    if (kyoten) {
      kyoten.innerHTML =
        '<option value="">選択</option>' +
        kyotens.map(v => `<option value="${v}">${v}</option>`).join('');
    }

    const jw = await fetchJsonSafe('api/get_work_type.php');
    const works = normalizeList(jw);
    if (work) {
      work.innerHTML =
        '<option value="">選択</option>' +
        works.map(v => `<option value="${v}">${v}</option>`).join('');
    }

    if (tanto) {
      tanto.innerHTML = '<option value="">拠点を選択してください</option>';
      tanto.disabled = true;
    }

  } catch (e) {
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

      tanto.innerHTML =
        '<option value="">選択</option>' +
        tantos.map(v => `<option value="${v}">${v}</option>`).join('');
      tanto.disabled = false;

    } catch (e) {
      console.error(e);
      tanto.innerHTML = '<option value="">取得失敗</option>';
    }
  });
}

/* ================= リサイズ時：連絡ボタン再判定 ================= */
window.addEventListener('resize', () => {
  updateContactActionsVisibility();
});

/* ================= Start ================= */
loadOptions();
showCard(false);

})();
</script>

</body>
</html>