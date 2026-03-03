<?php
function q($v){ return "`{$v}`"; }

/* ===== 毎月ここだけ変更（6桁） ===== */
const TARGET_YM = '202603';   // ← ここだけ毎月変更

const COL_MAP = [
  'TABLE' => 'data_output_' . TARGET_YM,
  'NO' => 'No.',

  'KYOTEN_NAME' => '拠点名',
  'TANTO_NAME'  => '担当者名',
  'WORK_TYPE'   => '作業種別',

  'CUSTOMER_NAME' => '顧客名',
  'MOBILE_TEL'    => '携帯TEL',
  'ADDRESS'       => '自宅住所',
  'TARGET_MONTH'  => '対象月',

  'CAR_NAME'      => '車名',
  'FIRST_REG'     => '初度登録年月',
  'NEXT_SHAKEN'   => '次回車検日',
  'SHAKEN_COUNT'  => '車検回数',

  'CHAO_PLAN'   => '現チャオコース/プラン',
  'CHAO_TARGET' => 'チャオ対象',
  'ZANKURE'     => '残クレ',

  'SERVICE_DATE'  => 'サービス予約日',
  'MARKET_ACTION' => '市場措置有',

  'ARRIVED'    => 'arrived',
  'ARRIVED_AT' => 'arrived_at',
];
