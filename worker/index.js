// FARM360 の Cloudflare Worker。
// 静的ファイル(dist/)の配信は Cloudflare が行い、ここでは /api/contact(お問い合わせの送信)だけを受け取る。
// メールは Cloudflare Email Routing で、登録済みの運営アドレス(MAIL_TO)にだけ送る。送信者への控えは送らない。

import { EmailMessage } from 'cloudflare:email';

const LIMITS = { send_name: 100, send_email: 255, send_message: 5000 };
const EMAIL_PATTERN = /^[^\s@<>"]+@[^\s@<>"]+\.[^\s@<>"]+$/;

export default {
  async fetch(request, env) {
    const url = new URL(request.url);

    if (url.pathname === '/api/contact') {
      if (request.method !== 'POST') {
        return json({ error: 'method_not_allowed' }, 405, { Allow: 'POST' });
      }
      return handleContact(request, env);
    }

    return env.ASSETS.fetch(request);
  },
};

async function handleContact(request, env) {
  let input;
  try {
    input = await request.json();
  } catch {
    return json({ error: 'invalid_json' }, 400);
  }

  const values = validate(input);
  if (!values) {
    return json({ error: 'validation' }, 422);
  }

  const human = await verifyTurnstile(input.turnstile_token, request.headers.get('CF-Connecting-IP'), env.TURNSTILE_SECRET);
  if (!human) {
    return json({ error: 'turnstile' }, 403);
  }

  const raw = buildMail({
    from: env.MAIL_FROM,
    to: env.MAIL_TO,
    replyTo: values.send_email,
    subject: `【FARM360】お問い合わせ(${values.send_name} 様)`,
    body: [
      'FARM360 のお問い合わせフォームから届きました。',
      'このメールに返信すると、お問い合わせした方に届きます。',
      '',
      '■お名前',
      values.send_name,
      '■メールアドレス',
      values.send_email,
      '',
      '■お問い合わせ内容',
      values.send_message,
    ].join('\n'),
  });

  try {
    await env.CONTACT_MAIL.send(new EmailMessage(env.MAIL_FROM, env.MAIL_TO, raw));
  } catch (error) {
    console.error('contact mail failed', error);
    return json({ error: 'send_failed' }, 502);
  }

  return json({ ok: true });
}

/** 入力を整えて返す。不正なら null。 */
export function validate(input) {
  if (!input || typeof input !== 'object') return null;

  const values = {};
  for (const [field, limit] of Object.entries(LIMITS)) {
    const value = typeof input[field] === 'string' ? input[field].trim() : '';
    if (value === '' || value.length > limit) return null;
    values[field] = value;
  }

  // 名前とメールアドレスはメールのヘッダーに入るので、改行を許さない
  if (/[\r\n]/.test(values.send_name) || /[\r\n]/.test(values.send_email)) return null;
  if (!EMAIL_PATTERN.test(values.send_email)) return null;

  values.send_message = values.send_message.replace(/\r\n?/g, '\n');
  return values;
}

async function verifyTurnstile(token, ip, secret) {
  if (!secret || typeof token !== 'string' || token === '') return false;

  const form = new FormData();
  form.append('secret', secret);
  form.append('response', token);
  if (ip) form.append('remoteip', ip);

  const response = await fetch('https://challenges.cloudflare.com/turnstile/v0/siteverify', { method: 'POST', body: form });
  if (!response.ok) return false;

  const result = await response.json();
  return result.success === true;
}

/** 日本語の本文を含むプレーンテキストのメール(MIME)を組み立てる。 */
export function buildMail({ from, to, replyTo, subject, body }) {
  const domain = from.split('@')[1];
  const headers = [
    `From: FARM360 <${from}>`,
    `To: ${to}`,
    `Reply-To: ${replyTo}`,
    `Subject: ${encodeHeader(subject)}`,
    `Date: ${new Date().toUTCString()}`,
    `Message-ID: <${crypto.randomUUID()}@${domain}>`,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
  ];
  const encodedBody = base64(body.replace(/\n/g, '\r\n')).replace(/.{1,76}/g, '$&\r\n');

  return `${headers.join('\r\n')}\r\n\r\n${encodedBody}`;
}

// 日本語のヘッダーは =?UTF-8?B?...?= に変える。1つの塊は75文字までなので、文字の途中で切らずに分ける
function encodeHeader(text) {
  const chunks = [];
  let current = '';
  for (const char of text) {
    if (new TextEncoder().encode(current + char).length > 45) {
      chunks.push(current);
      current = '';
    }
    current += char;
  }
  if (current !== '') chunks.push(current);

  return chunks.map((chunk) => `=?UTF-8?B?${base64(chunk)}?=`).join('\r\n ');
}

function base64(text) {
  const bytes = new TextEncoder().encode(text);
  let binary = '';
  for (const byte of bytes) binary += String.fromCharCode(byte);
  return btoa(binary);
}

function json(data, status = 200, headers = {}) {
  return new Response(JSON.stringify(data), {
    status,
    headers: { 'Content-Type': 'application/json; charset=utf-8', 'Cache-Control': 'no-store', ...headers },
  });
}
